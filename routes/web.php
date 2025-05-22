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
     ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
     ->group(function() {
    // Shopify “ping” must return 200
    Route::get('apps/vcard-app',   [VCardController::class, 'show']);
    // Save/Finalize POST
    Route::post('apps/vcard-app',  [VCardController::class, 'store']);
});