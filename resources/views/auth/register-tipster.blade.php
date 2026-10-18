<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipster Application | BetWinner Platform</title>
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
    <div class="max-w-lg w-full bg-forest-card border border-forest-border rounded-2xl p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-xl bg-gold/15 border border-gold/30 text-gold font-black text-xl mx-auto flex items-center justify-center mb-2">
                🌟
            </div>
            <h1 class="text-2xl font-black tracking-wide">JOIN TIPSTER NETWORK</h1>
            <p class="text-xs text-gray-300">Publish your verified bookmaker slips to thousands of bettors</p>
        </div>

        @if($errors->any())
            <div class="bg-red-950/50 border border-red-500/30 text-red-200 text-xs p-3 rounded-xl space-y-1">
                @foreach($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register.tipster.post') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white" placeholder="e.g. Ally Mkwawa">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Tipster Handle</label>
                    <input type="text" name="handle" value="{{ old('handle') }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white" placeholder="@master_odds">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white" placeholder="you@domain.com">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Phone (M-Pesa)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white" placeholder="+255 7XX XXX XXX">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Prediction Specialty</label>
                <input type="text" name="specialty" value="{{ old('specialty') }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white" placeholder="e.g. Over 2.5 Goals, Premier League Banker Doubles">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Short Bio</label>
                <textarea name="bio" rows="2" class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2 text-sm text-white" placeholder="Tell us about your analysis method and win streak...">{{ old('bio') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white" placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gold hover:bg-gold/90 text-forest-dark font-black text-sm rounded-xl transition shadow-lg mt-2">
                Submit Tipster Application
            </button>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs text-gray-400 hover:text-white">Already have an account? Sign in &rarr;</a>
            </div>
        </form>
    </div>
</body>
</html>
