<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VipSubscription;
use App\Models\SlipPurchase;
use App\Models\Transaction;
use Illuminate\Http\Request;

class VipAdminController extends Controller
{
    public function index()
    {
        $subscriptions = VipSubscription::with('user')->latest()->paginate(15);
        $purchases = SlipPurchase::with(['user', 'betslip'])->latest()->take(10)->get();

        $activeVipCount = VipSubscription::where('status', 'active')->where('expires_at', '>', now())->count();
        $totalSubscriptionRevenue = Transaction::where('type', 'subscription')->where('status', 'completed')->sum('amount');
        $totalSlipRevenue = Transaction::where('type', 'slip_unlock')->where('status', 'completed')->sum('amount');

        return view('admin.vip.index', compact(
            'subscriptions',
            'purchases',
            'activeVipCount',
            'totalSubscriptionRevenue',
            'totalSlipRevenue'
        ));
    }

    public function grantVip(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_type' => 'required|in:daily,weekly,monthly,vip_season',
            'amount' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
        ]);

        $startsAt = now();
        $expiresAt = now()->addDays($validated['duration_days']);

        $subscription = VipSubscription::create([
            'user_id' => $validated['user_id'],
            'plan_type' => $validated['plan_type'],
            'amount_paid' => $validated['amount'],
            'currency' => 'Tshs',
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'status' => 'active',
            'payment_reference' => 'ADMIN_GRANT_' . strtoupper(uniqid()),
        ]);

        // Record completed transaction
        Transaction::create([
            'user_id' => $validated['user_id'],
            'amount' => $validated['amount'],
            'currency' => 'Tshs',
            'type' => 'subscription',
            'provider' => 'admin_grant',
            'reference_id' => $subscription->payment_reference,
            'status' => 'completed',
            'metadata' => [
                'plan_type' => $validated['plan_type'],
                'granted_by' => auth()->id(),
            ],
        ]);

        return back()->with('success', "VIP Access granted to user until {$expiresAt->format('Y-m-d H:i')}.");
    }

    public function revokeVip(VipSubscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);
        return back()->with('success', "VIP subscription #{$subscription->id} revoked.");
    }
}
