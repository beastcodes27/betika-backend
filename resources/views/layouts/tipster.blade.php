<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tipster Portal') | BetWinner Tipster Workspace</title>
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
    </style>
</head>
<body class="min-h-screen bg-forest flex flex-col">

    <!-- Tipster Navbar -->
    <header class="h-20 bg-forest-dark border-b border-forest-border px-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-neon/15 border border-neon/30 flex items-center justify-center font-black text-neon text-xl">
                BW
            </div>
            <div>
                <h1 class="font-extrabold text-white tracking-wider text-base">TIPSTER PUBLISHING PORTAL</h1>
                <p class="text-[11px] text-gray-400">Publish Bookmaker Slips with Betika, SportyBet, 1xBet, MozzartBet, Betway</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-forest-card px-3 py-1.5 rounded-xl border border-forest-border text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-neon"></span>
                <span class="font-bold text-gray-300">Tipster:</span>
                <span class="font-black text-gold">{{ auth()->user()->name ?? 'Captain Odds' }}</span>
            </div>

            <a href="{{ route('tipster.slips.create') }}" class="px-4 py-2 bg-neon hover:bg-neon/90 text-forest-dark font-black text-xs rounded-xl transition shadow flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Publish New Slip
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1.5 text-xs font-bold text-red-400 hover:text-red-300">Logout</button>
            </form>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-6xl mx-auto w-full p-8 flex-1 space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-neon/10 border border-neon/30 text-neon flex items-center justify-between text-sm font-semibold">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-950/40 border border-red-500/30 text-red-300 flex items-center justify-between text-sm font-semibold">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-forest-border p-6 text-center text-xs text-gray-400">
        &copy; 2026 BetWinner Platform. Tipster profiles and bookmaker publications are strictly verified by Super Administration.
    </footer>

</body>
</html>
