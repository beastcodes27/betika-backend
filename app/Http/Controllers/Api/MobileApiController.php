<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Betslip;
use App\Models\Bookmaker;
use App\Models\CommunityPoll;
use App\Models\CommunityPollVote;
use App\Models\FeaturedMatch;
use App\Models\HomeAd;
use App\Models\PurchasedSlip;
use App\Models\TipsterProfile;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserVipSubscription;
use App\Models\VipPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MobileApiController extends Controller
{
    private const CURRENCY = 'Tshs';

    /**
     * Home feed: ad rotator, featured match, live community poll and top tipsters.
     */
    public function home(Request $request): JsonResponse
    {
        $ads = HomeAd::query()
            ->where('is_active', true)
            ->with('bookmaker')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (HomeAd $ad): array => [
                'id' => $ad->id,
                'badge' => $ad->badge,
                'title' => $ad->title,
                'subtitle' => $ad->subtitle,
                'highlight' => $ad->highlight,
                'ctaText' => $ad->cta_text,
                'promoCode' => $ad->promo_code,
                'targetUrl' => $ad->target_url,
                'bookmaker' => $this->bookmakerPayload($ad->bookmaker),
                'displaySeconds' => $ad->scroll_duration_seconds > 0 ? $ad->scroll_duration_seconds : 3,
            ]);

        $featuredMatch = FeaturedMatch::query()
            ->where('is_active', true)
            ->with('bookmaker')
            ->latest('id')
            ->first();

        $poll = $featuredMatch
            ? CommunityPoll::query()
                ->where('featured_match_id', $featuredMatch->id)
                ->where('is_live', true)
                ->latest('id')
                ->first()
            : null;

        $topTipsters = TipsterProfile::query()
            ->where('is_verified', true)
            ->withCount(['activeBetslips as active_slips_count'])
            ->orderByDesc('win_rate')
            ->take(10)
            ->get()
            ->map(fn (TipsterProfile $tipster): array => $this->tipsterPayload($tipster));

        return response()->json([
            'status' => 'success',
            'currency' => self::CURRENCY,
            'data' => [
                'adsBoard' => [
                    'rotationIntervalMs' => 3000,
                    'banners' => $ads,
                ],
                'featuredMatch' => $featuredMatch ? $this->featuredMatchPayload($featuredMatch) : null,
                'communityPoll' => $poll ? $this->pollPayload($poll) : null,
                'topTipsters' => $topTipsters,
            ],
        ]);
    }

    /**
     * Active betslips with VIP booking codes hidden until unlocked.
     */
    public function betslips(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);
        $hasVipAccess = $this->hasActiveVip($userId);

        $query = Betslip::query()
            ->where('status', 'active')
            ->with([
                'tipster' => fn ($builder) => $builder->withCount(['activeBetslips as active_slips_count']),
                'bookmaker',
                'selections',
            ]);

        if ($request->filled('bookmaker')) {
            $query->whereHas('bookmaker', fn ($builder) => $builder->where('slug', $request->string('bookmaker')));
        }

        if ($request->boolean('vip_only')) {
            $query->where('is_free', false);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        $slips = $query->latest('id')->get()->map(
            fn (Betslip $slip): array => $this->betslipPayload(
                $slip,
                $this->isSlipUnlocked($slip, $userId, $hasVipAccess),
            )
        );

        return response()->json([
            'status' => 'success',
            'currency' => self::CURRENCY,
            'data' => $slips,
        ]);
    }

    /**
     * Verified tipsters with their latest active slip.
     */
    public function tipsters(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);
        $hasVipAccess = $this->hasActiveVip($userId);

        $tipsters = TipsterProfile::query()
            ->where('is_verified', true)
            ->withCount(['activeBetslips as active_slips_count'])
            ->with([
                'activeBetslips' => fn ($query) => $query
                    ->with([
                        'tipster' => fn ($builder) => $builder->withCount(['activeBetslips as active_slips_count']),
                        'bookmaker',
                        'selections',
                    ])
                    ->latest('id'),
            ])
            ->orderByDesc('win_rate')
            ->get()
            ->map(function (TipsterProfile $tipster) use ($userId, $hasVipAccess): array {
                $latestSlip = $tipster->activeBetslips->first();

                return $this->tipsterPayload(
                    $tipster,
                    $latestSlip
                        ? $this->betslipPayload(
                            $latestSlip,
                            $this->isSlipUnlocked($latestSlip, $userId, $hasVipAccess),
                        )
                        : null,
                );
            });

        return response()->json([
            'status' => 'success',
            'data' => $tipsters,
        ]);
    }

    /**
     * Unlock a single VIP betslip and reveal its booking code.
     */
    public function unlockSlip(Request $request, Betslip $betslip): JsonResponse
    {
        $user = $this->resolveUser($request);

        if ($betslip->is_free) {
            return response()->json([
                'status' => 'success',
                'message' => 'This betslip is already free to use.',
                'data' => [
                    'betslipId' => $betslip->id,
                    'bookingCode' => $betslip->booking_code,
                    'bookmaker' => $betslip->bookmaker?->name,
                ],
            ]);
        }

        $validated = $request->validate([
            'payment_provider' => 'sometimes|in:mpesa,airtel,tigopesa,wallet',
            'phone' => 'sometimes|string|max:25',
        ]);

        $purchasedSlip = PurchasedSlip::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'betslip_id' => $betslip->id,
            ],
            [
                'amount_paid' => $betslip->amount,
                'booking_code_unlocked' => $betslip->booking_code,
                'status' => 'active',
                'purchased_at' => now(),
            ],
        );

        if ($purchasedSlip->wasRecentlyCreated) {
            $provider = $validated['payment_provider'] ?? 'mpesa';

            Transaction::query()->create([
                'reference' => 'PASS-'.strtoupper(uniqid()),
                'user_id' => $user->id,
                'type' => 'slip_purchase',
                'description' => 'Unlocked betslip: '.$betslip->title,
                'method' => $this->paymentMethodLabel($provider),
                'phone_number' => $validated['phone'] ?? $user->phone,
                'amount' => $this->parseAmount($betslip->amount),
                'currency' => self::CURRENCY,
                'status' => 'completed',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Betslip unlocked successfully!',
            'data' => [
                'betslipId' => $betslip->id,
                'bookingCode' => $betslip->booking_code,
                'bookmaker' => $betslip->bookmaker?->name,
            ],
        ]);
    }

    /**
     * Subscribe the mobile user to a VIP plan.
     */
    public function subscribeVip(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_slug' => 'required|string|exists:vip_plans,slug',
            'payment_provider' => 'sometimes|in:mpesa,airtel,tigopesa,wallet',
            'phone' => 'sometimes|string|max:25',
        ]);

        $user = $this->resolveUser($request);
        $plan = VipPlan::query()->where('slug', $validated['plan_slug'])->firstOrFail();

        $subscription = UserVipSubscription::query()->create([
            'user_id' => $user->id,
            'vip_plan_id' => $plan->id,
            'started_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
            'amount_paid' => $plan->price,
            'status' => 'active',
        ]);

        Transaction::query()->create([
            'reference' => 'VIP-'.strtoupper(uniqid()),
            'user_id' => $user->id,
            'type' => 'subscription',
            'description' => 'Subscribed to '.$plan->name,
            'method' => $this->paymentMethodLabel($validated['payment_provider'] ?? 'mpesa'),
            'phone_number' => $validated['phone'] ?? $user->phone,
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'status' => 'completed',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'VIP access activated successfully!',
            'data' => [
                'planSlug' => $plan->slug,
                'planName' => $plan->name,
                'expiresAt' => $subscription->expires_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Complete mobile profile: wallet, purchased slips, transactions and VIP state.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        $purchasedSlips = PurchasedSlip::query()
            ->where('user_id', $user->id)
            ->with(['betslip.tipster', 'betslip.bookmaker', 'betslip.selections'])
            ->latest('purchased_at')
            ->get();

        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->get();

        $activeVip = UserVipSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->with('plan')
            ->latest('id')
            ->first();

        $totalSpent = $transactions
            ->whereIn('type', ['slip_purchase', 'subscription'])
            ->sum(fn (Transaction $transaction): float => (float) $transaction->amount);

        $totalDeposited = $transactions
            ->where('type', 'deposit')
            ->sum(fn (Transaction $transaction): float => (float) $transaction->amount);

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatarUrl' => $user->avatar,
                    'walletBalance' => (float) $user->wallet_balance,
                    'role' => $user->role,
                ],
                'stats' => [
                    'purchasedSlips' => $purchasedSlips->count(),
                    'activeSlips' => $purchasedSlips->where('status', 'active')->count(),
                    'wonSlips' => $purchasedSlips->where('status', 'won')->count(),
                    'totalSpent' => (float) $totalSpent,
                    'totalDeposited' => (float) $totalDeposited,
                ],
                'vip' => $activeVip ? [
                    'isActive' => true,
                    'planName' => $activeVip->plan?->name,
                    'expiresAt' => $activeVip->expires_at->toIso8601String(),
                ] : [
                    'isActive' => false,
                    'planName' => null,
                    'expiresAt' => null,
                ],
                'purchasedSlips' => $purchasedSlips->map(
                    fn (PurchasedSlip $purchase): array => $this->purchasedSlipPayload($purchase)
                ),
                'transactions' => $transactions->map(
                    fn (Transaction $transaction): array => $this->transactionPayload($transaction)
                ),
            ],
        ]);
    }

    /**
     * Available VIP plans for the mobile paywall.
     */
    public function vipPlans(): JsonResponse
    {
        $plans = VipPlan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(fn (VipPlan $plan): array => [
                'id' => $plan->slug,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'currency' => $plan->currency,
                'durationDays' => $plan->duration_days,
                'description' => $plan->description,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $plans,
        ]);
    }

    /**
     * Record an ad banner click.
     */
    public function trackAdClick(HomeAd $homeAd): JsonResponse
    {
        $homeAd->increment('clicks_count');

        return response()->json([
            'status' => 'success',
            'data' => [
                'adId' => $homeAd->id,
                'clicksCount' => $homeAd->clicks_count,
            ],
        ]);
    }

    /**
     * Cast a community prediction vote.
     */
    public function votePoll(Request $request, CommunityPoll $communityPoll): JsonResponse
    {
        $validated = $request->validate([
            'choice' => 'required|in:1,X,2',
        ]);

        $column = match ($validated['choice']) {
            '1' => 'home_votes_count',
            'X' => 'draw_votes_count',
            '2' => 'away_votes_count',
        };

        $communityPoll->increment($column);
        $communityPoll->increment('total_votes_count');
        $communityPoll->refresh();

        $userId = $this->resolveUserId($request);

        CommunityPollVote::query()->create([
            'featured_match_id' => $communityPoll->featured_match_id,
            'user_id' => $userId,
            'device_id' => $request->header('X-Device-Id'),
            'vote_choice' => $validated['choice'],
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $this->pollPayload($communityPoll),
        ]);
    }

    /**
     * Upload the signed-in mobile user's profile picture.
     */
    public function uploadProfileAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $user = $this->resolveUser($request);
        $file = $request->file('avatar');
        $fileName = 'user_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
        $uploadDir = public_path('uploads/avatars');

        if (! File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $this->deleteUploadedAvatar($user->avatar);

        $file->move($uploadDir, $fileName);
        $relativePath = 'uploads/avatars/'.$fileName;
        $user->update(['avatar' => $request->getSchemeAndHttpHost().'/'.$relativePath]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile picture uploaded successfully!',
            'data' => [
                'userId' => $user->id,
                'avatarUrl' => $user->avatar,
            ],
        ]);
    }

    /**
     * Remove the signed-in mobile user's custom profile picture.
     */
    public function removeProfileAvatar(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        $this->deleteUploadedAvatar($user->avatar);
        $user->update(['avatar' => null]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile picture removed.',
            'data' => [
                'userId' => $user->id,
                'avatarUrl' => null,
            ],
        ]);
    }

    /**
     * Upload a tipster profile picture from the mobile client.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'tipster_id' => 'required|exists:tipster_profiles,id',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $tipster = TipsterProfile::query()->findOrFail($request->integer('tipster_id'));
        $file = $request->file('avatar');
        $fileName = 'mobile_tipster_'.$tipster->id.'_'.time().'.'.$file->getClientOriginalExtension();
        $uploadDir = public_path('uploads/avatars');

        if (! File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $this->deleteUploadedAvatar($tipster->avatar_url);

        $file->move($uploadDir, $fileName);
        $relativePath = 'uploads/avatars/'.$fileName;

        $tipster->update([
            'avatar_url' => $request->getSchemeAndHttpHost().'/'.$relativePath,
            'avatar_path' => $relativePath,
            'avatar_mime' => $file->getClientMimeType() ?? ('image/'.$file->getClientOriginalExtension()),
            'avatar_uploaded_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile picture uploaded successfully!',
            'data' => [
                'tipsterId' => $tipster->id,
                'avatarUrl' => $tipster->avatar_url,
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function featuredMatchPayload(FeaturedMatch $match): array
    {
        return [
            'id' => $match->id,
            'league' => $match->league,
            'kickoff' => $match->kickoff,
            'venue' => $match->venue,
            'tipsterConsensus' => $match->tipster_consensus,
            'isActive' => (bool) $match->is_active,
            'bookmaker' => $this->bookmakerPayload($match->bookmaker),
            'homeTeam' => [
                'name' => $match->home_team_name,
                'code' => $match->home_team_code,
                'logo' => $match->home_team_logo,
                'color' => $match->home_team_color,
                'form' => $match->home_team_form,
                'odds' => (float) $match->home_odds,
            ],
            'awayTeam' => [
                'name' => $match->away_team_name,
                'code' => $match->away_team_code,
                'logo' => $match->away_team_logo,
                'color' => $match->away_team_color,
                'form' => $match->away_team_form,
                'odds' => (float) $match->away_odds,
            ],
            'draw' => [
                'odds' => (float) $match->draw_odds,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function pollPayload(CommunityPoll $poll): array
    {
        return [
            'id' => $poll->id,
            'featuredMatchId' => $poll->featured_match_id,
            'isLive' => (bool) $poll->is_live,
            'homeVotes' => (int) $poll->home_votes_count,
            'drawVotes' => (int) $poll->draw_votes_count,
            'awayVotes' => (int) $poll->away_votes_count,
            'totalVotes' => (int) $poll->total_votes_count,
            'homePercentage' => $poll->pct_home,
            'drawPercentage' => $poll->pct_draw,
            'awayPercentage' => $poll->pct_away,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function betslipPayload(Betslip $slip, bool $isUnlocked): array
    {
        return [
            'id' => $slip->id,
            'title' => $slip->title,
            'category' => $slip->category,
            'tipster' => $slip->tipster ? $this->tipsterPayload($slip->tipster) : null,
            'bookmaker' => $this->bookmakerPayload($slip->bookmaker),
            'totalOdds' => (float) $slip->total_odds,
            'matchesCount' => (int) $slip->matches_count,
            'validityTime' => $slip->validity_time,
            'amount' => $slip->amount,
            'isFree' => (bool) $slip->is_free,
            'isVip' => ! (bool) $slip->is_free,
            'isUnlocked' => $isUnlocked,
            'confidence' => (int) $slip->confidence,
            'bookingCode' => $isUnlocked ? $slip->booking_code : '******',
            'selections' => $slip->selections->map(fn ($selection): array => [
                'id' => $selection->id,
                'match' => $selection->match,
                'league' => $selection->league,
                'kickoff' => $selection->kickoff,
                'pick' => $selection->pick,
                'odds' => (float) $selection->odds,
                'confidence' => (int) $selection->confidence,
            ])->values(),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $latestSlip
     * @return array<string, mixed>
     */
    private function tipsterPayload(TipsterProfile $tipster, ?array $latestSlip = null): array
    {
        return [
            'id' => $tipster->id,
            'displayName' => $tipster->display_name,
            'handle' => $tipster->handle,
            'avatarUrl' => $tipster->avatar_url,
            'avatarBg' => $tipster->avatar_bg,
            'specialty' => $tipster->specialty,
            'winRate' => (float) $tipster->win_rate,
            'followersCount' => (int) $tipster->followers_count,
            'isVerified' => (bool) $tipster->is_verified,
            'badge' => $tipster->badge,
            'streak' => $tipster->streak,
            'activeSlipsCount' => (int) ($tipster->active_slips_count ?? $tipster->activeBetslips->count()),
            'latestSlip' => $latestSlip,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function bookmakerPayload(?Bookmaker $bookmaker): ?array
    {
        if (! $bookmaker) {
            return null;
        }

        return [
            'id' => $bookmaker->id,
            'name' => $bookmaker->name,
            'slug' => $bookmaker->slug,
            'logoUrl' => $bookmaker->logo_path ? asset($bookmaker->logo_path) : null,
            'brandColor' => $bookmaker->brand_color,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function purchasedSlipPayload(PurchasedSlip $purchase): array
    {
        $slip = $purchase->betslip;
        $tipster = $slip?->tipster;

        return [
            'id' => $purchase->id,
            'betslipId' => $purchase->betslip_id,
            'title' => $slip?->title ?? 'Betslip',
            'tipsterName' => $tipster?->display_name ?? 'Tipster',
            'tipsterHandle' => $tipster?->handle ?? '',
            'tipsterAvatarUrl' => $tipster?->avatar_url,
            'tipsterAvatarBg' => $tipster?->avatar_bg,
            'bookmaker' => $slip?->bookmaker?->name ?? '',
            'bookmakerSlug' => $slip?->bookmaker?->slug ?? '',
            'bookingCode' => $purchase->booking_code_unlocked,
            'totalOdds' => (float) ($slip?->total_odds ?? 0),
            'matchesCount' => (int) ($slip?->matches_count ?? 0),
            'purchaseDate' => $purchase->purchased_at?->toIso8601String(),
            'amountPaid' => $purchase->amount_paid,
            'status' => $purchase->status,
            'validUntil' => $slip?->validity_time,
            'selections' => $slip?->selections->map(fn ($selection): array => [
                'id' => $selection->id,
                'match' => $selection->match,
                'league' => $selection->league,
                'kickoff' => $selection->kickoff,
                'pick' => $selection->pick,
                'odds' => (float) $selection->odds,
                'confidence' => (int) $selection->confidence,
            ])->values() ?? [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transactionPayload(Transaction $transaction): array
    {
        $isCredit = $transaction->type === 'deposit';

        return [
            'id' => $transaction->id,
            'reference' => $transaction->reference,
            'type' => $transaction->type,
            'description' => $transaction->description,
            'method' => $transaction->method,
            'amount' => $isCredit ? (float) $transaction->amount : -(float) $transaction->amount,
            'currency' => $transaction->currency,
            'date' => $transaction->created_at?->toIso8601String(),
            'status' => $transaction->status,
        ];
    }

    private function isSlipUnlocked(?Betslip $slip, ?int $userId, bool $hasVipAccess): bool
    {
        if (! $slip || $slip->is_free) {
            return true;
        }

        if ($hasVipAccess) {
            return true;
        }

        if (! $userId) {
            return false;
        }

        return PurchasedSlip::query()
            ->where('user_id', $userId)
            ->where('betslip_id', $slip->id)
            ->exists();
    }

    private function hasActiveVip(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return UserVipSubscription::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();
    }

    private function resolveUser(Request $request): User
    {
        $userId = $this->resolveUserId($request);

        if ($userId) {
            return User::query()->findOrFail($userId);
        }

        $email = $request->input('email') ?? $request->query('email');

        if ($email) {
            return User::query()->where('email', $email)->firstOrFail();
        }

        return User::query()->where('role', 'user')->orderBy('id')->firstOrFail();
    }

    private function resolveUserId(Request $request): ?int
    {
        $userId = $request->header('X-User-Id')
            ?? $request->input('user_id')
            ?? $request->query('user_id');

        return $userId ? (int) $userId : null;
    }

    private function paymentMethodLabel(string $provider): string
    {
        return match ($provider) {
            'airtel' => 'Airtel Money',
            'tigopesa' => 'Tigo Pesa',
            'wallet' => 'Wallet',
            default => 'M-Pesa',
        };
    }

    private function parseAmount(string $amount): float
    {
        $numeric = (float) preg_replace('/[^0-9.]/', '', str_replace(',', '', $amount));

        return $numeric > 0 ? $numeric : 2000.0;
    }

    private function deleteUploadedAvatar(?string $avatarUrl): void
    {
        if (! $avatarUrl) {
            return;
        }

        $path = parse_url($avatarUrl, PHP_URL_PATH);

        if (! is_string($path)) {
            return;
        }

        $fullPath = public_path(ltrim($path, '/'));

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
