<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Betslip;
use App\Models\Bookmaker;
use Illuminate\Http\Request;

class BetslipAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Betslip::with(['tipster.user', 'bookmaker', 'selections']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vip')) {
            $query->where('is_vip', $request->vip === '1');
        }

        if ($request->filled('bookmaker_id')) {
            $query->where('bookmaker_id', $request->bookmaker_id);
        }

        $slips = $query->latest()->paginate(15);
        $bookmakers = Bookmaker::where('is_active', true)->get();

        return view('admin.betslips.index', compact('slips', 'bookmakers'));
    }

    public function updateStatus(Request $request, Betslip $betslip)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,won,lost,void,cancelled',
        ]);

        $betslip->update(['status' => $validated['status']]);

        // If tipster profile exists, update their won/total slips count
        if ($betslip->tipster) {
            $tipster = $betslip->tipster;
            $wonCount = Betslip::where('tipster_id', $tipster->id)->where('status', 'won')->count();
            $totalCount = Betslip::where('tipster_id', $tipster->id)->whereIn('status', ['won', 'lost'])->count();
            $winRate = $totalCount > 0 ? round(($wonCount / $totalCount) * 100, 1) : 0;
            
            $tipster->update([
                'win_rate' => $winRate,
            ]);
        }

        return back()->with('success', "Betslip #{$betslip->id} status updated to " . strtoupper($validated['status']));
    }

    public function toggleVip(Betslip $betslip)
    {
        $betslip->update(['is_vip' => !$betslip->is_vip]);
        $status = $betslip->is_vip ? 'VIP Exclusive' : 'Public';
        return back()->with('success', "Betslip #{$betslip->id} is now marked as {$status}.");
    }

    public function destroy(Betslip $betslip)
    {
        $betslip->delete();
        return back()->with('success', "Betslip #{$betslip->id} deleted successfully.");
    }
}
