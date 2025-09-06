<?php

use App\Http\Controllers\Api\Profile\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->prefix('profile')->group(function () {
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
    });
});
