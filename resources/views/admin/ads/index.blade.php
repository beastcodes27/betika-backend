@extends('layouts.admin')

@section('title', 'Auto-Scrolling Ads Board')
@section('header', 'Ads Board & Promo Codes (Auto-scrolls every 3s)')

@section('content')
<div class="space-y-6">
    <div class="bg-emerald-950/40 border border-emerald-600/30 rounded-xl p-4 flex items-center justify-between text-xs text-emerald-300">
        <div class="flex items-center space-x-2">
            <span class="w-3 h-3 rounded-full bg-[#39FF88] animate-pulse"></span>
            <span>The mobile application auto-scrolls through these active banners every <strong>3 seconds</strong> seamlessly without visible timer distractions.</span>
        </div>
        <span class="font-mono text-[#DDD06A]">Rotator Frequency: 3000ms</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- New Ad Banner Form -->
        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg h-fit">
            <h3 class="text-base font-bold text-[#DDD06A] mb-4">Add Advertising Banner</h3>
            <form method="POST" action="{{ route('admin.ads.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Banner Title</label>
                    <input type="text" name="title" placeholder="e.g. 100% Karibu Bonus on Betika" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Subtitle / Incentive</label>
                    <input type="text" name="subtitle" placeholder="Get up to 500,000 Tshs First Deposit Match" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Promo Code (Hidden until clicked)</label>
                    <input type="text" name="promo_code" placeholder="BETIKA100" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm font-mono text-[#DDD06A]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Affiliated Bookmaker</label>
                    <select name="bookmaker_id" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                        <option value="">None / General Promotion</option>
                        @foreach($bookmakers as $bm)
                            <option value="{{ $bm->id }}">{{ $bm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Banner Image URL or Asset Name</label>
                    <input type="text" name="banner_url" placeholder="https://images.unsplash.com/photo-1508098682722-e99c43a406b2" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Action Type</label>
                        <select name="action_type" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-2 py-2 text-xs text-white">
                            <option value="register">Register Account</option>
                            <option value="deposit">Deposit Tshs</option>
                            <option value="vip">Subscribe VIP</option>
                            <option value="betslip">Open Betslip</option>
                            <option value="external">External Link</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Display (Seconds)</label>
                        <input type="number" name="display_seconds" value="3" min="2" max="10" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-xs text-white text-center">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Sort Order</label>
                    <input type="number" name="order_index" value="1" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-xs text-white">
                </div>

                <button type="submit" class="w-full bg-[#DDD06A] hover:bg-yellow-400 text-[#013220] font-bold py-2.5 rounded-lg text-sm transition">
                    Save to Ads Rotator
                </button>
            </form>
        </div>

        <!-- Banner List -->
        <div class="lg:col-span-2 space-y-4">
            @forelse($ads as $ad)
            <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-5 shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-4 w-full md:w-auto">
                    <div class="w-20 h-14 rounded-lg bg-cover bg-center shrink-0 border border-[#1b4332]" style="background-image: url('{{ $ad->banner_url }}');"></div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold text-[#DDD06A]">#{{ $ad->order_index }}</span>
                            <h4 class="font-bold text-white text-sm">{{ $ad->title }}</h4>
                        </div>
                        <p class="text-xs text-gray-400">{{ $ad->subtitle }}</p>
                        <div class="flex items-center space-x-3 mt-1 text-xs">
                            @if($ad->promo_code)
                                <span class="bg-[#013220] border border-[#1b4332] text-[#39FF88] font-mono px-2 py-0.5 rounded">
                                    Code: {{ $ad->promo_code }}
                                </span>
                            @endif
                            <span class="text-gray-400">Duration: <strong>{{ $ad->display_seconds }}s</strong></span>
                            <span class="text-gray-400">Clicks: <strong class="text-white">{{ $ad->click_count }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    <form method="POST" action="{{ route('admin.ads.toggle', $ad->id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $ad->is_active ? 'bg-emerald-950 text-emerald-400 border border-emerald-700' : 'bg-gray-800 text-gray-400' }}">
                            {{ $ad->is_active ? 'Active' : 'Disabled' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.ads.destroy', $ad->id) }}" onsubmit="return confirm('Delete this banner?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-950 text-red-300 border border-red-800">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-8 text-center text-gray-400">
                No ads on the board yet. Add banners to start the 3-second auto-rotation.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
