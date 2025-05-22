<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyShopifyProxy;

Route::get('/auth', [AuthController::class, 'redirectToShopify']);

Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback'])
     ->name('shopify.callback');

// GET proxy (no sessions, no CSRF—api group is stateless)
Route::get('apps/vcard-app', [VCardController::class, 'show'])
    ->middleware(['api', 'verify.shopify']);

// POST proxy (same: stateless + HMAC check)
Route::post('apps/vcard-app', [VCardController::class, 'store'])
    ->middleware(['api', 'verify.shopify']);