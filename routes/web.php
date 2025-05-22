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

Route::middleware('shopify_proxy')->group(function() {
    // GET must return 200 so Shopify doesn’t show its “third-party error”
    Route::get('apps/vcard-app',   [VCardController::class, 'show']);

    // POST carries your vCard JSON → writes metafields
    Route::post('apps/vcard-app',  [VCardController::class, 'store']);
});