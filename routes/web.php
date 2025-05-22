<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyShopifyProxy;

Route::get('/auth',      [AuthController::class, 'redirectToShopify']);

// Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback']);

Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback'])
     ->name('shopify.callback');

//
// Shopify App-Proxy GET: must return 200 OK
//
Route::get('apps/vcard-app', [VCardController::class, 'show'])
    // Skip Laravel’s web/session/CSRF entirely:
    ->withoutMiddleware([
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ])
    // Enforce Shopify HMAC on every call:
    ->middleware(VerifyShopifyProxy::class);

//
// Shopify App-Proxy POST: your vCard save
//
Route::post('apps/vcard-app', [VCardController::class, 'store'])
    ->withoutMiddleware([
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ])
    ->middleware(VerifyShopifyProxy::class);