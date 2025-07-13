<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
            if (!Auth::check() || Auth::user()->role_id !== 3) {
                Auth::logout(); // Log out the user
            return redirect()->route('showStudentLoginForm')->withErrors(['access' => 'You must be logged in.']);
        }

        return $next($request);
    }  
}
