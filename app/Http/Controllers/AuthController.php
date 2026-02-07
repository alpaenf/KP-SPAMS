<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Rate limiting: 5 attempts per minute per email
        $key = 'login:' . $request->input('email');
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'message' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }
        
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Clear rate limiter on successful login
            RateLimiter::clear($key);
            
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Cek role pengelola
            if ($user->role === 'pengelola') {
                return redirect()->intended(route('dashboard'));
            }
            
            // Jika bukan pengelola, logout dan tampilkan error
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors([
                'message' => 'Akun ini tidak memiliki akses sebagai pengelola.',
            ]);
        }

        // Increment rate limiter on failed login
        RateLimiter::hit($key, 60);
        
        return back()->withErrors([
            'message' => 'Email atau password salah.',
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
