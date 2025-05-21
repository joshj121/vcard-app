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

Route::get('apps/vcard-app', [VCardController::class, 'show'])
     ->middleware('verify.shopify');       // GET → show()

Route::match(['post','options'], 'apps/vcard-app', [VCardController::class, 'store'])
     ->middleware(['cors','verify.shopify']);