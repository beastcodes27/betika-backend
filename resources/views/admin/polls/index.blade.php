@extends('layouts.admin')

@section('title', 'Community Match Polls')
@section('header', 'Interactive Match Voting Polls')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- New Poll Form -->
        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg h-fit">
            <h3 class="text-base font-bold text-[#DDD06A] mb-4">Launch New Poll</h3>
            <form method="POST" action="{{ route('admin.polls.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Match Headline</label>
                    <input type="text" name="match_title" placeholder="Arsenal vs Chelsea" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Poll Question</label>
                    <input type="text" name="question" placeholder="Who will score the first goal?" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Poll Options (Min 2)</label>
                    <div class="space-y-2">
                        <input type="text" name="options[]" placeholder="Option 1: Bukayo Saka" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-1.5 text-xs text-white">
                        <input type="text" name="options[]" placeholder="Option 2: Cole Palmer" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-1.5 text-xs text-white">
                        <input type="text" name="options[]" placeholder="Option 3: No Goal / Clean Sheet" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-1.5 text-xs text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Poll Expiration Date</label>
                    <input type="date" name="expires_at" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <button type="submit" class="w-full bg-[#DDD06A] hover:bg-yellow-400 text-[#013220] font-bold py-2.5 rounded-lg text-sm transition">
                    Create & Activate Poll
                </button>
            </form>
        </div>

        <!-- Polls List -->
        <div class="lg:col-span-2 bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg">
            <h3 class="text-base font-bold text-white mb-4">Active & Historical Polls</h3>
            <div class="space-y-4">
                @forelse($polls as $p)
                <div class="bg-[#013220] border border-[#1b4332] rounded-xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-[#DDD06A] uppercase tracking-wider">{{ $p->match_title }}</span>
                            <h4 class="text-base font-bold text-white">{{ $p->question }}</h4>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2.5 py-0.5 rounded font-bold {{ $p->is_active ? 'bg-emerald-950 text-emerald-400 border border-emerald-700' : 'bg-gray-800 text-gray-400' }}">
                                {{ $p->is_active ? 'Active' : 'Closed' }}
                            </span>
                            <form method="POST" action="{{ route('admin.polls.toggle', $p->id) }}">
                                @csrf
                                <button type="submit" class="text-xs text-gray-300 hover:text-white underline">
                                    {{ $p->is_active ? 'Close' : 'Reopen' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.polls.destroy', $p->id) }}" onsubmit="return confirm('Delete poll?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300">Delete</button>
                            </form>
                        </div>
                    </div>

                    <!-- Options & Vote distribution -->
                    <div class="space-y-2 pt-2">
                        @foreach($p->options as $opt)
                            @php
                                $pct = $p->total_votes > 0 ? round(($opt['votes'] / $p->total_votes) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs text-gray-300 mb-1">
                                    <span>{{ $opt['text'] }}</span>
                                    <span class="font-bold text-[#39FF88]">{{ $opt['votes'] }} votes ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full bg-[#0b291a] rounded-full h-2 overflow-hidden border border-[#1b4332]">
                                    <div class="bg-[#DDD06A] h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-[11px] text-gray-400 pt-1 flex justify-between">
                        <span>Total Community Votes: <strong class="text-white">{{ $p->total_votes }}</strong></span>
                        <span>Expires: {{ $p->expires_at ? \Carbon\Carbon::parse($p->expires_at)->format('M d, Y') : 'Never' }}</span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400">No community polls created yet.</div>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $polls->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
