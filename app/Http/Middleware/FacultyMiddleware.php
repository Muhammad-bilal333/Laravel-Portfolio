<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacultyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // if (!Auth::check() ) {
        if (!Auth::check() ) {
        // if (!Auth::check() || !in_array(Auth::user()->role_id, [2, 5, 6, 7, 8])) {
            // Auth::logout(); // Log out the user
            return redirect()->route('showFacultyLoginForm')
                             ->withErrors(['access' => 'You must be logged in.']);
        }
    
        return $next($request);
    }  
}
