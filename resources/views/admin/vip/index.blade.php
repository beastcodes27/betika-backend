@extends('layouts.admin')

@section('title', 'VIP Subscriptions & Passes')
@section('header', 'VIP Subscriptions & Slip Access Revenue')

@section('content')
<div class="space-y-6">
    <!-- Revenue Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-5 shadow-lg">
            <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Active VIP Members</span>
            <div class="text-3xl font-extrabold text-[#39FF88] mt-1">{{ number_format($activeVipCount) }}</div>
            <div class="text-xs text-emerald-400 mt-2">Active subscribers accessing premium promo codes</div>
        </div>

        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-5 shadow-lg">
            <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Subscription Revenue</span>
            <div class="text-3xl font-extrabold text-[#DDD06A] mt-1">{{ number_format($totalSubscriptionRevenue) }} Tshs</div>
            <div class="text-xs text-yellow-300 mt-2">Weekly, Monthly & Season Passes</div>
        </div>

        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-5 shadow-lg">
            <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Single Slip Unlock Revenue</span>
            <div class="text-3xl font-extrabold text-white mt-1">{{ number_format($totalSlipRevenue) }} Tshs</div>
            <div class="text-xs text-gray-400 mt-2">Pay-per-view promo codes & premium slips</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Manual Grant Form -->
        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg h-fit">
            <h3 class="text-base font-bold text-[#DDD06A] mb-4">Manual VIP Membership Grant</h3>
            <form method="POST" action="{{ route('admin.vip.grant') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">User ID</label>
                    <input type="number" name="user_id" placeholder="e.g. 3" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Subscription Plan</label>
                    <select name="plan_type" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                        <option value="daily">Daily Pass (1 Day)</option>
                        <option value="weekly">Weekly VIP (7 Days)</option>
                        <option value="monthly">Monthly Master VIP (30 Days)</option>
                        <option value="vip_season">VIP Full Season (180 Days)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Duration (Days)</label>
                    <input type="number" name="duration_days" value="30" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Ledger Amount (Tshs)</label>
                    <input type="number" name="amount" value="30000" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <button type="submit" class="w-full bg-[#DDD06A] hover:bg-yellow-400 text-[#013220] font-bold py-2.5 rounded-lg text-sm transition">
                    Grant VIP Access
                </button>
            </form>
        </div>

        <!-- Subscriptions Table -->
        <div class="lg:col-span-2 bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg">
            <h3 class="text-base font-bold text-white mb-4">VIP Subscriber Directory</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-[#013220] border-b border-[#1b4332] text-gray-300 text-xs uppercase font-semibold">
                            <th class="p-3">User</th>
                            <th class="p-3">Tier</th>
                            <th class="p-3">Paid (Tshs)</th>
                            <th class="p-3">Valid Until</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1b4332] text-gray-200">
                        @forelse($subscriptions as $sub)
                        <tr class="hover:bg-[#082014]">
                            <td class="p-3 font-medium">
                                <div class="text-white">{{ $sub->user->name ?? 'User #' . $sub->user_id }}</div>
                                <div class="text-xs text-gray-400">{{ $sub->user->phone ?? $sub->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="p-3 capitalize font-semibold text-[#DDD06A]">{{ str_replace('_', ' ', $sub->plan_type) }}</td>
                            <td class="p-3 font-mono font-bold">{{ number_format($sub->amount_paid) }}</td>
                            <td class="p-3 text-xs text-gray-300">{{ \Carbon\Carbon::parse($sub->expires_at)->format('M d, Y H:i') }}</td>
                            <td class="p-3">
                                @if($sub->status === 'active' && $sub->expires_at > now())
                                    <span class="bg-emerald-950 text-emerald-400 text-[11px] px-2 py-0.5 rounded font-bold border border-emerald-700">Active</span>
                                @else
                                    <span class="bg-gray-800 text-gray-400 text-[11px] px-2 py-0.5 rounded font-bold">Expired</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                @if($sub->status === 'active')
                                <form method="POST" action="{{ route('admin.vip.revoke', $sub->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300">Revoke</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-400">No active VIP subscriptions logged yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
