<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentPengelolaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Hanya pengelola yang bisa akses panel pengelola
        if (!$user || $user->role !== 'pengelola') {
            abort(403, 'Akses ditolak. Hanya pengelola yang dapat mengakses panel ini.');
        }
        
        return $next($request);
    }
}
