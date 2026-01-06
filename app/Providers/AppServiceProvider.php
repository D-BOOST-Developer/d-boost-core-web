<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Define Gates for authorization
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('owner', function ($user) {
            return $user->isOwner() || $user->isAdmin();
        });

        Gate::define('staff', function ($user) {
            return $user->isStaff() || $user->isOwner() || $user->isAdmin();
        });
    }
}
