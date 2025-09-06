<?php

use App\Http\Controllers\Api\Search\SearchGameController;
use Illuminate\Support\Facades\Route;

Route::prefix('search')->group(function () {
    Route::get('/games', [SearchGameController::class, 'index']);
});
