<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class QecMiddleware
{
    public function handle($request, Closure $next)
    {
        // Only allow QEC users (role_id == 4)
        if (Auth::check() && Auth::user()->role_id == 4) {
            return $next($request);
        }
        abort(403, 'Unauthorized');
    }
} 