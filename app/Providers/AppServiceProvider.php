<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
        //
        /**/Inertia::share([
            'auth' => function () {
                if (Auth::check()) {
                    return [
                        'user' => Auth::user(),
                        'roles' => Auth::user()->getRoleNames(), // ["admin", "editor"]
                        'permissions' => Auth::user()->getAllPermissions()->pluck('name'), // ["edit articles", "delete articles"]
                    ];
                }
                return null;
            },
        ]);
    }
}
