<?php

namespace App\Http\Controllers\Tipster;

use App\Http\Controllers\Controller;
use App\Models\Betslip;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TipsterDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $profile = $user->tipsterProfile;

        $slips = Betslip::with(['bookmaker', 'selections'])
            ->where('tipster_id', $profile?->id)
            ->latest()
            ->paginate(10);

        $stats = [
            'total_slips' => Betslip::where('tipster_id', $profile?->id)->count(),
            'active_slips' => Betslip::where('tipster_id', $profile?->id)->where('status', 'active')->count(),
            'total_views' => Betslip::where('tipster_id', $profile?->id)->sum('views_count'),
            'total_copies' => Betslip::where('tipster_id', $profile?->id)->sum('copies_count'),
            'win_rate' => $profile->win_rate ?? 75.0,
            'streak' => $profile->streak ?? '1W',
            'can_publish' => $profile->can_publish ?? false,
        ];

        return view('tipster.dashboard', compact('profile', 'slips', 'stats'));
    }
}
