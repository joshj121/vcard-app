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

Route::match(['get','post','options'], 'apps/vcard-app', [VCardController::class, 'handle'])
     ->middleware(['cors','verify.shopify']);