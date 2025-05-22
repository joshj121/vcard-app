<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyShopifyProxy;

Route::get('/auth',      [AuthController::class, 'redirectToShopify']);

// Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback']);

Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback'])
     ->name('shopify.callback');

// 1️⃣ GET proxy endpoint — no sessions, only HMAC
Route::get('apps/vcard-app', [VCardController::class, 'show'])
    // drop every session/cookie/CSRF middleware
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \App\Http\Middleware\EncryptCookies::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ])
    // just run the Shopify HMAC check
    ->middleware(VerifyShopifyProxy::class);

// 2️⃣ POST proxy endpoint — same stripping + HMAC
Route::post('apps/vcard-app', [VCardController::class, 'store'])
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \App\Http\Middleware\EncryptCookies::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ])
    ->middleware(VerifyShopifyProxy::class);