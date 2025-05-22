<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\AuthController;

Route::get('/auth',      [AuthController::class, 'redirectToShopify']);

// Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback']);

Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback'])
     ->name('shopify.callback');

// Route::get('apps/vcard-app',   [VCardController::class, 'show'])
//      ->middleware('verify.shopify');

// Route::post('apps/vcard-app',  [VCardController::class, 'store'])
//      ->middleware(['cors','verify.shopify']);

Route::middleware(['cors','verify.shopify'])
    ->withoutMiddleware([
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \App\Http\Middleware\EncryptCookies::class,
    ])
    ->group(function() {
        // GET → keep Shopify happy
        Route::get('apps/vcard-app', [VCardController::class, 'show']);

        // POST → save the vCard
        Route::post('apps/vcard-app', [VCardController::class, 'store']);
    });