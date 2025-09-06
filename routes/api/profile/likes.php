<?php


use App\Http\Controllers\Api\Profile\LikeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->prefix('profile')->group(function () {
    Route::post('/like/{user}', [LikeController::class, 'likeUser']);
});
