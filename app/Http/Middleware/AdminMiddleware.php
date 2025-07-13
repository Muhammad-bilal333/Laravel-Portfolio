<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
            if (!Auth::check() || Auth::user()->role_id  !== 1) {
                // Auth::logout(); // Log out the user
            return redirect()->route('showAdminLoginForm')->withErrors(['access' => 'You must be logged in.']);
        }

        return $next($request);
    }   
}

