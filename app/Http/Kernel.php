<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // … your other web middleware …
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'shopify_proxy' => [
            // Trust proxies, etc.
            \App\Http\Middleware\TrustProxies::class,
            \Fruitcake\Cors\HandleCors::class,              // or your custom CORS
            \App\Http\Middleware\VerifyShopifyProxy::class,  // HMAC check
            // **NO** StartSession, VerifyCsrfToken, etc.
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'cors' => \App\Http\Middleware\HandleCors::class,
        'verify.shopify' => \App\Http\Middleware\VerifyShopifyProxy::class,
    ];
}
