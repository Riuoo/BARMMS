<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Define a gate for dashboard access
        Gate::define('access-dashboard', function ($user) {
            return in_array(session('user_role'), ['secretary', 'captain']);
        });        
    }
}
