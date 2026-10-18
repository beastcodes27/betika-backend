<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BetWinner Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: { DEFAULT: '#013220', dark: '#002416', card: '#062E1B', border: 'rgba(57, 255, 136, 0.15)' },
                        neon: '#39FF88',
                        gold: '#DDD06A',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-forest flex items-center justify-center p-6 text-white font-sans">
    <div class="max-w-md w-full bg-forest-card border border-forest-border rounded-2xl p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-neon/15 border border-neon/30 text-neon font-black text-2xl mx-auto flex items-center justify-center mb-3">
                BW
            </div>
            <h1 class="text-2xl font-black tracking-wide">BETWINNER ACCESS</h1>
            <p class="text-xs text-gray-300">Sign in to Super Admin or Tipster Account</p>
        </div>

        @if($errors->any())
            <div class="bg-red-950/50 border border-red-500/30 text-red-200 text-xs p-3 rounded-xl space-y-1">
                @foreach($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="bg-neon/15 border border-neon/30 text-neon text-xs p-3 rounded-xl font-bold">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-forest-dark border border-forest-border focus:border-neon focus:ring-1 focus:ring-neon rounded-xl px-4 py-3 text-sm text-white placeholder-gray-500 transition" placeholder="e.g. admin@betwinner.co.tz">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-forest-dark border border-forest-border focus:border-neon focus:ring-1 focus:ring-neon rounded-xl px-4 py-3 text-sm text-white placeholder-gray-500 transition" placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-forest-dark border-forest-border text-neon focus:ring-neon">
                    <span class="text-gray-300">Remember session</span>
                </label>
                <a href="{{ route('register.tipster') }}" class="text-gold font-bold hover:underline">Apply as Tipster &rarr;</a>
            </div>

            <button type="submit" class="w-full py-3.5 bg-neon hover:bg-neon/90 text-forest-dark font-black text-sm rounded-xl transition shadow-lg">
                Enter Portal
            </button>
        </form>

        <div class="pt-4 border-t border-forest-border text-center text-xs text-gray-400 space-y-1">
            <p><strong>Super Admin Demo:</strong> admin@betwinner.co.tz / Admin@2026!</p>
            <p><strong>Tipster Demo:</strong> captain@betwinner.co.tz / Tipster@2026!</p>
        </div>
    </div>
</body>
</html>
