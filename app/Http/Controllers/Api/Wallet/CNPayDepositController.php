<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Traits\Gateways\CNPayTrait;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CNPayDepositController extends Controller
{
    use CNPayTrait;

    /**
     * Criar depósito via CNPay
     */
    public function createDeposit(Request $request)
    {
        try {
            // Validar dados da requisição
            $request->validate([
                'valor' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:255',
            ]);

            $valor = floatval($request->valor);
            $description = $request->description ?? 'Depósito via CNPay';
            $user = Auth::user();

            if ($valor <= 0) {
                return response()->json([
                    'error' => 'Valor inválido'
                ], 400);
            }

            // Verificar se o usuário tem saldo suficiente (se aplicável)
            if ($user->wallet && $user->wallet->balance < 0) {
                return response()->json([
                    'error' => 'Saldo insuficiente'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Criar ID único da transação
                $transactionId = 'CNP' . time() . rand(1000, 9999);

                // Criar registro de transação
                $transaction = Transaction::create([
                    'payment_id' => $transactionId,
                    'reference' => $transactionId,
                    'user_id' => $user->id,
                    'payment_method' => 'cnpay',
                    'gateway_name' => 'cnpay',
                    'price' => $valor,
                    'currency' => 'BRL',
                    'status' => 0, // 0 = pending
                    'gateway_response' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Preparar dados para o CNPay
                $cnpayData = [
                    'amount' => $valor,
                    'currency' => 'BRL',
                    'description' => $description,
                    'external_id' => $transactionId,
                    'callback_url' => url('/api/cnpay/webhook'),
                    'return_url' => url('/payment/success'),
                    'expires_at' => now()->addMinutes(30)->toISOString(),
                    'customer' => [
                        'name' => $user->name ?? 'Usuário',
                        'email' => $user->email ?? '',
                        'tax_id' => ''
                    ]
                ];

                // Log da requisição que será enviada
                Log::info('CNPay - Criando depósito', [
                    'user_id' => $user->id,
                    'amount' => $valor,
                    'transaction_id' => $transactionId,
                    'cnpay_data' => $cnpayData,
                    'cnpay_data_json' => json_encode($cnpayData, JSON_PRETTY_PRINT)
                ]);

                // Fazer requisição para o CNPay usando o trait
                $cnpayResponse = $this->createCNPayPaymentWithCustomData($cnpayData);

                if (!$cnpayResponse['success']) {
                    throw new \Exception('Erro na API do CNPay: ' . ($cnpayResponse['error'] ?? 'Erro desconhecido'));
                }

                // Atualizar transação com dados do PIX
                $pixData = $cnpayResponse['data'] ?? [];
                
                $transaction->update([
                    'gateway_response' => $pixData,
                    'updated_at' => now(),
                ]);

                // Log do sucesso
                Log::info('CNPay - Depósito criado com sucesso', [
                    'transaction_id' => $transactionId,
                    'user_id' => $user->id,
                    'amount' => $valor,
                    'cnpay_response' => $cnpayResponse
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'PIX gerado com sucesso',
                    'data' => [
                        'transaction_id' => $transactionId,
                        'pix_key' => $pixData['pix_key'] ?? '',
                        'qr_code' => $pixData['qr_code'] ?? '',
                        'payment_url' => $pixData['payment_url'] ?? '',
                        'expires_at' => $pixData['expires_at'] ?? '',
                        'amount' => $valor,
                        'status' => 'pending'
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Erro ao criar depósito CNPay: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'amount' => $valor ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erro ao gerar PIX',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar pagamento CNPay com dados customizados
     */
    private function createCNPayPaymentWithCustomData($cnpayData)
    {
        try {
            // Inicializar configurações
            $this->initCNPay();

            if (!$this->uriCNPay || !$this->publicKeyCNPay || !$this->secretKeyCNPay) {
                throw new \Exception('Configurações do CNPay não encontradas');
            }

            // Log detalhado da requisição
            Log::info('CNPay - Dados da requisição customizada', [
                'uri_completa' => $this->uriCNPay . '/v1/pix/create',
                'method' => 'POST',
                'headers' => [
                    'x-public-key' => $this->publicKeyCNPay ? 'configurado' : 'não configurado',
                    'x-secret-key' => $this->secretKeyCNPay ? 'configurado' : 'não configurado',
                    'Content-Type' => 'application/json',
                ],
                'payload' => $cnpayData,
                'payload_json' => json_encode($cnpayData, JSON_PRETTY_PRINT),
                'configuracoes' => [
                    'uri_base' => $this->uriCNPay,
                    'public_key_length' => $this->publicKeyCNPay ? strlen($this->publicKeyCNPay) : 0,
                    'secret_key_length' => $this->secretKeyCNPay ? strlen($this->secretKeyCNPay) : 0,
                ]
            ]);

            // Fazer requisição usando cURL para maior controle
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => $this->uriCNPay . '/v1/pix/create',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($cnpayData),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'x-public-key: ' . $this->publicKeyCNPay,
                    'x-secret-key: ' . $this->secretKeyCNPay,
                    'User-Agent: CNPay-Integration/1.0 (Laravel)',
                    'Accept: application/json',
                    'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
                    'Accept-Encoding: gzip, deflate, br',
                    'Connection: keep-alive',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $responseHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
            
            curl_close($ch);

            if ($curlError) {
                throw new \Exception('Erro de conexão cURL: ' . $curlError);
            }

            // Log da resposta
            Log::info('CNPay - Resposta da API', [
                'http_code' => $httpCode,
                'response_headers' => $responseHeaders,
                'response_body' => $response,
                'response_body_length' => strlen($response)
            ]);

            if ($httpCode !== 200) {
                Log::error('CNPay - Erro HTTP', [
                    'http_code' => $httpCode,
                    'response_body' => $response,
                    'request_data' => $cnpayData
                ]);
                
                throw new \Exception('Erro HTTP: ' . $httpCode . ' - ' . $response);
            }

            $cnpayResponse = json_decode($response, true);

            if (!$cnpayResponse || !isset($cnpayResponse['success']) || !$cnpayResponse['success']) {
                throw new \Exception('Erro na resposta do CNPay: ' . ($cnpayResponse['message'] ?? 'Resposta inválida'));
            }

            return [
                'success' => true,
                'data' => $cnpayResponse['data'] ?? $cnpayResponse
            ];

        } catch (\Exception $e) {
            Log::error('CNPay - Erro na API customizada', [
                'error' => $e->getMessage(),
                'request_data' => $cnpayData,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Webhook do CNPay
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->all();
            $headers = $request->headers->all();

            Log::info('CNPay - Webhook recebido', [
                'headers' => $headers,
                'payload' => $payload,
                'payload_json' => json_encode($payload, JSON_PRETTY_PRINT),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Processar webhook usando o trait
            $result = $this->processCNPayWebhook($payload, $headers);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Webhook processado com sucesso',
                    'transaction_id' => $result['transaction_id'] ?? null
                ]);
            } else {
                Log::error('CNPay - Erro ao processar webhook', [
                    'error' => $result['error'],
                    'payload' => $payload
                ]);

                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('CNPay - Erro no webhook: ' . $e->getMessage(), [
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Verificar status do depósito
     */
    public function checkStatus($transactionId)
    {
        try {
            $transaction = Transaction::where('payment_id', $transactionId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$transaction) {
                return response()->json([
                    'error' => 'Transação não encontrada'
                ], 404);
            }

            // Verificar status no CNPay
            $statusResponse = $this->checkCNPayPaymentStatus($transactionId);

            if ($statusResponse['success']) {
                // Atualizar status da transação se necessário
                if ($statusResponse['status'] != $transaction->status) {
                    $transaction->update([
                        'status' => $statusResponse['status'],
                        'updated_at' => now()
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'transaction_id' => $transactionId,
                        'status' => $statusResponse['status'],
                        'status_text' => $statusResponse['status_text'],
                        'amount' => $transaction->price,
                        'currency' => $transaction->currency,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at
                    ]
                ]);
            } else {
                return response()->json([
                    'error' => 'Erro ao verificar status',
                    'message' => $statusResponse['error']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status CNPay: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
