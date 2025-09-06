<?php

use App\Http\Controllers\Api\Settings\BannerController;
use Illuminate\Support\Facades\Route;


Route::prefix('settings')->group(function () {
    Route::prefix('banners')
        ->group(function () {
            Route::get('/', [BannerController::class, 'index']);
        });
});
