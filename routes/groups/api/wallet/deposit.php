<?php

use App\Http\Controllers\Api\Wallet\DepositController;
use App\Http\Controllers\Api\Wallet\GatewaySelectionController;
use App\Http\Controllers\Api\Wallet\TransactionStatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('deposit')
    ->group(function ()
    {
        Route::get('/', [DepositController::class, 'index']);
        Route::post('/payment', [DepositController::class, 'submitPayment']);
        
        // Rotas para seleção de gateway
        Route::get('/gateways', [GatewaySelectionController::class, 'listGateways']);
        Route::post('/select-gateway', [GatewaySelectionController::class, 'selectGateway']);
        
        // Rotas para verificação de status de transações
        Route::get('/transaction-status', [TransactionStatusController::class, 'checkStatus']);
        Route::get('/transactions', [TransactionStatusController::class, 'listTransactions']);
    });

