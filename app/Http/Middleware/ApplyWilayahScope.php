<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ApplyWilayahScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Share user info to all views
        if ($user) {
            View::share('currentUserRole', $user->role);
            View::share('currentUserWilayah', $user->wilayah);
            View::share('isAdmin', $user->isAdmin());
            View::share('isPenarik', $user->isPenarik());
        }
        
        return $next($request);
    }
}
