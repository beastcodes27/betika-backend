@extends('layouts.admin')

@section('title', 'Betslip Management')
@section('header', 'Published Betslips & Booking Codes')

@section('content')
<div class="space-y-6">
    <!-- Top Filter Bar -->
    <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-4 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.betslips.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <select name="status" class="bg-[#013220] border border-[#1b4332] text-sm text-gray-200 rounded-lg px-3 py-2">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="won" {{ request('status') == 'won' ? 'selected' : '' }}>Won</option>
                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
            </select>

            <select name="vip" class="bg-[#013220] border border-[#1b4332] text-sm text-gray-200 rounded-lg px-3 py-2">
                <option value="">All Access Tiers</option>
                <option value="1" {{ request('vip') == '1' ? 'selected' : '' }}>VIP Only</option>
                <option value="0" {{ request('vip') == '0' ? 'selected' : '' }}>Free Slips</option>
            </select>

            <select name="bookmaker_id" class="bg-[#013220] border border-[#1b4332] text-sm text-gray-200 rounded-lg px-3 py-2">
                <option value="">All Bookmakers</option>
                @foreach($bookmakers as $bm)
                    <option value="{{ $bm->id }}" {{ request('bookmaker_id') == $bm->id ? 'selected' : '' }}>{{ $bm->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-[#DDD06A] text-[#013220] font-semibold text-sm px-4 py-2 rounded-lg hover:bg-yellow-400">
                Filter Slips
            </button>
        </form>

        <div class="text-xs text-gray-400">
            Total Slips: <span class="font-bold text-white">{{ $slips->total() }}</span>
        </div>
    </div>

    <!-- Slips Table -->
    <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-[#013220] border-b border-[#1b4332] text-gray-300 text-xs font-semibold uppercase">
                        <th class="p-4">Tipster / User</th>
                        <th class="p-4">Slip Title & Matches</th>
                        <th class="p-4">Bookmaker & Code</th>
                        <th class="p-4">Total Odds</th>
                        <th class="p-4">Stake & Potential</th>
                        <th class="p-4">Access Tier</th>
                        <th class="p-4">Outcome</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1b4332] text-gray-200">
                    @forelse($slips as $slip)
                    <tr class="hover:bg-[#082014] transition">
                        <td class="p-4 font-medium">
                            @if($slip->tipster)
                                <div class="flex items-center space-x-2">
                                    <span class="w-7 h-7 rounded-full bg-emerald-800 text-white flex items-center justify-center font-bold text-xs">
                                        {{ substr($slip->tipster->display_name, 0, 1) }}
                                    </span>
                                    <div>
                                        <div class="text-white font-semibold">{{ $slip->tipster->display_name }}</div>
                                        <div class="text-xs text-[#DDD06A]">Win Rate: {{ $slip->tipster->win_rate }}%</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 italic">Platform System</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-white">{{ $slip->title }}</div>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ $slip->selections->count() }} games:
                                @foreach($slip->selections->take(2) as $sel)
                                    <span class="inline-block bg-[#013220] px-1.5 py-0.5 rounded text-[11px] mr-1">
                                        {{ $sel->home_team }} vs {{ $sel->away_team }} ({{ $sel->prediction }})
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="font-semibold text-emerald-400">{{ $slip->bookmaker ? $slip->bookmaker->name : 'Universal' }}</span>
                            <div class="font-mono text-xs text-[#39FF88] mt-0.5 tracking-wider bg-black/40 px-2 py-0.5 rounded inline-block">
                                {{ $slip->booking_code ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="p-4 font-mono font-bold text-yellow-300">
                            {{ number_format($slip->total_odds, 2) }}
                        </td>
                        <td class="p-4 text-xs">
                            <div>Stake: <span class="text-white font-semibold">{{ number_format($slip->stake_amount) }} Tshs</span></div>
                            <div class="text-emerald-400">Est. Win: {{ number_format($slip->stake_amount * $slip->total_odds) }} Tshs</div>
                        </td>
                        <td class="p-4">
                            <form method="POST" action="{{ route('admin.betslips.toggle-vip', $slip->id) }}">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $slip->is_vip ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : 'bg-gray-700/50 text-gray-300' }}">
                                    {{ $slip->is_vip ? '⭐ VIP Tier' : '🔓 Free' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-4">
                            <form method="POST" action="{{ route('admin.betslips.status', $slip->id) }}" class="inline-block">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="text-xs rounded px-2 py-1 font-semibold border {{ $slip->status === 'won' ? 'bg-green-950 text-emerald-300 border-emerald-600' : ($slip->status === 'lost' ? 'bg-red-950 text-red-300 border-red-600' : 'bg-yellow-950 text-yellow-300 border-yellow-600') }}">
                                    <option value="active" {{ $slip->status === 'active' ? 'selected' : '' }}>⏳ Active</option>
                                    <option value="won" {{ $slip->status === 'won' ? 'selected' : '' }}>✅ Won</option>
                                    <option value="lost" {{ $slip->status === 'lost' ? 'selected' : '' }}>❌ Lost</option>
                                    <option value="void" {{ $slip->status === 'void' ? 'selected' : '' }}>⚪ Void</option>
                                </select>
                            </form>
                        </td>
                        <td class="p-4 text-right">
                            <form method="POST" action="{{ route('admin.betslips.destroy', $slip->id) }}" onsubmit="return confirm('Delete this slip?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-400">No betslips found matching your criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#1b4332]">
            {{ $slips->links() }}
        </div>
    </div>
</div>
@endsection
