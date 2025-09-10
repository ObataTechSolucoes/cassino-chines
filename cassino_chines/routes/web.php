<?php

use App\Http\Controllers\BauController;
use App\Models\Game;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\AccountWithdrawController;
use App\Http\Controllers\Api\Profile\WalletController;
use Illuminate\Http\Request;






// Processamento 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|Sme
*/

Route::post('/account_withdraw', [AccountWithdrawController::class, 'store']);
Route::get('/api/musics', [MusicController::class, 'index']);


Route::put('/api/bau/{id}/abrir', [BauController::class, 'abrirBau']);
Route::get('clear', function () {
    Artisan::command('clear', function () {
        Artisan::call('optimize:clear');
        return back();
    });

    return back();
});

// ROTA DE SAQUE 
Route::get('/withdrawal/{id}', [WalletController::class, 'withdrawalFromModal'])->name('withdrawal');

// Callback para saques da CNPay
Route::post('/cnpay/withdraw/callback', function (Request $request) {
    \Illuminate\Support\Facades\Log::info('Callback de saque CNPay recebido', [
        'payload' => $request->all(),
        'headers' => $request->headers->all()
    ]);
    
    try {
        $data = $request->all();
        
        // Verificar se é uma notificação de saque
        if (isset($data['withdraw']) && isset($data['withdraw']['id'])) {
            $withdrawId = $data['withdraw']['id'];
            $status = $data['withdraw']['status'] ?? 'unknown';
            
            // Verificar se o saque foi aprovado
            if (in_array(strtolower($status), ['completed', 'approved', 'success', 'paid'])) {
                \Illuminate\Support\Facades\Log::info('Saque aprovado via callback CNPay', [
                    'transaction_id' => $withdrawId,
                    'status' => $status
                ]);
                
                // FINALIZAR SAQUE E DEBITAR VALOR DO USUÁRIO
                $result = \App\Traits\Gateways\BsPayTrait::finalizeWithdrawalBsPay($withdrawId);
                
                if ($result) {
                    \Illuminate\Support\Facades\Log::info('Saque finalizado e valor debitado com sucesso', [
                        'transaction_id' => $withdrawId
                    ]);
                } else {
                    \Illuminate\Support\Facades\Log::error('Falha ao finalizar saque', [
                        'transaction_id' => $withdrawId
                    ]);
                }
                
                return response()->json(['status' => 'success', 'message' => 'Saque processado']);
            } else {
                \Illuminate\Support\Facades\Log::info('Saque não aprovado ainda', [
                    'transaction_id' => $withdrawId,
                    'status' => $status
                ]);
            }
        }
        
        return response()->json(['status' => 'success', 'message' => 'Callback recebido']);
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Erro ao processar callback de saque CNPay', [
            'error' => $e->getMessage(),
            'payload' => $request->all()
        ]);
        
        return response()->json(['status' => 'error', 'message' => 'Erro interno'], 500);
    }
});

// Callback para saques de afiliados da CNPay
Route::post('/cnpay/affiliate-withdraw/callback', function (Request $request) {
    \Illuminate\Support\Facades\Log::info('Callback de saque de afiliado CNPay recebido', [
        'payload' => $request->all(),
        'headers' => $request->headers->all()
    ]);
    
    try {
        $data = $request->all();
        
        // Verificar se é uma notificação de saque
        if (isset($data['withdraw']) && isset($data['withdraw']['id'])) {
            $withdrawId = $data['withdraw']['id'];
            $status = $data['withdraw']['status'] ?? 'unknown';
            
            // Verificar se o saque foi aprovado
            if (in_array(strtolower($status), ['completed', 'approved', 'success', 'paid'])) {
                \Illuminate\Support\Facades\Log::info('Saque de afiliado aprovado via callback CNPay', [
                    'transaction_id' => $withdrawId,
                    'status' => $status
                ]);
                
                // FINALIZAR SAQUE E DEBITAR VALOR DO USUÁRIO
                $result = \App\Traits\Gateways\BsPayTrait::finalizeAffiliateWithdrawalBsPay($withdrawId);
                
                if ($result) {
                    \Illuminate\Support\Facades\Log::info('Saque de afiliado finalizado e valor debitado com sucesso', [
                        'transaction_id' => $withdrawId
                    ]);
                } else {
                    \Illuminate\Support\Facades\Log::error('Falha ao finalizar saque de afiliado', [
                        'transaction_id' => $withdrawId
                    ]);
                }
                
                return response()->json(['status' => 'success', 'message' => 'Saque de afiliado processado']);
            } else {
                \Illuminate\Support\Facades\Log::info('Saque de afiliado não aprovado ainda', [
                    'transaction_id' => $withdrawId,
                    'status' => $status
                ]);
            }
        }
        
        return response()->json(['status' => 'success', 'message' => 'Callback recebido']);
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Erro ao processar callback de saque de afiliado CNPay', [
            'error' => $e->getMessage(),
            'payload' => $request->all()
        ]);
        
        return response()->json(['status' => 'error', 'message' => 'Erro interno'], 500);
    }
});

// GAMES PROVIDER

include_once(__DIR__ . '/groups/provider/playFiver.php'); 


// GATEWAYS
include_once(__DIR__ . '/groups/gateways/suitpay.php');
include_once(__DIR__ . '/groups/gateways/bspay.php');
include_once(__DIR__ . '/groups/gateways/ezzepay.php');
include_once(__DIR__ . '/groups/gateways/digitopay.php');
include_once(__DIR__ . '/groups/gateways/cnpay.php');

/// SOCIAL
include_once(__DIR__ . '/groups/auth/social.php');

// APP
include_once(__DIR__ . '/groups/layouts/app.php');










