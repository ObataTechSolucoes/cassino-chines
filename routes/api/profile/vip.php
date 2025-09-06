<?php

use App\Http\Controllers\Api\Profile\VipController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->prefix('profile')->group(function () {
    Route::prefix('vip')->group(function () {
        Route::get('/', [VipController::class, 'index']);
    });
});
