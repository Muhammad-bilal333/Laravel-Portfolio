<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('showAdminLoginForm')->withErrors(['access' => 'You must be logged in.']);
        }

        // Get authenticated user
        $user = Auth::user();

        // Check if user role matches
        if ($user->role_id != $role) {
            return redirect()->route('showAdminLoginForm')->withErrors(['access' => 'Unauthorized Access!']);
        }

        return $next($request);
    }
}
