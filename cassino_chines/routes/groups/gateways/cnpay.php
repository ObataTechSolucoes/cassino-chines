<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gateway\CNPayController;

Route::prefix('cnpay')
    ->group(function () {
        Route::post('qrcode-pix', [CNPayController::class, 'getQRCodePix']);
        Route::any('callback', [CNPayController::class, 'callbackMethod']);
        Route::post('payment', [CNPayController::class, 'callbackMethodPayment']);
        Route::post('consult-status-transaction', [CNPayController::class, 'consultStatusTransactionPix']);

        Route::middleware(['admin.filament', 'admin'])
            ->group(function () {
                // Rotas administrativas podem ser adicionadas aqui no futuro
            });
    });
