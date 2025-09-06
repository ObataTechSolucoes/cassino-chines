<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Traits\Gateways\CNPayTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CNPayController extends Controller
{
    use CNPayTrait;

    /**
     * Gerar QR Code PIX via CNPay
     */
    public function getQRCodePix(Request $request): JsonResponse
    {
        try {
            Log::info('CNPayController - getQRCodePix chamado', [
                'request_data' => $request->all(),
                'user_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ]);

            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'cpf' => 'required|string|max:255',
                'accept_bonus' => 'boolean'
            ]);

            Log::info('CNPayController - Validação passou, gerando QR Code PIX', [
                'amount' => $request->amount,
                'cpf' => $request->cpf,
                'accept_bonus' => $request->accept_bonus ?? false
            ]);

            $result = self::requestQrcodeCNPay($request);

            Log::info('CNPayController - Resultado da geração do QR Code', [
                'result' => $result,
                'success' => $result->getData()->status ?? false
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('CNPayController - Erro na geração do QR Code PIX', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro na geração do QR Code PIX: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Callback para confirmação de pagamento CNPay
     */
    public function callbackMethod(Request $request): JsonResponse
    {
        try {
            Log::info('CNPayController - callbackMethod chamado', [
                'request_data' => $request->all(),
                'headers' => $request->headers->all(),
                'timestamp' => now()->toISOString()
            ]);

            // Verificar se é uma confirmação válida
            if (isset($request->transaction_id) && isset($request->status)) {
                if ($request->status === 'PAID' || $request->status === 'COMPLETED') {
                    Log::info('CNPayController - Pagamento confirmado, finalizando', [
                        'transaction_id' => $request->transaction_id,
                        'status' => $request->status
                    ]);

                    if (self::finalizePaymentCNPay($request->transaction_id)) {
                        Log::info('CNPayController - Pagamento finalizado com sucesso', [
                            'transaction_id' => $request->transaction_id
                        ]);
                        return response()->json(['status' => 'success'], 200);
                    } else {
                        Log::error('CNPayController - Falha ao finalizar pagamento', [
                            'transaction_id' => $request->transaction_id
                        ]);
                        return response()->json(['error' => 'Falha ao finalizar pagamento'], 500);
                    }
                } else {
                    Log::info('CNPayController - Status não é de pagamento confirmado', [
                        'transaction_id' => $request->transaction_id ?? 'não fornecido',
                        'status' => $request->status
                    ]);
                    return response()->json(['status' => 'ignored'], 200);
                }
            }

            Log::warning('CNPayController - Callback inválido recebido', [
                'request_data' => $request->all()
            ]);

            return response()->json(['error' => 'Callback inválido'], 400);

        } catch (\Exception $e) {
            Log::error('CNPayController - Erro no callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json(['error' => 'Erro interno no callback'], 500);
        }
    }

    /**
     * Consultar status de transação CNPay
     */
    public function consultStatusTransactionPix(Request $request): JsonResponse
    {
        try {
            Log::info('CNPayController - consultStatusTransactionPix chamado', [
                'request_data' => $request->all(),
                'user_id' => auth('api')->id() ?? 'não autenticado'
            ]);

            $request->validate([
                'payment_id' => 'required|string',
                'gateway' => 'required|string|in:cnpay'
            ]);

            $paymentId = $request->payment_id;
            
            Log::info('CNPayController - Verificando status da transação', [
                'payment_id' => $paymentId
            ]);

            // Buscar transação no banco
            $transaction = \App\Models\Transaction::where('payment_id', $paymentId)->first();

            if (!$transaction) {
                Log::warning('CNPayController - Transação não encontrada', [
                    'payment_id' => $paymentId
                ]);
                return response()->json(['error' => 'Transação não encontrada'], 404);
            }

            Log::info('CNPayController - Status da transação consultado', [
                'payment_id' => $paymentId,
                'status' => $transaction->status,
                'amount' => $transaction->price
            ]);

            return response()->json([
                'success' => true,
                'transaction' => [
                    'payment_id' => $transaction->payment_id,
                    'status' => $transaction->status,
                    'amount' => $transaction->price,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('CNPayController - Erro ao consultar status da transação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao consultar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Callback de pagamento (método alternativo)
     */
    public function callbackMethodPayment(Request $request): JsonResponse
    {
        Log::info('CNPayController - callbackMethodPayment chamado', [
            'request_data' => $request->all()
        ]);

        // Log para debug
        \DB::table('debug')->insert(['text' => json_encode($request->all())]);

        return response()->json([], 200);
    }

    /**
     * Callback de saque CNPay
     */
    public function withdrawCallback(Request $request): JsonResponse
    {
        \Illuminate\Support\Facades\Log::info('Callback de saque CNPay recebido', [
            'payload' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            $data = $request->all();

            if (isset($data['withdraw']) && isset($data['withdraw']['id'])) {
                $withdrawId = $data['withdraw']['id'];
                $status = $data['withdraw']['status'] ?? 'unknown';

                if (in_array(strtolower($status), ['completed', 'approved', 'success', 'paid'])) {
                    \Illuminate\Support\Facades\Log::info('Saque aprovado via callback CNPay', [
                        'transaction_id' => $withdrawId,
                        'status' => $status
                    ]);

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
    }

    /**
     * Callback de saque de afiliado CNPay
     */
    public function affiliateWithdrawCallback(Request $request): JsonResponse
    {
        \Illuminate\Support\Facades\Log::info('Callback de saque de afiliado CNPay recebido', [
            'payload' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            $data = $request->all();

            if (isset($data['withdraw']) && isset($data['withdraw']['id'])) {
                $withdrawId = $data['withdraw']['id'];
                $status = $data['withdraw']['status'] ?? 'unknown';

                if (in_array(strtolower($status), ['completed', 'approved', 'success', 'paid'])) {
                    \Illuminate\Support\Facades\Log::info('Saque de afiliado aprovado via callback CNPay', [
                        'transaction_id' => $withdrawId,
                        'status' => $status
                    ]);

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
    }
}
