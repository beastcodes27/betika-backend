<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeAd;
use App\Models\Bookmaker;
use Illuminate\Http\Request;

class HomeAdAdminController extends Controller
{
    public function index()
    {
        $ads = HomeAd::with('bookmaker')->orderBy('order_index')->get();
        $bookmakers = Bookmaker::where('is_active', true)->get();
        return view('admin.ads.index', compact('ads', 'bookmakers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:255',
            'banner_url' => 'required|string',
            'promo_code' => 'nullable|string|max:50',
            'bookmaker_id' => 'nullable|exists:bookmakers,id',
            'action_type' => 'required|in:register,deposit,vip,betslip,external',
            'action_url' => 'nullable|string',
            'display_seconds' => 'required|integer|min:2|max:10',
            'order_index' => 'required|integer',
        ]);

        HomeAd::create(array_merge($validated, [
            'is_active' => true,
        ]));

        return back()->with('success', "Ad banner '{$validated['title']}' created for auto-scrolling board.");
    }

    public function toggleActive(HomeAd $ad)
    {
        $ad->update(['is_active' => !$ad->is_active]);
        $status = $ad->is_active ? 'Activated' : 'Paused';
        return back()->with('success', "Ad #{$ad->id} {$status}.");
    }

    public function destroy(HomeAd $ad)
    {
        $ad->delete();
        return back()->with('success', "Ad #{$ad->id} removed.");
    }
}
