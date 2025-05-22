<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyShopifyProxy;

Route::get('/auth', [AuthController::class, 'redirectToShopify']);

Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback'])
     ->name('shopify.callback');

Route::get('/', [VCardController::class, 'show'])
    ->withoutMiddleware('web')
    ->middleware(VerifyShopifyProxy::class);

Route::post('/', [VCardController::class, 'store'])
    ->withoutMiddleware('web')
    ->middleware(VerifyShopifyProxy::class);

Route::middleware(VerifyShopifyProxy::class)
     // Remove the entire `web` group (no sessions, no CSRF, no cookies)
     ->withoutMiddleware('web')
     ->group(function () {
         // Show (GET) must return 200 so Shopify doesn’t render an error
         Route::get('apps/vcard-app', [VCardController::class, 'show']);
         // Save (POST) writes the vCard data
         Route::post('apps/vcard-app', [VCardController::class, 'store']);
     });