<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Traits\Gateways\CNPayTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TransactionStatusController extends Controller
{
    use CNPayTrait;

    /**
     * Consultar status de transação por gateway
     */
    public function checkStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'payment_id' => 'required|string',
                'gateway' => 'required|string|in:suitpay,bspay,digitopay,ezzepay,cnpay'
            ]);

            $paymentId = $request->payment_id;
            $gateway = $request->gateway;

            // Buscar transação no banco
            $transaction = Transaction::where('payment_id', $paymentId)->first();
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'error' => 'Transação não encontrada'
                ], 404);
            }

            // Verificar status baseado no gateway
            switch ($gateway) {
                case 'cnpay':
                    return $this->checkCNPayStatus($paymentId, $transaction);
                    
                case 'suitpay':
                case 'bspay':
                case 'digitopay':
                case 'ezzepay':
                    // Para outros gateways, retornar status do banco
                    return response()->json([
                        'success' => true,
                        'status' => $this->getStatusText($transaction->status),
                        'status_code' => $transaction->status,
                        'gateway' => $gateway,
                        'payment_id' => $paymentId,
                        'amount' => $transaction->price,
                        'currency' => $transaction->currency,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at
                    ]);
                    
                default:
                    return response()->json([
                        'success' => false,
                        'error' => 'Gateway não suportado'
                    ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status da transação: ' . $e->getMessage(), [
                'payment_id' => $request->payment_id ?? 'não informado',
                'gateway' => $request->gateway ?? 'não informado'
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno ao verificar status da transação'
            ], 500);
        }
    }

    /**
     * Consultar status específico do CNPay
     */
    private function checkCNPayStatus(string $paymentId, Transaction $transaction): JsonResponse
    {
        try {
            Log::info('TransactionStatusController - checkCNPayStatus iniciado', [
                'payment_id' => $paymentId,
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'current_status' => $transaction->status,
                'timestamp' => now()->toISOString()
            ]);

            // Verificar status na API do CNPay
            Log::info('TransactionStatusController - Chamando checkCNPayPaymentStatus', [
                'payment_id' => $paymentId,
                'gateway' => 'cnpay'
            ]);
            
            $status = self::checkCNPayPaymentStatus($paymentId);
            
            Log::info('TransactionStatusController - Resposta do checkCNPayPaymentStatus', [
                'payment_id' => $paymentId,
                'status_response' => $status,
                'success' => $status['success'] ?? false,
                'error' => $status['error'] ?? null
            ]);
            
            if ($status['success']) {
                // Atualizar status no banco se necessário
                if ($status['status'] !== $transaction->status) {
                    Log::info('TransactionStatusController - Atualizando status da transação', [
                        'payment_id' => $paymentId,
                        'old_status' => $transaction->status,
                        'new_status' => $status['status'],
                        'transaction_id' => $transaction->id
                    ]);
                    
                    $transaction->update([
                        'status' => $status['status'],
                        'gateway_response' => json_encode($status)
                    ]);
                } else {
                    Log::info('TransactionStatusController - Status da transação não mudou', [
                        'payment_id' => $paymentId,
                        'current_status' => $transaction->status,
                        'api_status' => $status['status']
                    ]);
                }

                Log::info('TransactionStatusController - Retornando status atualizado', [
                    'payment_id' => $paymentId,
                    'final_status' => $status['status']
                ]);

                return response()->json([
                    'success' => true,
                    'status' => $status['status'],
                    'gateway' => 'cnpay',
                    'payment_id' => $paymentId,
                    'amount' => $status['amount'] ?? $transaction->price,
                    'currency' => $status['currency'] ?? $transaction->currency,
                    'created_at' => $status['created_at'] ?? $transaction->created_at,
                    'updated_at' => $status['updated_at'] ?? $transaction->updated_at,
                    'original_status' => $status['original_status'] ?? null
                ]);
            }

            // Se não conseguiu verificar na API, retornar status do banco
            Log::warning('TransactionStatusController - Falha na API, retornando status do banco', [
                'payment_id' => $paymentId,
                'api_error' => $status['error'] ?? 'erro desconhecido',
                'fallback_status' => $transaction->status
            ]);

            return response()->json([
                'success' => true,
                'status' => $this->getStatusText($transaction->status),
                'status_code' => $transaction->status,
                'gateway' => 'cnpay',
                'payment_id' => $paymentId,
                'amount' => $transaction->price,
                'currency' => $transaction->currency,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'note' => 'Status obtido do banco local (API indisponível)'
            ]);

        } catch (\Exception $e) {
            Log::error('TransactionStatusController - Erro ao verificar status CNPay', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id
            ]);

            // Em caso de erro, retornar status do banco
            Log::info('TransactionStatusController - Retornando status do banco devido a erro', [
                'payment_id' => $paymentId,
                'fallback_status' => $transaction->status
            ]);

            return response()->json([
                'success' => true,
                'status' => $this->getStatusText($transaction->status),
                'status_code' => $transaction->status,
                'gateway' => 'cnpay',
                'payment_id' => $paymentId,
                'amount' => $transaction->price,
                'currency' => $transaction->currency,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'note' => 'Status obtido do banco local (erro na API)'
            ]);
        }
    }

    /**
     * Consultar status de transação por token
     * Endpoint: /api/transaction/status/by-token
     */
    public function checkStatusByToken(Request $request): JsonResponse
    {
        try {
            // Log detalhado do request para debugging
            Log::info('TransactionStatusController - Request recebido em checkStatusByToken', [
                'all_data' => $request->all(),
                'query_params' => $request->query(),
                'post_data' => $request->post(),
                'headers' => $request->headers->all(),
                'method' => $request->method(),
                'url' => $request->url(),
                'full_url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);

            // Tentar obter token de diferentes formas (incluindo Axios params)
            $token = $request->input('token') ?? 
                     $request->query('token') ?? 
                     $request->get('token') ?? 
                     $request->token ?? 
                     $request->input('params.token') ??  // Para Axios params
                     $request->get('params.token') ??    // Para Axios params
                     null;

            Log::info('TransactionStatusController - Token extraído', [
                'token_from_input' => $request->input('token'),
                'token_from_query' => $request->query('token'),
                'token_from_get' => $request->get('token'),
                'token_from_request' => $request->token,
                'token_final' => $token
            ]);

                    // Validação do token
        if (empty($token)) {
            Log::warning('TransactionStatusController - Token não fornecido');
            return response()->json([
                'success' => false,
                'error' => 'Token é obrigatório',
                'instructions' => [
                    '1. Forneça o token da transação',
                    '2. Exemplo: /api/transaction/status/by-token?token=TOKEN_AQUI'
                ]
            ], 400);
        }

            // Buscar transação apenas pelo token (sem autenticação)
            $transaction = Transaction::where('token', $token)->first();

            if (!$transaction) {
                Log::warning('TransactionStatusController - Transação não encontrada por token', [
                    'token' => $token
                ]);

                return response()->json([
                    'error' => 'Transação não encontrada.',
                ], 404);
            }

            Log::info('TransactionStatusController - Transação encontrada por token', [
                'transaction_id' => $transaction->id,
                'payment_id' => $transaction->payment_id,
                'gateway_name' => $transaction->gateway_name,
                'status' => $transaction->status
            ]);

            // A transação foi encontrada, agora processar o status

            // Verificar status baseado no gateway
            switch ($transaction->gateway_name) {
                case 'cnpay':
                    return $this->checkCNPayStatusByToken($transaction);
                    
                case 'suitpay':
                case 'bspay':
                case 'digitopay':
                case 'ezzepay':
                    // Para outros gateways, retornar status do banco
                    return response()->json([
                        'success' => true,
                        'status' => $this->getStatusText($transaction->status),
                        'status_code' => $transaction->status,
                        'gateway' => $transaction->gateway_name,
                        'payment_id' => $transaction->payment_id,
                        'amount' => $transaction->price,
                        'currency' => $transaction->currency,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at
                    ]);
                    
                default:
                    return response()->json([
                        'success' => true,
                        'status' => $this->getStatusText($transaction->status),
                        'status_code' => $transaction->status,
                        'gateway' => 'unknown',
                        'payment_id' => $transaction->payment_id,
                        'amount' => $transaction->price,
                        'currency' => $transaction->currency,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at
                    ]);
            }

        } catch (\Exception $e) {
            Log::error('TransactionStatusController - Erro ao verificar status por token: ' . $e->getMessage(), [
                'token' => $request->input('token')
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno ao verificar status da transação'
            ], 500);
        }
    }

    /**
     * Consultar status CNPay por token
     */
    private function checkCNPayStatusByToken(Transaction $transaction): JsonResponse
    {
        try {
            Log::info('TransactionStatusController - checkCNPayStatusByToken iniciado', [
                'transaction_id' => $transaction->id,
                'payment_id' => $transaction->payment_id,
                'user_id' => $transaction->user_id,
                'current_status' => $transaction->status
            ]);

            // Verificar status na API do CNPay
            $status = self::checkCNPayPaymentStatus($transaction->payment_id);
            
            if ($status && isset($status['success']) && $status['success']) {
                Log::info('TransactionStatusController - Status CNPay verificado com sucesso por token', [
                    'transaction_id' => $transaction->id,
                    'api_status' => $status['status'] ?? 'unknown',
                    'original_status' => $status['original_status'] ?? 'unknown'
                ]);

                return response()->json([
                    'success' => true,
                    'status' => $status['status'] ?? $transaction->status,
                    'status_code' => $status['status'] ?? $transaction->status,
                    'gateway' => 'cnpay',
                    'payment_id' => $transaction->payment_id,
                    'amount' => $status['amount'] ?? $transaction->price,
                    'currency' => $status['currency'] ?? $transaction->currency,
                    'created_at' => $status['created_at'] ?? $transaction->created_at,
                    'updated_at' => $status['updated_at'] ?? $transaction->updated_at,
                    'original_status' => $status['original_status'] ?? null
                ]);
            }

            // Se não conseguiu verificar na API, retornar status do banco
            Log::warning('TransactionStatusController - Falha na API CNPay, retornando status do banco por token', [
                'transaction_id' => $transaction->id,
                'api_error' => $status['error'] ?? 'erro desconhecido',
                'fallback_status' => $transaction->status
            ]);

            return response()->json([
                'success' => true,
                'status' => $this->getStatusText($transaction->status),
                'status_code' => $transaction->status,
                'gateway' => 'cnpay',
                'payment_id' => $transaction->payment_id,
                'amount' => $transaction->price,
                'currency' => $transaction->currency,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'note' => 'Status obtido do banco local (API indisponível)'
            ]);

        } catch (\Exception $e) {
            Log::error('TransactionStatusController - Erro ao verificar status CNPay por token: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Em caso de erro, retornar status do banco
            return response()->json([
                'success' => true,
                'status' => $this->getStatusText($transaction->status),
                'status_code' => $transaction->status,
                'gateway' => 'cnpay',
                'payment_id' => $transaction->payment_id,
                'amount' => $transaction->price,
                'currency' => $transaction->currency,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'note' => 'Status obtido do banco local (erro na API)'
            ]);
        }
    }

    /**
     * Listar todas as transações do usuário
     */
    public function listTransactions(Request $request): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            
            $transactions = Transaction::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao listar transações: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erro ao listar transações'
            ], 500);
        }
    }

    /**
     * Converter código de status para texto legível
     */
    private function getStatusText(int $statusCode): string
    {
        $statusMap = [
            0 => 'pending',
            1 => 'confirmed',
            2 => 'cancelled',
            3 => 'failed',
            4 => 'processing'
        ];

        return $statusMap[$statusCode] ?? 'unknown';
    }
}
