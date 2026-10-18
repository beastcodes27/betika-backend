@extends('layouts.admin')

@section('title', 'Tipster Management')
@section('page_title', 'Tipster Network Management')
@section('page_subtitle', 'Approve, Verify, Monitor, and Set Badges for Managed Tipsters')

@section('content')
<div class="space-y-6">

    <!-- Filters & Actions Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-forest-card border border-forest-border p-4 rounded-2xl">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tipsters.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ !$status ? 'bg-neon/15 text-neon border border-neon/30' : 'text-gray-300 hover:bg-forest-dark' }}">All ({{ \App\Models\TipsterProfile::count() }})</a>
            <a href="{{ route('admin.tipsters.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'pending' ? 'bg-gold/20 text-gold border border-gold/40' : 'text-gray-300 hover:bg-forest-dark' }}">Pending ({{ \App\Models\TipsterProfile::where('status', 'pending')->count() }})</a>
            <a href="{{ route('admin.tipsters.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'approved' ? 'bg-neon/15 text-neon border border-neon/30' : 'text-gray-300 hover:bg-forest-dark' }}">Approved ({{ \App\Models\TipsterProfile::where('status', 'approved')->count() }})</a>
            <a href="{{ route('admin.tipsters.index', ['status' => 'suspended']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'suspended' ? 'bg-red-950/40 text-red-300 border border-red-500/30' : 'text-gray-300 hover:bg-forest-dark' }}">Suspended ({{ \App\Models\TipsterProfile::where('status', 'suspended')->count() }})</a>
        </div>
    </div>

    <!-- Tipsters Table -->
    <div class="bg-forest-card border border-forest-border rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-forest-dark/70 border-b border-forest-border text-gray-400 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="p-4">Tipster</th>
                        <th class="p-4">Handle & Contact</th>
                        <th class="p-4">Specialty</th>
                        <th class="p-4">Win Rate & Streak</th>
                        <th class="p-4">Slips Published</th>
                        <th class="p-4">Status & Permissions</th>
                        <th class="p-4 text-right">Super Admin Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-forest-border">
                    @forelse($tipsters as $tipster)
                        <tr class="hover:bg-forest-dark/40 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center font-black text-white text-base shadow border border-forest-border bg-forest-dark" style="background-color: {{ $tipster->avatar_bg }}">
                                        @if($tipster->avatar_url)
                                            <img src="{{ $tipster->avatar_url }}" alt="{{ $tipster->display_name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($tipster->display_name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-white text-sm flex items-center gap-1.5">
                                            {{ $tipster->display_name }}
                                            @if($tipster->is_verified)
                                                <span class="text-neon" title="Verified Tipster">&check;</span>
                                            @endif
                                        </p>
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-black bg-gold/20 text-gold border border-gold/40 mt-0.5">{{ $tipster->badge }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4">
                                <p class="font-mono text-gray-200 font-bold">{{ $tipster->handle }}</p>
                                <p class="text-gray-400 text-[11px]">{{ $tipster->user->email ?? 'No email' }}</p>
                                <p class="text-gray-400 text-[11px]">{{ $tipster->user->phone ?? 'No phone' }}</p>
                            </td>

                            <td class="p-4 max-w-[180px] text-gray-300">
                                {{ $tipster->specialty }}
                            </td>

                            <td class="p-4">
                                <p class="text-neon font-black text-sm">{{ number_format($tipster->win_rate, 1) }}%</p>
                                <p class="text-gray-400 text-[11px] font-bold">{{ $tipster->streak }}</p>
                            </td>

                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-forest-dark border border-forest-border text-white">
                                    {{ $tipster->betslips->count() }} Slips
                                </span>
                            </td>

                            <td class="p-4 space-y-1">
                                <div>
                                    @if($tipster->status === 'approved')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-neon/15 text-neon border border-neon/30">APPROVED</span>
                                    @elseif($tipster->status === 'pending')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-gold/20 text-gold border border-gold/40">PENDING</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-red-950/40 text-red-300 border border-red-500/30">SUSPENDED</span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-gray-400">
                                    Publishing: <strong class="{{ $tipster->can_publish ? 'text-neon' : 'text-red-400' }}">{{ $tipster->can_publish ? 'Allowed' : 'Blocked' }}</strong>
                                </p>
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($tipster->status !== 'approved')
                                        <form action="{{ route('admin.tipsters.approve', $tipster) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-neon text-forest-dark hover:bg-neon/90 transition shadow">
                                                Approve
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.tipsters.suspend', $tipster) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-red-950/50 text-red-300 hover:bg-red-950/80 border border-red-500/30 transition">
                                                Suspend
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.tipsters.edit', $tipster) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-forest-inner text-gray-200 hover:text-white border border-forest-border transition">
                                        Edit
                                    </a>

                                    @if($tipster->avatar_url)
                                        <form action="{{ route('admin.tipsters.reset-avatar', $tipster) }}" method="POST" onsubmit="return confirm('Reset this tipster avatar picture to default initials?');">
                                            @csrf
                                            <button type="submit" class="px-2 py-1.5 rounded-lg text-[10px] font-bold text-amber-300 bg-amber-950/40 border border-amber-600/30 hover:bg-amber-900/50" title="Reset Inappropriate Avatar">
                                                Reset Pic
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">
                                No tipsters found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tipsters->hasPages())
            <div class="p-4 border-t border-forest-border">
                {{ $tipsters->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
