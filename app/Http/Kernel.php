<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // Global middleware (optional)
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
    ];

    // Middleware groups for web and api routes
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    // Aliases for route middleware
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'prevent_back' => \App\Http\Middleware\PreventBack::class,
        'access.token' => \App\Http\Middleware\VerifyAccessSyncToken::class,
    ];

    // Define route middleware (for specific routes like the 'auth' middleware)
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'prevent_back' => \App\Http\Middleware\PreventBack::class,
        'access.token' => \App\Http\Middleware\VerifyAccessSyncToken::class,
        // Add more route middleware here as needed
    ];
}
