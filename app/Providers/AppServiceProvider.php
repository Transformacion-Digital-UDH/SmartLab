<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Route::middleware(config('jetstream.middleware', ['web']))
            ->group(base_path('routes/jetstream.php'));
        Route::middleware(config('fortify.middleware', ['web']))
            ->group(base_path('routes/fortify.php'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        /**/
        Inertia::share([
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
