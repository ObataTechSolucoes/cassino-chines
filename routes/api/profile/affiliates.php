<?php

use App\Http\Controllers\Api\Profile\AffiliateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->prefix('profile')->group(function () {
    Route::prefix('affiliates')->group(function () {
        Route::get('/', [AffiliateController::class, 'index']);
        Route::get('/generate', [AffiliateController::class, 'generateCode']);
        Route::post('/request', [AffiliateController::class, 'makeRequest']);
    });
});
