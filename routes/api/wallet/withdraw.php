<?php

use App\Http\Controllers\Api\Profile\WalletController;
use App\Http\Controllers\Api\Wallet\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.jwt')->prefix('wallet')->group(function () {
    Route::prefix('withdraw')->group(function () {
        Route::get('/', [WithdrawController::class, 'index']);
        Route::post('/request', [WalletController::class, 'requestWithdrawal']);
    });
});
