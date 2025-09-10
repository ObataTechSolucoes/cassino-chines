<?php

use App\Http\Controllers\Gateway\EzzePayController;
use Illuminate\Support\Facades\Route;

Route::prefix('ezzepay')
    ->group(function () {
        Route::post('qrcode-pix', [EzzePayController::class, 'getQRCodePix']);
        Route::post('consult-status-transaction', [EzzePayController::class, 'consultStatusTransactionPix']);
    });
