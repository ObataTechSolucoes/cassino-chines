<?php

use App\Http\Controllers\Gateway\CNPayController;
use Illuminate\Support\Facades\Route;

Route::prefix('cnpay')
    ->group(function () {
        Route::post('qrcode-pix', [CNPayController::class, 'getQRCodePix']);
        Route::post('consult-status-transaction', [CNPayController::class, 'consultStatusTransactionPix']);
    });
