<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(100);
        Relation::enforceMorphMap([
            'subject'                       => "App\Models\Subject",
            'custom'                        => "App\Models\CustomTimetableItems",
            'App\Models\User'               => 'App\Models\User',
            'App\Models\AccountApplication' => 'App\Models\AccountApplication',
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Implicitly grant "Super-Admin" role all permission checks using can()
        Gate::after(function ($user, $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });
    }
}
