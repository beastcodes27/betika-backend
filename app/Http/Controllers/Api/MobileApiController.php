<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Betslip;
use App\Models\Bookmaker;
use App\Models\CommunityPoll;
use App\Models\FeaturedMatch;
use App\Models\HomeAd;
use App\Models\TipsterProfile;
use App\Models\VipSubscription;
use App\Models\SlipPurchase;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MobileApiController extends Controller
{
    /**
     * Home feed payload: Auto-scrolling ads, featured big game, community poll, and top tipsters.
     */
    public function home(Request $request)
    {
        // 3-second auto-scrolling ads board
        $ads = HomeAd::where('is_active', true)
            ->with('bookmaker')
            ->orderBy('order_index')
            ->get()
            ->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'subtitle' => $ad->subtitle,
                    'bannerUrl' => $ad->banner_url,
                    'promoCode' => $ad->promo_code,
                    'bookmaker' => $ad->bookmaker ? [
                        'name' => $ad->bookmaker->name,
                        'slug' => $ad->bookmaker->slug,
                        'logo' => $ad->bookmaker->logo_url,
                    ] : null,
                    'actionType' => $ad->action_type,
                    'displaySeconds' => $ad->display_seconds ?? 3,
                ];
            });

        // Active featured big match with odds
        $featuredMatch = FeaturedMatch::where('is_active', true)
            ->latest('kickoff_at')
            ->first();

        // Community voting poll
        $poll = CommunityPoll::where('is_active', true)
            ->latest()
            ->first();

        // Verified top tipsters
        $topTipsters = TipsterProfile::with('user')
            ->where('verification_status', 'verified')
            ->where('is_suspended', false)
            ->orderByDesc('win_rate')
            ->take(6)
            ->get()
            ->map(function ($tp) {
                return [
                    'id' => $tp->id,
                    'displayName' => $tp->display_name,
                    'avatarUrl' => $tp->avatar_url,
                    'specialty' => $tp->specialty,
                    'winRate' => (float) $tp->win_rate,
                    'followersCount' => $tp->followers_count,
                    'isVerified' => $tp->is_verified,
                ];
            });

        return response()->json([
            'status' => 'success',
            'currency' => 'Tshs',
            'data' => [
                'adsBoard' => [
                    'rotationIntervalMs' => 3000,
                    'banners' => $ads,
                ],
                'featuredMatch' => $featuredMatch,
                'communityPoll' => $poll,
                'topTipsters' => $topTipsters,
            ],
        ]);
    }

    /**
     * Betslips list: Hides booking codes and promo codes for VIP slips unless user is subscribed.
     */
    public function betslips(Request $request)
    {
        $userId = $request->header('X-User-Id') ?? $request->query('user_id');
        $hasVipAccess = false;

        if ($userId) {
            $hasVipAccess = VipSubscription::where('user_id', $userId)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->exists();
        }

        $query = Betslip::with(['tipster.user', 'bookmaker', 'selections'])
            ->where('status', 'active');

        if ($request->filled('bookmaker')) {
            $query->whereHas('bookmaker', function ($q) use ($request) {
                $q->where('slug', $request->bookmaker);
            });
        }

        if ($request->filled('vip_only')) {
            $query->where('is_vip', true);
        }

        $slips = $query->latest()->paginate(15);

        $slips->getCollection()->transform(function ($slip) use ($userId, $hasVipAccess) {
            // Check individual purchase if not full VIP subscriber
            $isUnlocked = !$slip->is_vip || $hasVipAccess;
            if (!$isUnlocked && $userId) {
                $isUnlocked = SlipPurchase::where('user_id', $userId)
                    ->where('betslip_id', $slip->id)
                    ->where('status', 'completed')
                    ->exists();
            }

            return [
                'id' => $slip->id,
                'title' => $slip->title,
                'tipster' => $slip->tipster ? [
                    'id' => $slip->tipster->id,
                    'name' => $slip->tipster->display_name,
                    'avatar' => $slip->tipster->avatar_url,
                    'winRate' => (float) $slip->tipster->win_rate,
                    'isVerified' => $slip->tipster->is_verified,
                ] : null,
                'bookmaker' => $slip->bookmaker ? [
                    'id' => $slip->bookmaker->id,
                    'name' => $slip->bookmaker->name,
                    'slug' => $slip->bookmaker->slug,
                    'logo' => $slip->bookmaker->logo_url,
                ] : null,
                'totalOdds' => (float) $slip->total_odds,
                'stakeAmount' => (float) $slip->stake_amount,
                'potentialWin' => (float) ($slip->stake_amount * $slip->total_odds),
                'currency' => 'Tshs',
                'isVip' => (bool) $slip->is_vip,
                'isUnlocked' => $isUnlocked,
                // Booking code / promo code is hidden if locked
                'bookingCode' => $isUnlocked ? $slip->booking_code : '******',
                'matchCount' => $slip->selections->count(),
                'selections' => $slip->selections->map(function ($s) {
                    return [
                        'homeTeam' => $s->home_team,
                        'awayTeam' => $s->away_team,
                        'league' => $s->league,
                        'prediction' => $s->prediction,
                        'odds' => (float) $s->odds,
                        'confidence' => $s->confidence,
                    ];
                }),
                'createdAt' => $slip->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $slips,
        ]);
    }

    /**
     * Unlock single VIP betslip using Tshs payment pass.
     */
    public function unlockSlip(Request $request, Betslip $betslip)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payment_provider' => 'required|in:mpesa,airtel,tigopesa,wallet',
            'phone' => 'required|string',
        ]);

        $unlockCost = 3000; // 3,000 Tshs per single VIP slip

        $purchase = SlipPurchase::create([
            'user_id' => $validated['user_id'],
            'betslip_id' => $betslip->id,
            'amount_paid' => $unlockCost,
            'currency' => 'Tshs',
            'payment_reference' => 'PASS_' . strtoupper(uniqid()),
            'status' => 'completed',
        ]);

        Transaction::create([
            'user_id' => $validated['user_id'],
            'amount' => $unlockCost,
            'currency' => 'Tshs',
            'type' => 'slip_unlock',
            'provider' => $validated['payment_provider'],
            'reference_id' => $purchase->payment_reference,
            'status' => 'completed',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Betslip unlocked successfully!',
            'data' => [
                'betslipId' => $betslip->id,
                'bookingCode' => $betslip->booking_code,
                'bookmaker' => $betslip->bookmaker ? $betslip->bookmaker->name : null,
            ],
        ]);
    }

    /**
     * Record ad click interaction.
     */
    public function trackAdClick(Request $request, HomeAd $homeAd)
    {
        $homeAd->increment('click_count');
        return response()->json(['status' => 'success']);
    }

    /**
     * Vote on community poll.
     */
    public function votePoll(Request $request, CommunityPoll $communityPoll)
    {
        $validated = $request->validate([
            'option_index' => 'required|integer',
        ]);

        $options = $communityPoll->options;
        $idx = $validated['option_index'];

        if (!isset($options[$idx])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid option selected'], 422);
        }

        $options[$idx]['votes'] = ($options[$idx]['votes'] ?? 0) + 1;
        $communityPoll->options = $options;
        $communityPoll->increment('total_votes');
        $communityPoll->save();

        return response()->json([
            'status' => 'success',
            'data' => $communityPoll,
        ]);
    }
}
