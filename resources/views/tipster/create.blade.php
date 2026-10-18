@extends('layouts.tipster')

@section('title', 'Publish Bookmaker Slip')

@section('content')
<div class="max-w-3xl mx-auto bg-forest-card border border-forest-border rounded-2xl p-6 space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-forest-border">
        <div>
            <h2 class="text-xl font-black text-white">Publish New Betslip</h2>
            <p class="text-xs text-gray-400">Post ticket with bookmaker code (Betika, SportyBet, 1xBet, MozzartBet, Betway)</p>
        </div>
        <a href="{{ route('tipster.dashboard') }}" class="text-xs text-gray-400 hover:text-white">&larr; Back to Dashboard</a>
    </div>

    @if($errors->any())
        <div class="bg-red-950/50 border border-red-500/30 text-red-200 text-xs p-3 rounded-xl space-y-1">
            @foreach($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('tipster.slips.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Bookmaker & Category -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Betting Company</label>
                <select name="bookmaker_id" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white focus:border-neon">
                    @foreach($bookmakers as $bm)
                        <option value="{{ $bm->id }}" {{ old('bookmaker_id') == $bm->id ? 'selected' : '' }}>
                            {{ $bm->name }} ({{ $bm->code_prefix }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Category</label>
                <select name="category" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white focus:border-neon">
                    <option value="Banker">Banker (Safe Multi)</option>
                    <option value="High Odds">High Odds Accumulator</option>
                    <option value="Goals">Goal Rush / Over-Under</option>
                    <option value="Combo">Combo Special</option>
                    <option value="VIP">VIP Elite Ticket</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Booking Code</label>
                <input type="text" name="booking_code" value="{{ old('booking_code') }}" required placeholder="e.g. BTK-94821" class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm font-mono font-bold text-neon focus:border-neon uppercase">
            </div>
        </div>

        <!-- Slip Title & Validity -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Slip Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Weekend 4-Fold Banker Combo" class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Validity Duration</label>
                <input type="text" name="validity_time" value="{{ old('validity_time', 'Valid for 2h 30m') }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white">
            </div>
        </div>

        <!-- Odds, Confidence & VIP Pricing -->
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Total Odds</label>
                <input type="number" step="0.01" name="total_odds" value="{{ old('total_odds', '4.50') }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white font-black">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Confidence (%)</label>
                <input type="number" name="confidence" value="{{ old('confidence', '88') }}" min="50" max="100" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Access Tier</label>
                <div class="flex items-center gap-4 pt-2">
                    <label class="flex items-center gap-1.5 cursor-pointer text-xs font-bold">
                        <input type="checkbox" name="is_free" id="isFreeCheckbox" value="1" checked class="rounded bg-forest-dark border-forest-border text-neon">
                        <span>Free Slip</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Match Selections Builder -->
        <div class="space-y-3 pt-4 border-t border-forest-border">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-black text-white">Included Matches (Selections)</h4>
                <span class="text-xs text-gray-400">At least 1 game required</span>
            </div>

            <div id="matchesContainer" class="space-y-3">
                <!-- Row 1 -->
                <div class="bg-forest-dark p-4 rounded-xl border border-forest-border space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="matches[0][match]" placeholder="Match (e.g. Arsenal vs Chelsea)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                        <input type="text" name="matches[0][league]" placeholder="League (e.g. Premier League)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="text" name="matches[0][kickoff]" placeholder="Kickoff (e.g. Today 20:30)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                        <input type="text" name="matches[0][pick]" placeholder="Tip (e.g. Home to Win)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                        <input type="number" step="0.01" name="matches[0][odds]" placeholder="Odds (e.g. 1.75)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="bg-forest-dark p-4 rounded-xl border border-forest-border space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="matches[1][match]" placeholder="Match (e.g. Real Madrid vs Sevilla)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                        <input type="text" name="matches[1][league]" placeholder="League (e.g. La Liga)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="text" name="matches[1][kickoff]" placeholder="Kickoff (e.g. Today 22:00)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                        <input type="text" name="matches[1][pick]" placeholder="Tip (e.g. Over 2.5 Goals)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                        <input type="number" step="0.01" name="matches[1][odds]" placeholder="Odds (e.g. 1.60)" required class="bg-forest-card border border-forest-border rounded-lg px-3 py-2 text-xs text-white">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-forest-border">
            <a href="{{ route('tipster.dashboard') }}" class="text-xs text-gray-400 hover:text-white">Cancel</a>
            <button type="submit" class="px-6 py-3 bg-neon hover:bg-neon/90 text-forest-dark font-black text-sm rounded-xl transition shadow">
                Publish Betslip Live &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
