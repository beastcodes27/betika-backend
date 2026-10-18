<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin') | BetWinner Administration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: {
                            DEFAULT: '#013220',
                            dark: '#002416',
                            card: '#062E1B',
                            inner: '#0B3B24',
                            border: 'rgba(57, 255, 136, 0.15)',
                        },
                        neon: '#39FF88',
                        gold: '#DDD06A',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #013220; color: #FFFFFF; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel { background: rgba(6, 46, 27, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(57, 255, 136, 0.15); }
        .glow-gold { box-shadow: 0 0 15px rgba(221, 208, 106, 0.25); }
        .glow-neon { box-shadow: 0 0 15px rgba(57, 255, 136, 0.25); }
    </style>
</head>
<body class="min-h-screen bg-forest flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-forest-dark border-r border-forest-border flex flex-col justify-between shrink-0">
        <div>
            <!-- Logo Header -->
            <div class="h-20 flex items-center px-6 border-b border-forest-border gap-3">
                <div class="w-10 h-10 rounded-xl bg-neon/15 border border-neon/30 flex items-center justify-center font-black text-neon text-xl">
                    BW
                </div>
                <div>
                    <h1 class="font-extrabold text-white tracking-wider text-base leading-none">BETWINNER</h1>
                    <span class="text-[10px] font-bold text-gold uppercase tracking-widest">SUPER ADMIN PANEL</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-neon/15 text-neon border border-neon/30' : 'text-gray-300 hover:bg-forest-card hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard & Analytics
                </a>

                <a href="{{ route('admin.tipsters.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.tipsters.*') ? 'bg-neon/15 text-neon border border-neon/30' : 'text-gray-300 hover:bg-forest-card hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Tipsters Manager
                    </div>
                    @php $pendingTipsters = \App\Models\TipsterProfile::where('status', 'pending')->count(); @endphp
                    @if($pendingTipsters > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-black bg-gold text-forest-dark">{{ $pendingTipsters }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.betslips.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.betslips.*') ? 'bg-neon/15 text-neon border border-neon/30' : 'text-gray-300 hover:bg-forest-card hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Betslips Oversight
                </a>

                <a href="{{ route('admin.featured.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.featured.*') ? 'bg-neon/15 text-neon border border-neon/30' : 'text-gray-300 hover:bg-forest-card hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    Top Big Game & Poll
                </a>

                <a href="{{ route('admin.ads.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.ads.*') ? 'bg-neon/15 text-neon border border-neon/30' : 'text-gray-300 hover:bg-forest-card hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    Ads Board Banners
                </a>

                <a href="{{ route('admin.vip.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.vip.*') ? 'bg-neon/15 text-neon border border-neon/30' : 'text-gray-300 hover:bg-forest-card hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    VIP Subscriptions & Tshs
                </a>

                <a href="{{ route('admin.bookmakers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.bookmakers.*') ? 'bg-neon/15 text-neon border border-neon/30' : 'text-gray-300 hover:bg-forest-card hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Betting Companies
                </a>
            </nav>
        </div>

        <!-- User Section Footer -->
        <div class="p-4 border-t border-forest-border">
            <div class="flex items-center justify-between bg-forest-card p-3 rounded-xl border border-forest-border mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gold/20 text-gold flex items-center justify-center font-bold text-sm">
                        SA
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Super Admin' }}</p>
                        <p class="text-[10px] text-gray-400 truncate">admin@betwinner.co.tz</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-gold/20 text-gold border border-gold/40">SUPER</span>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-bold text-red-400 bg-red-950/30 hover:bg-red-950/60 border border-red-500/20 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout System
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Bar -->
        <header class="h-20 bg-forest-dark/80 backdrop-blur border-b border-forest-border px-8 flex items-center justify-between sticky top-0 z-30">
            <div>
                <h2 class="text-xl font-extrabold text-white">@yield('page_title', 'Overview')</h2>
                <p class="text-xs text-gray-400">@yield('page_subtitle', 'BetWinner Central Super Administration Platform')</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="bg-forest-card border border-forest-border px-3 py-1.5 rounded-lg flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-neon animate-pulse"></div>
                    <span class="text-xs font-bold text-gray-200">Currency: <strong class="text-gold">Tshs</strong></span>
                </div>

                <a href="{{ route('tipster.dashboard') }}" target="_blank" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-forest-dark bg-neon hover:bg-neon/90 transition shadow">
                    Tipster Portal Demo &rarr;
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="p-8 space-y-6 flex-1">
            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-neon/10 border border-neon/30 text-neon flex items-center justify-between text-sm font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-950/40 border border-red-500/30 text-red-300 flex items-center justify-between text-sm font-semibold">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
