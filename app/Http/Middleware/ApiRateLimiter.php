<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * Rate limit: 60 requests per minute per IP address for public API endpoints
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'api:' . $request->ip();
        
        // 60 requests per minute
        if (RateLimiter::tooManyAttempts($key, 60)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak permintaan. Silakan coba lagi dalam beberapa saat.',
                'retry_after' => $retryAfter,
            ], 429);
        }
        
        RateLimiter::hit($key, 60); // Expire in 60 seconds
        
        $response = $next($request);
        
        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', 60);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, 60));
        
        return $response;
    }
}
