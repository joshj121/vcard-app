<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCardController;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get','post'], 'apps/vcard-app', [VCardController::class, 'handle'])
     ->middleware('verify.shopify');
