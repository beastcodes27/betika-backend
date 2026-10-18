<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. You do not have permission to access this resource.',
                    'required_roles' => $roles,
                ], 403);
            }
            abort(403, 'Access denied. You do not have permissions for this section.');
        }

        // If tipster role, verify account status
        if ($user->isTipster()) {
            $profile = $user->tipsterProfile;
            if ($profile && $profile->status === 'suspended') {
                abort(403, 'Your tipster account has been suspended by the Super Administrator.');
            }
        }

        return $next($request);
    }
}
