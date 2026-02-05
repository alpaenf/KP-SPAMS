<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Pelanggan;
use App\Observers\PelangganObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observer untuk pelanggan
        Pelanggan::observe(PelangganObserver::class);
        
        // Gate untuk akses admin/pengelola
        Gate::define('canAccessFilament', function ($user) {
            return $user->canAccessFilament();
        });
    }
}
