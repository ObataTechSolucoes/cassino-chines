<?php

use App\Http\Controllers\Api\Missions\MissionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->group(function () {
    Route::apiResource('missions', MissionController::class);
});
