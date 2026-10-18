@extends('layouts.admin')

@section('title', 'Featured Big Matches')
@section('header', 'Big Match Showcase & Odds')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Create Match Form -->
        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg h-fit">
            <h3 class="text-base font-bold text-[#DDD06A] mb-4">Add Featured Big Match</h3>
            <form method="POST" action="{{ route('admin.featured.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">League Name</label>
                    <input type="text" name="league_name" placeholder="e.g. UEFA Champions League" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Home Team</label>
                        <input type="text" name="home_team" placeholder="Arsenal" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Away Team</label>
                        <input type="text" name="away_team" placeholder="Chelsea" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Venue / Stadium</label>
                    <input type="text" name="venue" placeholder="Emirates Stadium, London" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Kickoff Date & Time</label>
                    <input type="datetime-local" name="kickoff_at" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Home (1)</label>
                        <input type="number" step="0.01" name="home_win_odds" placeholder="1.85" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-2 py-2 text-sm text-white text-center">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Draw (X)</label>
                        <input type="number" step="0.01" name="draw_odds" placeholder="3.50" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-2 py-2 text-sm text-white text-center">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Away (2)</label>
                        <input type="number" step="0.01" name="away_win_odds" placeholder="4.20" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-2 py-2 text-sm text-white text-center">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Expert Key Analysis</label>
                    <textarea name="expert_analysis" rows="3" placeholder="Both teams score in 80% of meetings. Arsenal unbeaten in last 6 home fixtures." class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white"></textarea>
                </div>

                <button type="submit" class="w-full bg-[#DDD06A] hover:bg-yellow-400 text-[#013220] font-bold py-2.5 rounded-lg text-sm transition">
                    Publish Featured Match
                </button>
            </form>
        </div>

        <!-- Matches List -->
        <div class="lg:col-span-2 bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg">
            <h3 class="text-base font-bold text-white mb-4">Active & Upcoming Big Matches</h3>
            <div class="space-y-4">
                @forelse($matches as $m)
                <div class="bg-[#013220] border border-[#1b4332] rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold text-[#DDD06A] uppercase tracking-wider">{{ $m->league_name }}</span>
                            <span class="text-xs text-gray-400">• {{ \Carbon\Carbon::parse($m->kickoff_at)->format('M d, H:i') }}</span>
                            @if($m->is_active)
                                <span class="bg-emerald-950 text-emerald-400 text-[10px] px-2 py-0.5 rounded font-semibold border border-emerald-700">Live on Home</span>
                            @else
                                <span class="bg-gray-800 text-gray-400 text-[10px] px-2 py-0.5 rounded font-semibold">Hidden</span>
                            @endif
                        </div>
                        <div class="text-lg font-bold text-white">
                            {{ $m->home_team }} <span class="text-gray-400 font-normal">vs</span> {{ $m->away_team }}
                        </div>
                        <div class="text-xs text-gray-300 flex items-center space-x-4">
                            <span>📍 {{ $m->venue ?? 'Main Stadium' }}</span>
                            <span>Odds: <strong class="text-[#39FF88]">{{ $m->home_win_odds }}</strong> / <strong>{{ $m->draw_odds }}</strong> / <strong class="text-yellow-400">{{ $m->away_win_odds }}</strong></span>
                        </div>
                        @if($m->expert_analysis)
                            <p class="text-xs text-gray-400 italic pt-1">{{ Str::limit($m->expert_analysis, 110) }}</p>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2 self-end md:self-center">
                        <form method="POST" action="{{ route('admin.featured.toggle', $m->id) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $m->is_active ? 'bg-amber-900/40 text-amber-300 border border-amber-700' : 'bg-emerald-900/40 text-emerald-300 border border-emerald-700' }}">
                                {{ $m->is_active ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.featured.destroy', $m->id) }}" onsubmit="return confirm('Delete this match?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-900/40 text-red-300 border border-red-700">
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400">No featured matches configured.</div>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $matches->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
