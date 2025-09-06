<?php

use App\Http\Controllers\Api\Profile\AffiliateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->prefix('profile')->group(function () {
    Route::post('verificar-bau/{bauId}', [AffiliateController::class, 'verificarBau']);
    Route::post('abrir-bau/{bauId}', [AffiliateController::class, 'abrirBau']);
});
