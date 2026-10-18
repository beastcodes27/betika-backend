<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TipsterProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isTipster()) {
                return redirect()->route('tipster.dashboard');
            }
            return redirect('/');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated. Contact Super Admin.']);
            }

            if ($user->isSuperAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome back, Super Admin!');
            }

            if ($user->isTipster()) {
                $profile = $user->tipsterProfile;
                if ($profile && $profile->status === 'suspended') {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Your tipster account has been suspended by the administrator.']);
                }
                return redirect()->intended(route('tipster.dashboard'))
                    ->with('success', 'Welcome back to your Tipster Portal!');
            }

            return redirect()->intended('/')
                ->with('info', 'Logged in as regular user.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterTipster(): View
    {
        return view('auth.register-tipster');
    }

    public function registerTipster(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'handle' => ['required', 'string', 'max:50', 'unique:tipster_profiles,handle'],
            'specialty' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => 'tipster',
            'is_active' => true,
            'status' => 'pending',
            'password' => Hash::make($validated['password']),
        ]);

        $colors = ['#3B82F6', '#10B981', '#EC4899', '#8B5CF6', '#F59E0B'];
        $handle = str_starts_with($validated['handle'], '@') ? $validated['handle'] : '@' . $validated['handle'];

        TipsterProfile::create([
            'user_id' => $user->id,
            'handle' => $handle,
            'display_name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'specialty' => $validated['specialty'],
            'avatar_bg' => $colors[array_rand($colors)],
            'win_rate' => 75.00,
            'badge' => 'NEW TIPSTER 🌟',
            'streak' => '1W Streak',
            'followers_count' => 0,
            'is_verified' => false,
            'can_publish' => false,
            'status' => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Tipster application submitted! The Super Admin will review and activate your publishing rights.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}
