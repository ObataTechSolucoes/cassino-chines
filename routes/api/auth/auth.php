<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Public endpoints
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forget-password', [AuthController::class, 'submitForgetPassword']);
    Route::post('reset-password/{token}', [AuthController::class, 'submitResetPassword']);

    // Protected endpoints
    Route::middleware('auth.jwt')->group(function () {
        Route::get('verify', [AuthController::class, 'verify']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('me', [AuthController::class, 'me']);
    });
});
