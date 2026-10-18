@extends('layouts.admin')

@section('title', 'Betting Companies & Bookmakers')
@section('header', 'Real Bookmaker Integrations & Branding')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- New Bookmaker Form -->
        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg h-fit">
            <h3 class="text-base font-bold text-[#DDD06A] mb-4">Add Betting Partner</h3>
            <form method="POST" action="{{ route('admin.bookmakers.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Company Name</label>
                    <input type="text" name="name" placeholder="e.g. Betika" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Slug Identifier</label>
                    <input type="text" name="slug" placeholder="e.g. betika" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm font-mono text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Logo URL or App Asset Key</label>
                    <input type="text" name="logo_url" placeholder="assets/bookmakers/betika.png" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Brand Primary Color (HEX)</label>
                    <input type="text" name="primary_color" placeholder="#013220" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm font-mono text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Official Website</label>
                    <input type="url" name="website_url" placeholder="https://www.betika.com/en-tz/" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Affiliate / Tracking Code</label>
                    <input type="text" name="affiliate_code" placeholder="BETWINNER2025" class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm font-mono text-[#DDD06A]">
                </div>

                <button type="submit" class="w-full bg-[#DDD06A] hover:bg-yellow-400 text-[#013220] font-bold py-2.5 rounded-lg text-sm transition">
                    Register Bookmaker
                </button>
            </form>
        </div>

        <!-- Bookmaker Partners Grid -->
        <div class="lg:col-span-2 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($bookmakers as $bm)
                <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-5 shadow-lg flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-white text-sm shadow" style="background-color: {{ $bm->primary_color ?? '#013220' }}; border: 1px solid #1b4332;">
                                    {{ substr($bm->name, 0, 2) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-white text-base">{{ $bm->name }}</h4>
                                    <span class="text-xs font-mono text-gray-400">{{ $bm->slug }}</span>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded font-bold {{ $bm->is_active ? 'bg-emerald-950 text-emerald-400 border border-emerald-700' : 'bg-gray-800 text-gray-400' }}">
                                {{ $bm->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </div>

                        <div class="text-xs text-gray-300 space-y-1.5 pt-2 border-t border-[#1b4332]">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Total Slips Linked:</span>
                                <span class="font-bold text-[#DDD06A]">{{ $bm->betslips_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Affiliate Tag:</span>
                                <span class="font-mono text-[#39FF88]">{{ $bm->affiliate_code ?? 'None' }}</span>
                            </div>
                            @if($bm->website_url)
                            <div class="flex justify-between truncate">
                                <span class="text-gray-400">Website:</span>
                                <a href="{{ $bm->website_url }}" target="_blank" class="text-emerald-400 underline truncate max-w-[150px]">{{ $bm->website_url }}</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-[#1b4332] flex items-center justify-between">
                        <form method="POST" action="{{ route('admin.bookmakers.toggle', $bm->id) }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold {{ $bm->is_active ? 'text-amber-400 hover:text-amber-300' : 'text-emerald-400 hover:text-emerald-300' }}">
                                {{ $bm->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        <span class="text-[11px] text-gray-500">ID: {{ $bm->id }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
