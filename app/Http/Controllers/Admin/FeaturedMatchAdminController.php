<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedMatch;
use Illuminate\Http\Request;

class FeaturedMatchAdminController extends Controller
{
    public function index()
    {
        $matches = FeaturedMatch::latest('kickoff_at')->paginate(15);
        return view('admin.featured.index', compact('matches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'home_team' => 'required|string|max:100',
            'away_team' => 'required|string|max:100',
            'home_team_logo' => 'nullable|string',
            'away_team_logo' => 'nullable|string',
            'league_name' => 'required|string|max:100',
            'league_badge' => 'nullable|string',
            'venue' => 'nullable|string|max:150',
            'kickoff_at' => 'required|date',
            'home_win_odds' => 'nullable|numeric|min:1.01',
            'draw_odds' => 'nullable|numeric|min:1.01',
            'away_win_odds' => 'nullable|numeric|min:1.01',
            'expert_analysis' => 'nullable|string',
        ]);

        FeaturedMatch::create(array_merge($validated, [
            'is_active' => true,
        ]));

        return back()->with('success', "Featured big match '{$validated['home_team']} vs {$validated['away_team']}' added successfully!");
    }

    public function toggleActive(FeaturedMatch $match)
    {
        $match->update(['is_active' => !$match->is_active]);
        $status = $match->is_active ? 'Activated' : 'Deactivated';
        return back()->with('success', "Featured match #{$match->id} {$status}.");
    }

    public function destroy(FeaturedMatch $match)
    {
        $match->delete();
        return back()->with('success', "Featured match removed.");
    }
}
