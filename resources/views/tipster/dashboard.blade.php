@extends('layouts.tipster')

@section('title', 'My Tipster Workspace')

@section('content')
<div class="space-y-6">

    <!-- Profile Overview Card -->
    <div class="bg-forest-card border border-forest-border p-6 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-white text-2xl shadow" style="background-color: {{ $profile->avatar_bg ?? '#3B82F6' }}">
                {{ substr($profile->display_name ?? 'T', 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-black text-white">{{ $profile->display_name ?? auth()->user()->name }}</h2>
                    @if($profile->is_verified ?? false)
                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-neon/15 text-neon border border-neon/30">VERIFIED PRO</span>
                    @endif
                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-gold/20 text-gold border border-gold/40">{{ $profile->badge ?? 'TIPSTER' }}</span>
                </div>
                <p class="text-xs text-gray-400 font-mono">{{ $profile->handle ?? '@tipster' }} &bull; {{ $profile->specialty ?? 'Football Prediction Specialist' }}</p>
                <p class="text-xs text-neon font-bold mt-1">Super Admin Approval: <span class="capitalize">{{ $profile->status ?? 'Active' }}</span> (Publishing: {{ ($profile->can_publish ?? false) ? 'Enabled' : 'Pending' }})</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($profile->can_publish ?? false)
                <a href="{{ route('tipster.slips.create') }}" class="px-5 py-2.5 bg-neon hover:bg-neon/90 text-forest-dark font-black text-sm rounded-xl transition shadow">
                    + Publish Bookmaker Slip
                </a>
            @else
                <span class="px-4 py-2 bg-gold/20 text-gold border border-gold/40 text-xs font-bold rounded-xl">
                    Approval Pending
                </span>
            @endif
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-forest-card border border-forest-border p-4 rounded-xl">
            <p class="text-[11px] font-bold text-gray-400 uppercase">Win Rate</p>
            <p class="text-2xl font-black text-neon mt-1">{{ number_format($stats['win_rate'], 1) }}%</p>
            <p class="text-[10px] text-gray-400">Verified by Admin</p>
        </div>
        <div class="bg-forest-card border border-forest-border p-4 rounded-xl">
            <p class="text-[11px] font-bold text-gray-400 uppercase">Current Streak</p>
            <p class="text-2xl font-black text-gold mt-1">{{ $stats['streak'] }}</p>
            <p class="text-[10px] text-gray-400">Winning Runs</p>
        </div>
        <div class="bg-forest-card border border-forest-border p-4 rounded-xl">
            <p class="text-[11px] font-bold text-gray-400 uppercase">Code Copies</p>
            <p class="text-2xl font-black text-white mt-1">{{ number_format($stats['total_copies']) }}</p>
            <p class="text-[10px] text-gray-400">By App Bettors</p>
        </div>
        <div class="bg-forest-card border border-forest-border p-4 rounded-xl">
            <p class="text-[11px] font-bold text-gray-400 uppercase">Active Slips</p>
            <p class="text-2xl font-black text-white mt-1">{{ $stats['active_slips'] }}</p>
            <p class="text-[10px] text-gray-400">Live in Mobile Feed</p>
        </div>
    </div>

    <!-- My Published Slips -->
    <div class="bg-forest-card border border-forest-border rounded-2xl p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-white text-base">My Published Slips with Bookmakers</h3>
            <span class="text-xs text-gray-400">{{ $slips->total() }} Total Slips</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-forest-border text-gray-400 uppercase text-[10px]">
                    <tr>
                        <th class="py-2.5">Title</th>
                        <th class="py-2.5">Bookmaker</th>
                        <th class="py-2.5">Booking Code</th>
                        <th class="py-2.5">Matches</th>
                        <th class="py-2.5">Odds</th>
                        <th class="py-2.5">Access</th>
                        <th class="py-2.5">Status</th>
                        <th class="py-2.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-forest-border">
                    @forelse($slips as $slip)
                        <tr class="hover:bg-forest-dark/40 transition">
                            <td class="py-3 font-bold text-white max-w-[200px] truncate">{{ $slip->title }}</td>
                            <td class="py-3 font-bold text-gold">{{ $slip->bookmaker->name }}</td>
                            <td class="py-3 font-mono font-black text-neon bg-forest-dark px-2 py-1 rounded">{{ $slip->booking_code }}</td>
                            <td class="py-3 text-gray-300">{{ $slip->matches_count }} Games</td>
                            <td class="py-3 font-black text-white">{{ number_format($slip->total_odds, 2) }}</td>
                            <td class="py-3">
                                @if($slip->is_free)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-neon/15 text-neon">FREE</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-gold/20 text-gold">VIP ({{ $slip->amount }})</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="capitalize px-2 py-0.5 rounded text-[10px] bg-forest-dark border border-forest-border text-gray-300">{{ $slip->status }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <form action="{{ route('tipster.slips.destroy', $slip) }}" method="POST" onsubmit="return confirm('Delete this slip?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-bold text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">
                                You have not published any slips yet. Click "Publish New Slip" to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($slips->hasPages())
            <div class="pt-4 border-t border-forest-border">
                {{ $slips->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
