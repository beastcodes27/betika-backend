@extends('layouts.admin')

@section('title', 'Financial Ledger')
@section('header', 'Payment Gateway Ledger (M-Pesa & Airtel Money)')

@section('content')
<div class="space-y-6">
    <!-- Financial summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-5 shadow-lg">
            <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Total Revenue Collected (Tshs)</span>
            <div class="text-3xl font-extrabold text-[#39FF88] mt-1">{{ number_format($totalCollected) }} Tshs</div>
            <div class="text-xs text-emerald-400 mt-2">Cleared & verified mobile money deposits</div>
        </div>
        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-5 shadow-lg">
            <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Pending Reconciliations</span>
            <div class="text-3xl font-extrabold text-amber-300 mt-1">{{ number_format($pendingCount) }}</div>
            <div class="text-xs text-amber-400 mt-2">Transactions awaiting network gateway callback</div>
        </div>
    </div>

    <!-- Filters & Transactions Table -->
    <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl overflow-hidden shadow-xl">
        <div class="p-4 border-b border-[#1b4332] flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.transactions.index') }}" class="flex flex-wrap items-center gap-3">
                <select name="provider" class="bg-[#013220] border border-[#1b4332] text-sm text-gray-200 rounded-lg px-3 py-1.5">
                    <option value="">All Providers</option>
                    <option value="mpesa" {{ request('provider') == 'mpesa' ? 'selected' : '' }}>Vodacom M-Pesa</option>
                    <option value="airtel" {{ request('provider') == 'airtel' ? 'selected' : '' }}>Airtel Money</option>
                    <option value="tigopesa" {{ request('provider') == 'tigopesa' ? 'selected' : '' }}>Tigo Pesa</option>
                </select>

                <select name="status" class="bg-[#013220] border border-[#1b4332] text-sm text-gray-200 rounded-lg px-3 py-1.5">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>

                <select name="type" class="bg-[#013220] border border-[#1b4332] text-sm text-gray-200 rounded-lg px-3 py-1.5">
                    <option value="">All Types</option>
                    <option value="subscription" {{ request('type') == 'subscription' ? 'selected' : '' }}>VIP Subscription</option>
                    <option value="slip_unlock" {{ request('type') == 'slip_unlock' ? 'selected' : '' }}>Single Slip Unlock</option>
                </select>

                <button type="submit" class="bg-[#DDD06A] text-[#013220] font-semibold text-xs px-3 py-2 rounded-lg hover:bg-yellow-400">
                    Filter Ledger
                </button>
            </form>

            <span class="text-xs text-gray-400">Total Records: <strong class="text-white">{{ $transactions->total() }}</strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-[#013220] border-b border-[#1b4332] text-gray-300 text-xs uppercase font-semibold">
                        <th class="p-3">Reference ID</th>
                        <th class="p-3">User</th>
                        <th class="p-3">Provider</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Amount (Tshs)</th>
                        <th class="p-3">Timestamp</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1b4332] text-gray-200">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-[#082014]">
                        <td class="p-3 font-mono text-xs text-[#DDD06A] font-bold">{{ $tx->reference_id }}</td>
                        <td class="p-3">
                            <div class="text-white font-medium">{{ $tx->user->name ?? 'User #' . $tx->user_id }}</div>
                            <div class="text-xs text-gray-400">{{ $tx->user->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="p-3">
                            <span class="inline-block px-2 py-0.5 rounded text-xs uppercase font-bold {{ $tx->provider === 'mpesa' ? 'bg-red-950 text-red-400 border border-red-700' : ($tx->provider === 'airtel' ? 'bg-amber-950 text-amber-400 border border-amber-700' : 'bg-blue-950 text-blue-400 border border-blue-700') }}">
                                {{ $tx->provider }}
                            </span>
                        </td>
                        <td class="p-3 text-xs capitalize text-gray-300">{{ str_replace('_', ' ', $tx->type) }}</td>
                        <td class="p-3 font-mono font-bold text-white">{{ number_format($tx->amount) }} {{ $tx->currency }}</td>
                        <td class="p-3 text-xs text-gray-400">{{ \Carbon\Carbon::parse($tx->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="p-3">
                            @if($tx->status === 'completed')
                                <span class="bg-emerald-950 text-emerald-300 border border-emerald-600 text-xs px-2 py-0.5 rounded font-bold">Completed</span>
                            @elseif($tx->status === 'pending')
                                <span class="bg-yellow-950 text-yellow-300 border border-yellow-600 text-xs px-2 py-0.5 rounded font-bold">Pending</span>
                            @else
                                <span class="bg-red-950 text-red-300 border border-red-600 text-xs px-2 py-0.5 rounded font-bold">Failed</span>
                            @endif
                        </td>
                        <td class="p-3 text-right text-xs space-x-2">
                            @if($tx->status === 'pending')
                                <form method="POST" action="{{ route('admin.transactions.complete', $tx->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-400 hover:text-emerald-300">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.transactions.fail', $tx->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-400 hover:text-red-300">Reject</button>
                                </form>
                            @else
                                <span class="text-gray-500 italic">Settled</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-400">No financial transactions recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#1b4332]">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
