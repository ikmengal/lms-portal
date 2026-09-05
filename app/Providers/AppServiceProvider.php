<?php

namespace App\Providers;

use App\Models\Role;
use App\Observers\RoleObserver;
use Illuminate\Support\Facades\View;
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
        Role::observe(RoleObserver::class);

        try {
            View::share('site', \App\Models\Setting::allCached());
        } catch (\Throwable) {
            // Settings table not migrated yet (fresh install)
            View::share('site', []);
        }
    }
}
