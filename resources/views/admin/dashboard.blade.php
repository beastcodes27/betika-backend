@extends('layouts.admin')

@section('title', 'Platform Overview')
@section('page_title', 'Executive Super Admin Dashboard')
@section('page_subtitle', 'Live Platform Statistics, Financial Overview (Tshs), and Tipster Network Activity')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Revenue Card -->
        <div class="bg-forest-card border border-forest-border p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Gross Platform Revenue</p>
                    <h3 class="text-2xl font-black text-gold mt-1">Tshs {{ number_format($stats['total_revenue_tshs'] ?? 15420000) }}</h3>
                    <p class="text-[11px] text-neon font-semibold mt-1 flex items-center gap-1">
                        <span>&uarr; 18.4%</span> vs last week
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gold/15 text-gold flex items-center justify-center font-bold text-lg">
                    💰
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-gold/50 to-transparent"></div>
        </div>

        <!-- Active Slips Card -->
        <div class="bg-forest-card border border-forest-border p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Published Slips</p>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $stats['active_slips'] }} <span class="text-xs font-normal text-gray-400">/ {{ $stats['total_slips'] }} total</span></h3>
                    <p class="text-[11px] text-gray-400 font-semibold mt-1">Across 5 Betting Partners</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-neon/15 text-neon flex items-center justify-center font-bold text-lg">
                    📋
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-neon/50 to-transparent"></div>
        </div>

        <!-- Tipsters Card -->
        <div class="bg-forest-card border border-forest-border p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Managed Tipsters</p>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $stats['total_tipsters'] }} <span class="text-xs font-bold text-gold">PRO</span></h3>
                    @if($stats['pending_tipsters'] > 0)
                        <p class="text-[11px] text-gold font-bold mt-1">{{ $stats['pending_tipsters'] }} Awaiting Approval</p>
                    @else
                        <p class="text-[11px] text-neon font-semibold mt-1">100% Verified & Active</p>
                    @endif
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center font-bold text-lg">
                    🎯
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500/50 to-transparent"></div>
        </div>

        <!-- Community Poll Votes Card -->
        <div class="bg-forest-card border border-forest-border p-5 rounded-2xl relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Community Votes Cast</p>
                    <h3 class="text-2xl font-black text-neon mt-1">{{ number_format($stats['total_poll_votes']) }}</h3>
                    <p class="text-[11px] text-gray-400 font-semibold mt-1">Big Match Predictions</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-neon/15 text-neon flex items-center justify-center font-bold text-lg">
                    🗳️
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-neon/50 to-transparent"></div>
        </div>
    </div>

    <!-- Bookmaker Slips Distribution Pill Bar -->
    <div class="bg-forest-card border border-forest-border p-4 rounded-2xl">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-gray-300">Live Betting Company Slip Integration</h4>
            <span class="text-[11px] text-gray-400">Real bookmaker logos enabled on mobile slips</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            @foreach($bookmakers as $bm)
                <div class="bg-forest-dark border border-forest-border p-3 rounded-xl flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black text-white">{{ $bm->name }}</p>
                        <p class="text-[10px] text-gray-400">Prefix: <strong class="text-gold">{{ $bm->code_prefix }}</strong></p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-neon/15 text-neon border border-neon/30">
                        {{ $bm->betslips_count }} Slips
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 2 Column Section: Recent Published Slips vs Tipster Leaderboard -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Slips Table (2 cols) -->
        <div class="lg:col-span-2 bg-forest-card border border-forest-border rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-white text-base">Recent Published Betslips</h3>
                    <p class="text-xs text-gray-400">Live booking codes posted by verified tipsters</p>
                </div>
                <a href="{{ route('admin.betslips.index') }}" class="text-xs font-bold text-neon hover:underline">View All &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-forest-border text-gray-400 font-bold uppercase text-[10px] tracking-wider">
                            <th class="py-2.5">Tipster</th>
                            <th class="py-2.5">Title</th>
                            <th class="py-2.5">Bookmaker</th>
                            <th class="py-2.5">Booking Code</th>
                            <th class="py-2.5">Odds</th>
                            <th class="py-2.5">Tier</th>
                            <th class="py-2.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-forest-border">
                        @foreach($recentSlips as $slip)
                            <tr class="hover:bg-forest-dark/40 transition">
                                <td class="py-3 font-bold text-white flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black text-white" style="background-color: {{ $slip->tipster->avatar_bg ?? '#013220' }}">
                                        {{ substr($slip->tipster->display_name ?? 'T', 0, 1) }}
                                    </span>
                                    <span>{{ $slip->tipster->display_name ?? 'Tipster' }}</span>
                                </td>
                                <td class="py-3 text-gray-200 max-w-[160px] truncate">{{ $slip->title }}</td>
                                <td class="py-3 font-bold text-gold">{{ $slip->bookmaker->name ?? 'Betika' }}</td>
                                <td class="py-3 font-mono font-bold text-neon bg-forest-dark px-2 py-1 rounded border border-forest-border">{{ $slip->booking_code }}</td>
                                <td class="py-3 font-black text-white">{{ number_format($slip->total_odds, 2) }}</td>
                                <td class="py-3">
                                    @if($slip->is_free)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-neon/15 text-neon border border-neon/30">FREE</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-gold/20 text-gold border border-gold/40">VIP ({{ $slip->amount }})</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-forest-inner text-gray-300 capitalize">{{ $slip->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tipster Performance (1 col) -->
        <div class="bg-forest-card border border-forest-border rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-white text-base">Top Tipsters</h3>
                    <p class="text-xs text-gray-400">Ranked by verified win rate</p>
                </div>
                <a href="{{ route('admin.tipsters.index') }}" class="text-xs font-bold text-gold hover:underline">Manage &rarr;</a>
            </div>

            <div class="space-y-3">
                @foreach($topTipsters as $t)
                    <div class="bg-forest-dark border border-forest-border p-3 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white text-sm" style="background-color: {{ $t->avatar_bg }}">
                                {{ substr($t->display_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-black text-white flex items-center gap-1">
                                    {{ $t->display_name }}
                                    @if($t->is_verified)
                                        <span class="text-neon text-[11px]">&check;</span>
                                    @endif
                                </p>
                                <p class="text-[10px] text-gray-400">{{ $t->handle }} &bull; {{ $t->streak }}</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-xs font-black text-neon">{{ number_format($t->win_rate, 0) }}% Win</span>
                            <p class="text-[10px] text-gold font-bold">{{ $t->badge }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
