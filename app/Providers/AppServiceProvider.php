<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\Router;

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
        Schema::defaultStringLength(191);
        date_default_timezone_set(config('app.timezone'));

        // Ensure access.token middleware alias is registered (defensive)
        $this->app->booted(function () {
            /** @var Router $router */
            $router = $this->app->make(Router::class);
            $router->aliasMiddleware('access.token', \App\Http\Middleware\VerifyAccessSyncToken::class);
        });

    }
}
