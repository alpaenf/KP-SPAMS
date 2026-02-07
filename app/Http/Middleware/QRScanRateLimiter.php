<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QRScanRateLimiter
{
    /**
     * Handle an incoming request untuk QR Scanner
     * 
     * Rate limiting: 
     * - Max 30 scan per menit per IP
     * - Max 5 scan untuk ID pelanggan yang sama dalam 2 menit
     * 
     * Mencegah abuse/spam scanning
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $idPelanggan = $request->input('id_pelanggan');
        
        // Rate limit per IP: Max 30 request per menit
        $ipKey = "qr_scan_ip_{$ip}";
        $ipCount = Cache::get($ipKey, 0);
        
        if ($ipCount >= 30) {
            Log::warning("QR Scanner rate limit exceeded", [
                'ip' => $ip,
                'id_pelanggan' => $idPelanggan,
                'count' => $ipCount
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.',
            ], 429);
        }
        
        Cache::put($ipKey, $ipCount + 1, now()->addMinute());
        
        // Rate limit per pelanggan: Max 5 scan dalam 2 menit
        if ($idPelanggan) {
            $pelangganKey = "qr_scan_pelanggan_{$idPelanggan}";
            $pelangganCount = Cache::get($pelangganKey, 0);
            
            if ($pelangganCount >= 5) {
                Log::warning("QR Scanner pelanggan rate limit exceeded", [
                    'ip' => $ip,
                    'id_pelanggan' => $idPelanggan,
                    'count' => $pelangganCount
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'QR code pelanggan ini baru saja di-scan. Silakan tunggu 2 menit.',
                ], 429);
            }
            
            Cache::put($pelangganKey, $pelangganCount + 1, now()->addMinutes(2));
        }
        
        return $next($request);
    }
}
