<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Hanya admin yang bisa akses panel admin
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses panel ini.');
        }
        
        return $next($request);
    }
}
