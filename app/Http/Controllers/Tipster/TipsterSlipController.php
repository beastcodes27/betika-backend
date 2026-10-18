<?php

namespace App\Http\Controllers\Tipster;

use App\Http\Controllers\Controller;
use App\Models\Betslip;
use App\Models\Bookmaker;
use App\Models\MatchSelection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TipsterSlipController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $profile = Auth::user()->tipsterProfile;

        if (! $profile || ! $profile->can_publish) {
            return redirect()->route('tipster.dashboard')
                ->with('error', 'Your publishing privileges are currently pending Super Admin approval.');
        }

        $bookmakers = Bookmaker::where('is_active', true)->orderBy('sort_order')->get();

        return view('tipster.create', compact('bookmakers', 'profile'));
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = Auth::user()->tipsterProfile;

        if (! $profile || ! $profile->can_publish) {
            return redirect()->route('tipster.dashboard')->with('error', 'Publishing disabled.');
        }

        $validated = $request->validate([
            'bookmaker_id' => ['required', 'exists:bookmakers,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:Banker,High Odds,Goals,Combo,VIP'],
            'booking_code' => ['required', 'string', 'max:50'],
            'total_odds' => ['required', 'numeric', 'min:1.01'],
            'validity_time' => ['required', 'string', 'max:100'],
            'is_free' => ['boolean'],
            'amount' => ['nullable', 'string', 'max:50'],
            'confidence' => ['required', 'integer', 'between:50,100'],
            'matches' => ['required', 'array', 'min:1'],
            'matches.*.match' => ['required', 'string'],
            'matches.*.league' => ['required', 'string'],
            'matches.*.kickoff' => ['required', 'string'],
            'matches.*.pick' => ['required', 'string'],
            'matches.*.odds' => ['required', 'numeric', 'min:1.01'],
        ]);

        $isFree = $request->boolean('is_free');
        $amount = $isFree ? 'Free' : ($validated['amount'] ?: 'Tshs 2,000');

        $slug = Str::slug($validated['title']) . '-' . Str::random(5);

        $slip = Betslip::create([
            'tipster_id' => $profile->id,
            'bookmaker_id' => $validated['bookmaker_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'],
            'booking_code' => strtoupper($validated['booking_code']),
            'total_odds' => $validated['total_odds'],
            'matches_count' => count($validated['matches']),
            'validity_time' => $validated['validity_time'],
            'amount' => $amount,
            'is_free' => $isFree,
            'confidence' => $validated['confidence'],
            'status' => 'active',
        ]);

        foreach ($validated['matches'] as $idx => $m) {
            MatchSelection::create([
                'betslip_id' => $slip->id,
                'match' => $m['match'],
                'league' => $m['league'],
                'kickoff' => $m['kickoff'],
                'pick' => $m['pick'],
                'odds' => $m['odds'],
                'confidence' => $validated['confidence'],
                'sort_order' => $idx + 1,
            ]);
        }

        return redirect()->route('tipster.dashboard')
            ->with('success', "Betslip '{$slip->title}' published successfully with code {$slip->booking_code}!");
    }

    public function destroy(Betslip $slip): RedirectResponse
    {
        $profile = Auth::user()->tipsterProfile;

        if ($slip->tipster_id !== $profile?->id && ! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $slip->delete();

        return back()->with('success', 'Betslip removed.');
    }
}
