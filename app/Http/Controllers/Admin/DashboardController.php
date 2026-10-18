<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Betslip;
use App\Models\Bookmaker;
use App\Models\CommunityPoll;
use App\Models\FeaturedMatch;
use App\Models\HomeAd;
use App\Models\TipsterProfile;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserVipSubscription;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_tipsters' => TipsterProfile::count(),
            'pending_tipsters' => TipsterProfile::where('status', 'pending')->count(),
            'active_slips' => Betslip::where('status', 'active')->count(),
            'total_slips' => Betslip::count(),
            'active_ads' => HomeAd::where('is_active', true)->count(),
            'vip_subscriptions' => UserVipSubscription::where('status', 'active')->count(),
            'total_revenue_tshs' => Transaction::where('status', 'completed')->sum('amount'),
            'total_poll_votes' => CommunityPoll::sum('total_votes_count'),
        ];

        $recentSlips = Betslip::with(['tipster', 'bookmaker'])
            ->latest()
            ->take(6)
            ->get();

        $topTipsters = TipsterProfile::with('user')
            ->orderByDesc('win_rate')
            ->take(5)
            ->get();

        $bookmakers = Bookmaker::withCount('betslips')
            ->orderBy('sort_order')
            ->get();

        $featuredMatch = FeaturedMatch::with(['bookmaker', 'activePoll'])
            ->where('is_active', true)
            ->first();

        return view('admin.dashboard', compact(
            'stats',
            'recentSlips',
            'topTipsters',
            'bookmakers',
            'featuredMatch'
        ));
    }
}
