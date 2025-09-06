<?php

use App\Http\Controllers\Api\Profile\RecentsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->prefix('profile')->group(function () {
    Route::prefix('recents')->group(function () {
        Route::get('/', [RecentsController::class, 'index']);
    });
});
