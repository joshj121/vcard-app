<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyShopifyProxy;

Route::get('/auth', [AuthController::class, 'redirectToShopify']);

Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback'])
     ->name('shopify.callback');

// All requests to /apps/vcard-app should be stateless (API) + HMAC‐checked:
Route::get('apps/vcard-app', [VCardController::class, 'show'])
    ->middleware([
        'api',                                 // Laravel’s stateless group (no CSRF/session)
        VerifyShopifyProxy::class,             // your HMAC signature check
    ]);

Route::post('apps/vcard-app', [VCardController::class, 'store'])
    ->middleware([
        'api',
        VerifyShopifyProxy::class,
    ]);