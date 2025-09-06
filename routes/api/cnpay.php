<?php

use App\Http\Controllers\Api\Wallet\CNPayDepositController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CNPay API Routes
|--------------------------------------------------------------------------
|
| Rotas para integração com o gateway de pagamento CNPay
|
*/

Route::prefix('cnpay')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        // Criar depósito via CNPay
        Route::post('/deposit', [CNPayDepositController::class, 'createDeposit'])
            ->name('cnpay.deposit.create');

        // Verificar status do depósito
        Route::get('/deposit/{transactionId}/status', [CNPayDepositController::class, 'checkStatus'])
            ->name('cnpay.deposit.status')
            ->where('transactionId', '[A-Z0-9]+');
    });

    // Webhook do CNPay (sem autenticação)
    Route::post('/webhook', [CNPayDepositController::class, 'webhook'])
        ->name('cnpay.webhook');
});

// Rota de teste (apenas para desenvolvimento)
if (app()->environment('local', 'development')) {
    Route::get('/cnpay/test', function () {
        return response()->json([
            'message' => 'CNPay API funcionando',
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment()
        ]);
    })->name('cnpay.test');
}
