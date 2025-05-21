<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\AuthController;

Route::get('/auth',      [AuthController::class, 'redirectToShopify']);

// Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback']);

Route::get('/auth/callback', [AuthController::class, 'handleShopifyCallback'])
     ->name('shopify.callback');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::match(['get','post','options'], 'apps/vcard-app', [VCardController::class, 'handle'])
//      ->middleware(['cors','verify.shopify']);

// GET: must return 200 so Shopify doesn’t show its “third-party” error
Route::get('apps/vcard-app', [VCardController::class, 'show'])
    ->middleware('verify.shopify')
    // no CSRF for GET needed, but explicitly strip it if it runs
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

// POST: save/finalize
Route::post('apps/vcard-app', [VCardController::class, 'store'])
    ->middleware(['cors','verify.shopify'])
    // strip out CSRF so 419 goes away
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);