<?php

use App\Http\Controllers\Api\Missions\MissionUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->group(function () {
    Route::apiResource('missionsusers', MissionUserController::class);
});
