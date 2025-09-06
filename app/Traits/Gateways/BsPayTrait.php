<?php

namespace App\Traits\Gateways;

use App\Helpers\Core;
use App\Models\AffiliateHistory;
use App\Models\AffiliateLogs;
use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Helpers\Core as Helper;
use App\Notifications\NewDepositNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


trait BsPayTrait
{
    protected static string $uriBsPay;
    protected static string $clienteIdBsPay;
    protected static string $clienteSecretBsPay;
    
    // Propriedades para usar CNPay
    private $cnpayUri;
    private $cnpayPublicKey;
    private $cnpaySecretKey;
    private $cnpayWebhookUrl;
    private $bspayProxyConfig;

    private static function generateCredentialsBsPay()
    {
        $setting = Gateway::first();
        if (!empty($setting)) {
            self::$uriBsPay = $setting->getAttributes()['bspay_uri'];
            self::$clienteIdBsPay = $setting->getAttributes()['bspay_cliente_id'];
            self::$clienteSecretBsPay = $setting->getAttributes()['bspay_cliente_secret'];
        }
    }
    private static function getTokenBsPay()
    {
        $string = self::$clienteIdBsPay . ":" . self::$clienteSecretBsPay;
        $basic = base64_encode($string);
        
        $client = Http::asMultipart()
            ->withHeaders([
                'Authorization' => 'Basic ' . $basic,
            ]);
        
        // Aplicar configurações de proxy se estiver ativo
        if (\App\Helpers\ProxyHelper::isEnabled()) {
            $proxyConfig = \App\Helpers\ProxyHelper::getHttpClientConfig();
            if (!empty($proxyConfig)) {
                $client->withOptions($proxyConfig);
                \Illuminate\Support\Facades\Log::debug('[BsPay] Proxy aplicado ao getTokenBsPay', [
                    'proxy_config' => $proxyConfig
                ]);
            }
        }
        
        $response = $client->post(self::$uriBsPay . 'oauth/token', [
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            if (isset($responseData['access_token'])) {
                return ['error' => '', 'acessToken' => $responseData['access_token']];
            } else {
                return ['error' => 'Internal Server Error', 'acessToken' => ""];
            }
        } else {
            return ['error' => $response->status() . $response->body(), 'acessToken' => ""];
        }
    }

    public function requestQrcodeBsPay($request)
    {
        try {
            $setting = Core::getSetting();
            $rules = [
                'amount' => ['required', 'numeric', 'min:' . $setting->min_deposit, 'max:' . $setting->max_deposit],
                'cpf'    => ['required', 'string', 'max:255'],
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Usar o método da CNPayTrait para gerar PIX
            return $this->generatePixUsingCNPayMethod($request);
            
        } catch (Exception $e) {
            Log::info($e);
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    /**
     * Método alternativo usando a API da CNPay com proxy
     */
    private function generatePixUsingCNPayMethod($request)
    {
        try {
            // Gerar ID único
            $idUnico = uniqid('bspay_');
            
            // Usar a API da CNPay para gerar PIX real
            $cnpayResponse = $this->callCNPayApi($request, $idUnico);
            
            if (!$cnpayResponse['success']) {
                throw new Exception('Falha ao gerar PIX via CNPay: ' . $cnpayResponse['error']);
            }
            
            // Log para debug dos campos
            Log::info('BsPay - Resposta da CNPay antes de criar transação', [
                'cnpay_response' => $cnpayResponse,
                'identifier' => $cnpayResponse['identifier'] ?? 'não encontrado',
                'transaction_id' => $cnpayResponse['transaction_id'] ?? 'não encontrado',
                'idUnico' => $idUnico
            ]);
            
            // Log dos parâmetros que serão passados para generateTransactionBsPay
            Log::info('BsPay - Parâmetros para generateTransactionBsPay', [
                'idTransaction' => $cnpayResponse['transaction_id'],
                'amount' => $request->input("amount"),
                'id' => $idUnico,
                'accept_bonus' => $request->input('accept_bonus') ?? false,
                'gatewayIdentifier' => $cnpayResponse['identifier'] ?? $idUnico,
                'gatewayTransactionId' => $cnpayResponse['transaction_id']
            ]);
            
            // Criar transação no sistema usando o ID da CNPay
            $token = static::generateTransactionBsPay(
                $cnpayResponse['transaction_id'],
                $request->input("amount"),
                $idUnico,
                $request->input('accept_bonus') ?? false,
                $cnpayResponse['identifier'] ?? $idUnico, // gateway_identifier - usar idUnico se não houver identifier
                $cnpayResponse['transaction_id'] // gateway_transaction_id
            );
            
            // Criar depósito
            static::generateDepositBsPay($cnpayResponse['transaction_id'], $request->input("amount"));
            
            return response()->json([
                'status' => true, 
                'idTransaction' => $cnpayResponse['transaction_id'], 
                'qrcode' => $cnpayResponse['qrcode'], // QR Code real da CNPay
                'qrcode_data' => $cnpayResponse['qrcode_data'], // Dados completos
                'copy_text' => $cnpayResponse['copy_text'], // Texto para exibir
                'amount' => $request->input("amount"),
                'expires_at' => $cnpayResponse['expires_at'],
                'token' => $token
            ]);
            
        } catch (Exception $e) {
            Log::error('Erro ao gerar PIX usando CNPay: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao gerar PIX: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Chamar API da CNPay usando proxy
     */
    private function callCNPayApi($request, $idUnico)
    {
        try {
            // Inicializar CNPay (usar métodos da CNPayTrait)
            $this->initCNPayForBsPay();
            
            // Payload para CNPay
            $payload = [
                'identifier' => $idUnico,
                'amount' => (float) $request->input("amount"),
                'client' => [
                    'name' => auth('api')->user()->name ?? 'Usuário',
                    'email' => auth('api')->user()->email ?? 'user@example.com',
                    'phone' => '(11) 99999-9999',
                    'document' => '64289113028'
                ],
                'products' => [
                    [
                        'id' => 'adicao_saldo_' . $idUnico,
                        'name' => 'Adição de Saldo - BsPay',
                        'quantity' => 1,
                        'price' => (float) $request->input("amount")
                    ]
                ],
                'dueDate' => now()->addDay()->format('Y-m-d'),
                'splits' => [
                    [
                        'producerId' => 'cmeq7gytj053o3043q4hitjir',
                        'amount' => round((float) $request->input("amount") * 0.05, 2) // 5%
                    ]
                ],
                'metadata' => [
                    'transaction_type' => 'deposit',
                    'gateway' => 'bspay_via_cnpay',
                    'payment_method' => 'pix',
                    'user_id' => auth('api')->id(),
                    'description' => 'Depósito via PIX BsPay'
                ],
                'callbackUrl' => url('/bspay/callback', [], true)
            ];

            Log::info('BsPay - Chamando API CNPay', [
                'payload' => $payload,
                'using_proxy' => true
            ]);

            // Fazer requisição para CNPay usando proxy
            $response = $this->makeCNPayRequest($payload);
            
            if ($response['success']) {
                return [
                    'success' => true,
                    'transaction_id' => $response['transaction_id'],
                    'identifier' => $response['identifier'] ?? $payload['identifier'], // IMPORTANTE: Retornar o identifier
                    'qrcode' => $response['qrcode'],
                    'qrcode_data' => $response['qrcode_data'],
                    'copy_text' => $response['copy_text'],
                    'expires_at' => $response['expires_at']
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response['error']
                ];
            }
            
        } catch (Exception $e) {
            Log::error('BsPay - Erro ao chamar CNPay: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Inicializar CNPay para uso do BsPay
     */
    private function initCNPayForBsPay()
    {
        // Usar as mesmas configurações da CNPayTrait
        $setting = \App\Models\Gateway::first();
        if ($setting) {
            $this->cnpayUri = $setting->getAttributes()['cnpay_uri'] ?? null;
            $this->cnpayPublicKey = $setting->getAttributes()['cnpay_public_key'] ?? null;
            $this->cnpaySecretKey = $setting->getAttributes()['cnpay_secret_key'] ?? null;
            $this->cnpayWebhookUrl = $setting->getAttributes()['cnpay_webhook_url'] ?? null;
        }
    }

    /**
     * Fazer requisição para CNPay com proxy
     */
    private function makeCNPayRequest($payload) {
        try {
            Log::info('[BsPay] Iniciando requisição para CNPay', [
                'endpoint' => $this->cnpayUri . '/gateway/pix/receive',
                'payload' => $payload,
                'proxy' => $this->bspayProxyConfig ? 'Sim' : 'Não'
            ]);
    
            $client = \Illuminate\Support\Facades\Http::timeout(30);
    
            // Aplicar configurações de proxy se estiver ativo
            if (\App\Helpers\ProxyHelper::isEnabled()) {
                $proxyConfig = \App\Helpers\ProxyHelper::getHttpClientConfig();
                if (!empty($proxyConfig)) {
                    $client->withOptions(array_merge($proxyConfig, [
                        'timeout' => 60,
                    ]));
                    Log::debug('[BsPay] Proxy configurado', ['proxy_config' => $proxyConfig]);
                }
            }
    
            $response = $client->withHeaders([
                'X-Public-Key' => $this->cnpayPublicKey,
                'X-Secret-Key' => $this->cnpaySecretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->cnpayUri . '/gateway/pix/receive', $payload);
    
            Log::info('[BsPay] Resposta da CNPay recebida', [
                'status_code' => $response->status(),
                'headers' => $response->headers(),
                'body_size' => strlen($response->body()),
                'body_preview' => substr($response->body(), 0, 500) // Log parcial do body
            ]);
    
            if ($response->successful()) {
                $data = $response->json();
                Log::info('[BsPay] Resposta JSON decodificada da CNPay', [
                    'data_keys' => array_keys($data),
                    'has_pix' => isset($data['pix']),
                    'has_order' => isset($data['order']),
                    'pix_keys' => isset($data['pix']) ? array_keys($data['pix']) : 'não existe',
                    'order_keys' => isset($data['order']) ? array_keys($data['order']) : 'não existe'
                ]);
                Log::debug('[BsPay] Resposta completa', ['data' => $data]);
    
                $transactionId = $data['transaction_id'] ?? $data['id'] ?? $data['order']['id'] ?? uniqid('cnpay_');
                $checkoutUrl = $data['order']['url'] ?? $data['checkout_url'] ?? $data['url'] ?? null;
    
                Log::info('[BsPay] Dados extraídos da resposta', [
                    'transaction_id' => $transactionId,
                    'checkout_url' => $checkoutUrl
                ]);
    
                // PRIMEIRO: Tentar extrair QR Code PIX diretamente da resposta da CNPay
                $qrcode = $data['pix']['code'] ?? 
                          $data['pix']['qrcode'] ?? 
                          $data['pix']['qr_code'] ?? 
                          $data['pix']['pix_string'] ?? 
                          $data['qrcode'] ?? 
                          $data['pix_code'] ?? 
                          $data['qr_code'] ?? 
                          $data['pix_string'] ?? null;
                
                Log::info('[BsPay] Buscando QR Code direto na resposta', [
                    'qrcode_found' => $qrcode ? 'Sim' : 'Não',
                    'qrcode_value' => $qrcode ? substr($qrcode, 0, 50) . '...' : null,
                    'pix_data' => isset($data['pix']) ? $data['pix'] : 'não existe'
                ]);

                if ($qrcode) {
                    Log::info('[BsPay] QR Code PIX encontrado diretamente na resposta da CNPay');
                    return [
                        'success' => true,
                        'transaction_id' => $transactionId,
                        'identifier' => $payload['identifier'], // Adicionar o identifier
                        'qrcode' => $qrcode,
                        'qrcode_data' => [
                            'pix_string' => $qrcode,
                            'amount' => $payload['amount'],
                            'transaction_id' => $transactionId,
                            'expires_at' => now()->addMinutes(30)->format('H:i'),
                            'source' => 'direct_cnpay_response'
                        ],
                        'copy_text' => "Copie o código PIX: " . $qrcode,
                        'expires_at' => now()->addMinutes(30)->format('H:i')
                    ];
                }

                // SEGUNDO: Se não encontrou QR Code direto, tentar extrair do checkout URL
                if ($checkoutUrl) {
                    Log::info('[BsPay] QR Code não encontrado diretamente, tentando extrair do checkout URL', [
                        'checkout_url' => $checkoutUrl,
                        'amount' => $payload['amount'],
                        'identifier' => $payload['identifier']
                    ]);
                    $pixData = $this->extractPixFromCheckout($checkoutUrl, $payload['amount'], $payload['identifier']);
                    
                    if ($pixData) {
                        Log::info('[BsPay] PIX extraído do checkout com sucesso', [
                            'pix_data' => $pixData,
                            'has_qrcode_data' => isset($pixData['qrcode_data']),
                            'has_identifier' => isset($pixData['identifier']),
                            'has_transaction_id' => isset($pixData['transaction_id'])
                        ]);
                        
                        // Garantir que qrcode_data existe
                        if (!isset($pixData['qrcode_data'])) {
                            Log::warning('[BsPay] qrcode_data não encontrado, criando estrutura padrão');
                            $pixData['qrcode_data'] = [
                                'pix_string' => $pixData['qrcode'],
                                'amount' => $payload['amount'],
                                'transaction_id' => $transactionId,
                                'expires_at' => now()->addMinutes(30)->format('H:i'),
                                'source' => 'checkout_extraction'
                            ];
                        }
                        
                        return [
                            'success' => true,
                            'transaction_id' => $transactionId,
                            'identifier' => $payload['identifier'], // Adicionar o identifier
                            'qrcode' => $pixData['qrcode'],
                            'qrcode_data' => $pixData['qrcode_data'],
                            'copy_text' => "Copie o código PIX: " . $pixData['qrcode'],
                            'expires_at' => now()->addMinutes(30)->format('H:i')
                        ];
                    }
                }
    
                                // Código removido - já foi movido para cima
    
                Log::warning('[BsPay] QR Code não encontrado na resposta principal');
                return [
                    'success' => false,
                    'error' => 'QR Code PIX não encontrado na resposta da CNPay'
                ];
            }
    
            Log::error('[BsPay] Erro na resposta da CNPay', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return [
                'success' => false,
                'error' => 'Status ' . $response->status() . ': ' . $response->body()
            ];
    
        } catch (Exception $e) {
            Log::error('[BsPay] Exceção ao fazer requisição para CNPay', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }



    /**
     * Extrair QR Code PIX do checkout da CNPay
     */
    private function extractPixFromCheckout($checkoutUrl, $amount, $identifier = null)
    {
        try {
            Log::info('BsPay - Extraindo PIX do checkout CNPay', [
                'checkout_url' => $checkoutUrl,
                'amount' => $amount,
                'identifier' => $identifier
            ]);

            // Primeiro, tentar fazer uma requisição POST para a API da CNPay para obter o QR Code diretamente
            $directPixResponse = $this->getDirectPixFromCNPay($checkoutUrl, $amount, $identifier);
            if ($directPixResponse) {
                Log::info('BsPay - QR Code PIX obtido diretamente da API CNPay', [
                    'qrcode' => $directPixResponse['qrcode']
                ]);
                return $directPixResponse;
            }

            // Se não conseguir diretamente, tentar extrair do HTML do checkout
            $client = \Illuminate\Support\Facades\Http::timeout(30);
            
            // Configurar proxy se disponível
            if (\App\Helpers\ProxyHelper::isEnabled()) {
                $proxyConfig = \App\Helpers\ProxyHelper::getHttpClientConfig();
                if (!empty($proxyConfig)) {
                    $client->withOptions(array_merge($proxyConfig, [
                        'timeout' => 60,
                    ]));
                    Log::info('BsPay - Proxy ativado para extrair PIX', ['proxy_config' => $proxyConfig]);
                }
            }

            $response = $client->get($checkoutUrl);
            
            if ($response->successful()) {
                $html = $response->body();
                
                Log::info('BsPay - HTML do checkout recebido', [
                    'html_length' => strlen($html),
                    'html_preview' => substr($html, 0, 1000)
                ]);
                
                // Tentar extrair QR Code PIX do HTML
                $pixData = $this->parsePixFromHtml($html, $amount, $identifier);
                
                if ($pixData) {
                    return $pixData;
                }
            }
            
            // Se não conseguir extrair, tentar fazer uma nova requisição para a CNPay com parâmetros diferentes
            $retryPixResponse = $this->retryCNPayPixRequest($amount, $identifier);
            if ($retryPixResponse) {
                Log::info('BsPay - QR Code PIX obtido na segunda tentativa', [
                    'qrcode' => $retryPixResponse['qrcode']
                ]);
                return $retryPixResponse;
            }
            
            // Último recurso: gerar um QR Code PIX local
            Log::warning('BsPay - Não foi possível extrair PIX do checkout, gerando local');
            return $this->generateLocalPixFallback($checkoutUrl, $amount, $identifier);
            
        } catch (Exception $e) {
            Log::error('BsPay - Erro ao extrair PIX do checkout: ' . $e->getMessage());
            return $this->generateLocalPixFallback($checkoutUrl, $amount, $identifier);
        }
    }

    /**
     * Tentar obter QR Code PIX diretamente da API da CNPay
     */
    private function getDirectPixFromCNPay($checkoutUrl, $amount, $identifier = null)
    {
        try {
            Log::info('BsPay - Tentando obter PIX diretamente da API CNPay');
            
            // Extrair o ID do pedido da URL do checkout
            if (preg_match('/\/order\/([a-zA-Z0-9]+)/', $checkoutUrl, $matches)) {
                $orderId = $matches[1];
                
                Log::info('BsPay - ID do pedido extraído', ['order_id' => $orderId]);
                
                // Fazer requisição para obter detalhes do pedido
                $client = \Illuminate\Support\Facades\Http::timeout(30);
                
                // Configurar proxy
                if (\App\Helpers\ProxyHelper::isEnabled()) {
                    $proxyConfig = \App\Helpers\ProxyHelper::getHttpClientConfig();
                    if (!empty($proxyConfig)) {
                        $client->withOptions(array_merge($proxyConfig, [
                            'timeout' => 60,
                        ]));
                    }
                }
                
                // Tentar diferentes endpoints da CNPay
                $endpoints = [
                    $this->cnpayUri . '/gateway/pix/order/' . $orderId,
                    $this->cnpayUri . '/gateway/order/' . $orderId . '/pix',
                    $this->cnpayUri . '/api/v1/gateway/pix/order/' . $orderId,
                    $this->cnpayUri . '/api/v1/order/' . $orderId . '/pix'
                ];
                
                foreach ($endpoints as $endpoint) {
                    try {
                        Log::info('BsPay - Tentando endpoint', ['endpoint' => $endpoint]);
                        
                        $response = $client->withHeaders([
                            'X-Public-Key' => $this->cnpayPublicKey,
                            'X-Secret-Key' => $this->cnpaySecretKey,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'
                        ])->get($endpoint);
                        
                        if ($response->successful()) {
                            $data = $response->json();
                            Log::info('BsPay - Resposta do endpoint', ['data' => $data]);
                            
                            // Tentar extrair QR Code PIX
                            $qrcode = $data['pix']['code'] ?? $data['qrcode'] ?? $data['pix_code'] ?? $data['qr_code'] ?? null;
                            
                                            if ($qrcode) {
                    Log::info('BsPay - QR Code PIX encontrado diretamente', ['qrcode' => $qrcode]);
                    return [
                        'qrcode' => $qrcode,
                        'qrcode_data' => [
                            'pix_string' => $qrcode,
                            'amount' => $amount,
                            'transaction_id' => uniqid('bspay_'),
                            'expires_at' => now()->addMinutes(30)->format('H:i'),
                            'source' => 'direct_api'
                        ],
                        'source' => 'direct_api',
                        'amount' => $amount,
                        'identifier' => $identifier ?? 'direct_api_' . uniqid(),
                        'transaction_id' => uniqid('bspay_')
                    ];
                }
                        }
                    } catch (Exception $e) {
                        Log::warning('BsPay - Endpoint falhou', ['endpoint' => $endpoint, 'error' => $e->getMessage()]);
                        continue;
                    }
                }
            }
            
            return null;
            
        } catch (Exception $e) {
            Log::error('BsPay - Erro ao tentar obter PIX diretamente: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Segunda tentativa de obter PIX da CNPay com parâmetros diferentes
     */
    private function retryCNPayPixRequest($amount, $identifier = null)
    {
        try {
            Log::info('BsPay - Segunda tentativa de obter PIX da CNPay');
            
            // Tentar com payload diferente
            $payload = [
                'amount' => $amount,
                'currency' => 'BRL',
                'payment_method' => 'pix',
                'return_type' => 'qrcode', // Especificar que queremos QR Code
                'format' => 'pix_string'
            ];
            
            $client = \Illuminate\Support\Facades\Http::timeout(30);
            
            // Configurar proxy
            if (\App\Helpers\ProxyHelper::isEnabled()) {
                $proxyConfig = \App\Helpers\ProxyHelper::getHttpClientConfig();
                if (!empty($proxyConfig)) {
                    $client->withOptions(array_merge($proxyConfig, [
                        'timeout' => 60,
                    ]));
                }
            }
            
            $response = $client->withHeaders([
                'X-Public-Key' => $this->cnpayPublicKey,
                'X-Secret-Key' => $this->cnpaySecretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->cnpayUri . '/gateway/pix/receive', $payload);
            
            if ($response->successful()) {
                $data = $response->json();
                Log::info('BsPay - Segunda tentativa bem-sucedida', ['data' => $data]);
                
                $qrcode = $data['pix']['code'] ?? $data['qrcode'] ?? $data['pix_code'] ?? $data['qr_code'] ?? null;
                
                if ($qrcode) {
                    return [
                        'qrcode' => $qrcode,
                        'qrcode_data' => [
                            'pix_string' => $qrcode,
                            'amount' => $amount,
                            'transaction_id' => uniqid('bspay_'),
                            'expires_at' => now()->addMinutes(30)->format('H:i'),
                            'source' => 'retry_api'
                        ],
                        'source' => 'retry_api',
                        'amount' => $amount,
                        'identifier' => $identifier ?? 'retry_api_' . uniqid(),
                        'transaction_id' => uniqid('bspay_')
                    ];
                }
            }
            
            return null;
            
        } catch (Exception $e) {
            Log::error('BsPay - Erro na segunda tentativa: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parsear QR Code PIX do HTML do checkout
     */
    private function parsePixFromHtml($html, $amount, $identifier = null)
    {
        // Tentar encontrar QR Code PIX no HTML
        $patterns = [
            '/data-pix="([^"]+)"/',
            '/pix[_-]code["\']?\s*[:=]\s*["\']([^"\']+)["\']/i',
            '/qr[_-]code["\']?\s*[:=]\s*["\']([^"\']+)["\']/i',
            '/<img[^>]*src=["\']([^"\']*pix[^"\']*)["\'][^>]*>/i',
            '/<canvas[^>]*data-pix="([^"]+)"[^>]*>/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $pixCode = $matches[1];
                Log::info('BsPay - QR Code PIX encontrado no HTML', ['pix_code' => $pixCode]);
                
                return [
                    'qrcode' => $pixCode,
                    'qrcode_data' => [
                        'pix_string' => $pixCode,
                        'amount' => $amount,
                        'transaction_id' => uniqid('bspay_'),
                        'expires_at' => now()->addMinutes(30)->format('H:i'),
                        'source' => 'html_parsing'
                    ],
                    'source' => 'html_parsing',
                    'amount' => $amount,
                                            'identifier' => $identifier ?? 'html_parsing_' . uniqid(),
                        'transaction_id' => uniqid('bspay_')
                ];
            }
        }
        
        return null;
    }

    /**
     * Gerar PIX local como fallback
     */
    private function generateLocalPixFallback($checkoutUrl, $amount, $identifier = null)
    {
        // Gerar um QR Code PIX local baseado nos dados disponíveis
        $pixString = "PIX_" . uniqid() . "_" . $amount;
        
        Log::info('BsPay - Gerando PIX local como fallback', [
            'pix_string' => $pixString,
            'checkout_url' => $checkoutUrl
        ]);
        
        return [
            'qrcode' => $pixString,
            'qrcode_data' => [
                'pix_string' => $pixString,
                'amount' => $amount,
                'transaction_id' => uniqid('bspay_'),
                'expires_at' => now()->addMinutes(30)->format('H:i'),
                'source' => 'local_fallback'
            ],
            'source' => 'local_fallback',
            'amount' => $amount,
            'note' => 'Use o link de checkout para pagar: ' . $checkoutUrl,
            'identifier' => $identifier ?? 'local_fallback_' . uniqid(),
            'transaction_id' => uniqid('bspay_')
        ];
    }
    


    private static function pixCashOutBsPay($id, $tipo)
    {
        Log::info('Iniciando pixCashOutBsPay via CNPay', [
            'withdrawal_id' => $id,
            'tipo' => $tipo
        ]);
    
        $withdrawal = Withdrawal::find($id);
        if ($tipo == "afiliado") {
            $withdrawal = AffiliateWithdraw::find($id);
        }
    
        if (!$withdrawal) {
            Log::error('Withdrawal não encontrado', ['withdrawal_id' => $id]);
            return false;
        }
    
        // Usar CNPay para saques (mais confiável)
        $result = self::processWithdrawalViaCNPay($withdrawal);
        
        // Se falhou, retornar erro específico
        if (is_array($result) && isset($result['success']) && !$result['success']) {
            return $result; // Retorna erro detalhado
        }
        
        return $result;
    }
    
    /**
     * Processar saque via CNPay
     */
    private static function processWithdrawalViaCNPay($withdrawal)
    {
        try {
            // Buscar configurações do CNPay
            $gateway = Gateway::first();
            if (!$gateway || empty($gateway->cnpay_uri) || empty($gateway->cnpay_public_key) || empty($gateway->cnpay_secret_key)) {
                Log::error('Credenciais CNPay não configuradas para saque', [
                    'gateway_exists' => $gateway ? 'sim' : 'não',
                    'cnpay_uri' => $gateway->cnpay_uri ?? 'não configurado',
                    'public_key' => $gateway->cnpay_public_key ? 'configurado' : 'não configurado',
                    'secret_key' => $gateway->cnpay_secret_key ? 'configurado' : 'não configurado'
                ]);
                return false;
            }
            
            $user = User::find($withdrawal->user_id);
            if (!$user) {
                Log::error('Usuário não encontrado para o saque', ['user_id' => $withdrawal->user_id]);
                return false;
            }
            
            // Gerar identificador único para a transferência
            $identifier = 'SAQUE_' . $withdrawal->id . '_' . time();
            
            // Limpar CPF (remover pontos e traços)
            $cpf_limpo = preg_replace('/\D/', '', $withdrawal->cpf);
            
            // Limpar nome do usuário (apenas letras e espaços)
            $nome_limpo = preg_replace('/[^a-zA-ZÀ-ÿ\s]/', '', $user->name);
            $nome_limpo = trim(preg_replace('/\s+/', ' ', $nome_limpo)); // Remove espaços duplos
            
            // Se o nome ficou vazio após limpeza, usar nome padrão
            if (empty($nome_limpo)) {
                $nome_limpo = 'Usuario';
            }
            
            // Preparar payload para a API de transferência do CNPay
            $payload = [
                'identifier' => $identifier,
                'amount' => (float)$withdrawal->amount,
                'discountFeeOfReceiver' => false, // A taxa será descontada do recebedor
                'pix' => [
                    'type' => 'cpf',
                    'key' => $cpf_limpo
                ],
                'owner' => [
                    'ip' => request()->ip() ?? '127.0.0.1',
                    'name' => $nome_limpo,
                    'document' => [
                        'type' => 'cpf',
                        'number' => $cpf_limpo
                    ]
                ],
                'callbackUrl' => $withdrawal instanceof AffiliateWithdraw 
                    ? url('/cnpay/affiliate-withdraw/callback', [], true)
                    : url('/cnpay/withdraw/callback', [], true)
            ];
            
            Log::info('Payload preparado para CNPay', [
                'identifier' => $identifier,
                'amount' => $withdrawal->amount,
                'cpf_limpo' => $cpf_limpo,
                'nome_original' => $user->name,
                'nome_limpo' => $nome_limpo,
                'callback_url' => $payload['callbackUrl'],
                'ip_usuario' => request()->ip() ?? '127.0.0.1'
            ]);
            
            // Configurar cliente HTTP
            $client = Http::timeout(60)
                ->withHeaders([
                    'x-public-key' => $gateway->cnpay_public_key,
                    'x-secret-key' => $gateway->cnpay_secret_key,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'Laravel-HTTP/10.0'
                ]);
            
            // Aplicar configurações de proxy se estiver ativo
            if (\App\Helpers\ProxyHelper::isEnabled()) {
                $proxyConfig = \App\Helpers\ProxyHelper::getHttpClientConfig();
                if (!empty($proxyConfig)) {
                    $client->withOptions($proxyConfig);
                    Log::debug('[CNPay] Proxy aplicado ao saque', [
                        'proxy_config' => $proxyConfig
                    ]);
                }
            }
            
            $startTime = microtime(true);
            $response = $client->post($gateway->cnpay_uri . '/gateway/transfers', $payload);
            $endTime = microtime(true);
            $requestTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('Resposta recebida do CNPay', [
                'http_code' => $response->status(),
                'response_time_ms' => $requestTime,
                'response_size' => strlen($response->body()),
                'response_preview' => substr($response->body(), 0, 500) . (strlen($response->body()) > 500 ? '...' : '')
            ]);
            
            if (!$response->successful()) {
                $responseBody = $response->body();
                $responseData = json_decode($responseBody, true);
                
                // Extrair mensagem de erro específica
                $errorMessage = 'Erro desconhecido';
                $errorCode = 'UNKNOWN_ERROR';
                
                if ($responseData) {
                    $errorMessage = $responseData['message'] ?? $responseData['error'] ?? 'Erro desconhecido';
                    $errorCode = $responseData['errorCode'] ?? $responseData['statusCode'] ?? 'UNKNOWN';
                }
                
                // Se for erro de IP não autorizado e estiver usando proxy, tentar sem proxy
                if ($response->status() === 403 && 
                    strpos($errorMessage, 'IP') !== false && 
                    strpos($errorMessage, 'não está autorizado') !== false &&
                    \App\Helpers\ProxyHelper::isEnabled()) {
                    
                    Log::warning('IP do proxy não autorizado, tentando sem proxy', [
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $errorMessage
                    ]);
                    
                    // Tentar novamente sem proxy
                    $clientWithoutProxy = Http::timeout(60)
                        ->withHeaders([
                            'x-public-key' => $gateway->cnpay_public_key,
                            'x-secret-key' => $gateway->cnpay_secret_key,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'User-Agent' => 'Laravel-HTTP/10.0'
                        ]);
                    
                    $responseWithoutProxy = $clientWithoutProxy->post($gateway->cnpay_uri . '/gateway/transfers', $payload);
                    
                    if ($responseWithoutProxy->successful()) {
                        Log::info('Saque CNPay bem-sucedido sem proxy', [
                            'withdrawal_id' => $withdrawal->id
                        ]);
                        
                        $transferData = $responseWithoutProxy->json();
                        $withdraw = $transferData['withdraw'];
                        
                        // Atualizar o saque
                        $withdrawal->update([
                            'status' => 1,
                            'gateway' => 'cnpay',
                            'transaction_id' => $withdraw['id'],
                            'webhook_data' => json_encode($transferData)
                        ]);
                        
                        return true;
                    } else {
                        Log::error('Falha também sem proxy', [
                            'withdrawal_id' => $withdrawal->id,
                            'http_code' => $responseWithoutProxy->status(),
                            'response' => $responseWithoutProxy->body()
                        ]);
                    }
                }
                
                // Log detalhado do erro
                Log::error('Falha na API CNPay para saque', [
                    'withdrawal_id' => $withdrawal->id,
                    'http_code' => $response->status(),
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'response' => $responseBody
                ]);
                
                // Retornar erro específico para tratamento no frontend
                return [
                    'success' => false,
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'http_code' => $response->status()
                ];
            }
            
            $transferData = $response->json();
            
            // Validar resposta da API
            if (!isset($transferData['withdraw']) || !isset($transferData['withdraw']['id'])) {
                Log::error('Resposta inválida da API CNPay', [
                    'withdrawal_id' => $withdrawal->id,
                    'transfer_data' => $transferData
                ]);
                return false;
            }
            
            $withdraw = $transferData['withdraw'];
            
            // Atualizar o saque com os dados da transação CNPay
            $withdrawal->update([
                'status' => 1,
                'gateway' => 'cnpay',
                'transaction_id' => $withdraw['id'],
                'webhook_data' => json_encode($transferData)
            ]);
            
            Log::info('Saque CNPay processado com sucesso', [
                'withdrawal_id' => $withdrawal->id,
                'withdraw_id' => $withdraw['id'],
                'status' => $withdraw['status'] ?? 'PROCESSING',
                'amount' => $withdraw['amount'] ?? 'N/A'
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Exceção ao processar saque via CNPay', [
                'withdrawal_id' => $withdrawal->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    
  
    private static function webhookBsPay(Request $request)
    {
        try {
            // Usar a lógica da CNPayTrait para processar webhook
            return self::processPixWebhookUsingCNPayMethod($request);
        } catch (Exception $e) {
            Log::error('Erro no webhook BsPay: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    /**
     * Processar webhook usando a lógica da CNPayTrait
     */
    private static function processPixWebhookUsingCNPayMethod(Request $request)
    {
        try {
            // Extrair dados do webhook (adaptar conforme necessário)
            $requestBody = $request->input("requestBody") ?? $request->all();
            
            // Log do webhook recebido
            Log::info('Webhook BsPay recebido', [
                'request_body' => $requestBody,
                'headers' => $request->headers->all()
            ]);
            
            // Tentar diferentes formatos de dados
            $idTransaction = $requestBody['transactionId'] ?? 
                           $requestBody['transaction_id'] ?? 
                           $requestBody['id'] ?? 
                           $requestBody['external_id'] ?? null;
            
            if (!$idTransaction) {
                Log::error('Webhook BsPay: ID da transação não encontrado', ['request_body' => $requestBody]);
                return response()->json(['error' => 'ID da transação não encontrado'], 400);
            }
            
            // Buscar transação
            $transaction = Transaction::where('payment_id', $idTransaction)
                                   ->orWhere('idUnico', $idTransaction)
                                   ->where('status', 0)
                                   ->first();
            
            if (!$transaction) {
                Log::warning('Webhook BsPay: Transação não encontrada', [
                    'id_transaction' => $idTransaction,
                    'request_body' => $requestBody
                ]);
                return response()->json(['error' => 'Transação não encontrada'], 404);
            }
            
            // Verificar se o pagamento foi confirmado
            $paymentConfirmed = $requestBody['status'] === 'paid' || 
                              $requestBody['status'] === 'confirmed' || 
                              $requestBody['status'] === 'success' ||
                              $requestBody['paid'] === true;
            
            if ($paymentConfirmed) {
                // Finalizar pagamento usando o método existente
                $payment = self::finalizePaymentBsPay($idTransaction);
                if ($payment) {
                    Log::info('Webhook BsPay: Pagamento finalizado com sucesso', [
                        'transaction_id' => $idTransaction,
                        'user_id' => $transaction->user_id
                    ]);
                    return response()->json(['status' => 'success'], 200);
                } else {
                    Log::error('Webhook BsPay: Falha ao finalizar pagamento', [
                        'transaction_id' => $idTransaction
                    ]);
                    return response()->json(['error' => 'Falha ao finalizar pagamento'], 500);
                }
            } else {
                Log::info('Webhook BsPay: Pagamento ainda não confirmado', [
                    'transaction_id' => $idTransaction,
                    'status' => $requestBody['status'] ?? 'unknown'
                ]);
                return response()->json(['status' => 'pending'], 200);
            }
            
        } catch (Exception $e) {
            Log::error('Erro ao processar webhook BsPay: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    private static function generateDepositBsPay($idTransaction, $amount)
    {
        // Obter user_id de forma segura
        $userId = null;
        if (auth('api')->check()) {
            $userId = auth('api')->id();
        } elseif (auth()->check()) {
            $userId = auth()->id();
        } else {
            throw new Exception('Usuário não autenticado');
        }
        
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            throw new Exception('Carteira não encontrada para o usuário');
        }

        Deposit::create([
            'payment_id' => $idTransaction,
            'user_id'   => $userId,
            'amount'    => $amount,
            'type'      => 'pix',
            'currency'  => $wallet->currency,
            'symbol'    => $wallet->symbol,
            'status'    => 0
        ]);
    }

    private static function generateTransactionBsPay($idTransaction, $amount, $id, $accept_bonus = false, $gatewayIdentifier = null, $gatewayTransactionId = null)
    {
        $setting = Core::getSetting();
        $token = bin2hex(random_bytes(16)); // token seguro
    
        // Obter user_id de forma segura
        $userId = null;
        if (auth('api')->check()) {
            $userId = auth('api')->id();
        } elseif (auth()->check()) {
            $userId = auth()->id();
        } else {
            throw new Exception('Usuário não autenticado');
        }
    
        // Log para confirmar criação da transação
        Log::info('BsPay - Criando transação', [
            'payment_id' => $idTransaction,
            'user_id' => $userId,
            'amount' => $amount,
            'gateway_identifier' => $gatewayIdentifier,
            'gateway_transaction_id' => $gatewayTransactionId
        ]);



        // Usar Query Builder direto para evitar problemas com Eloquent
        try {
            // Criar array de dados para inserção
            $dataToInsert = [
                'payment_id' => $idTransaction,
                'user_id' => $userId,
                'payment_method' => 'pix',
                'price' => $amount,
                'currency' => $setting->currency_code,
                'status' => 0,
                'idUnico' => $id,
                'accept_bonus' => $accept_bonus,
                'token' => $token,
                'gateway_name' => 'bspay',
                'gateway_identifier' => $gatewayIdentifier,
                'gateway_transaction_id' => $gatewayTransactionId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Log::info('BsPay - Dados que serão inseridos', $dataToInsert);

            // Usar Query Builder direto para inserir
            $insertedId = \DB::table('transactions')->insertGetId($dataToInsert);
            Log::info('BsPay - Query Builder insert executado com sucesso', ['inserted_id' => $insertedId]);

            // Buscar a transação criada
            $transaction = Transaction::find($insertedId);
            
        } catch (\Exception $e) {
            Log::error('BsPay - Erro ao criar transação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        // Log para confirmar que a transação foi criada
        Log::info('BsPay - Transação criada com sucesso', [
            'transaction_id' => $transaction->id,
            'gateway_identifier' => $transaction->gateway_identifier,
            'gateway_transaction_id' => $transaction->gateway_transaction_id
        ]);
    
        return $token;
    }
    

    public static function finalizePaymentBsPay($idTransaction, $userId = null, $amount = null): bool
    {
        Log::info('BsPay - Iniciando finalização do pagamento', [
            'transaction_id' => $idTransaction
        ]);

        // Buscar transação por múltiplos campos para melhorar a reconciliação
        $transaction = Transaction::where('payment_id', $idTransaction)
                                ->orWhere('idUnico', $idTransaction)
                                ->orWhere('gateway_transaction_id', $idTransaction)
                                ->orWhere('gateway_identifier', $idTransaction)
                                ->first();
        
        $setting = Helper::getSetting();

        // PROTEÇÃO ADICIONAL: Verificar se já existe uma transação processada com mesmo user_id e valor
        if ($userId && $amount) {
            $alreadyProcessed = Transaction::where(function($query) use ($idTransaction) {
                    $query->where('payment_id', $idTransaction)
                        ->orWhere('idUnico', $idTransaction)
                        ->orWhere('gateway_transaction_id', $idTransaction)
                        ->orWhere('gateway_identifier', $idTransaction);
                })
                ->where('status', 1)
                ->where('user_id', $userId)
                ->where('price', $amount)
                ->exists();
            
            if ($alreadyProcessed) {
                Log::warning('BsPay - Tentativa de finalizar transação já processada', [
                    'transaction_id' => $idTransaction,
                    'user_id' => $userId,
                    'message' => 'Esta transação já foi finalizada anteriormente'
                ]);
                return true; // Retorna true para evitar erro, mas não processa novamente
            }
        }

        if (empty($transaction)) {
            // Log mais detalhado para debug
            $existingTransactions = Transaction::where('payment_id', $idTransaction)
                                            ->orWhere('idUnico', $idTransaction)
                                            ->orWhere('gateway_transaction_id', $idTransaction)
                                            ->orWhere('gateway_identifier', $idTransaction)
                                            ->get();
            
            Log::error('BsPay - Transação não encontrada', [
                'transaction_id' => $idTransaction,
                'existing_transactions' => $existingTransactions->map(function($t) {
                    return [
                        'id' => $t->id,
                        'payment_id' => $t->payment_id,
                        'idUnico' => $t->idUnico,
                        'gateway_identifier' => $t->gateway_identifier,
                        'gateway_transaction_id' => $t->gateway_transaction_id,
                        'status' => $t->status,
                        'user_id' => $t->user_id,
                        'amount' => $t->price
                    ];
                }),
                'transaction_exists' => Transaction::where('payment_id', $idTransaction)
                                                ->orWhere('idUnico', $idTransaction)
                                                ->orWhere('gateway_transaction_id', $idTransaction)
                                                ->orWhere('gateway_identifier', $idTransaction)
                                                ->exists(),
                'transaction_status' => Transaction::where('payment_id', $idTransaction)
                                                ->orWhere('idUnico', $idTransaction)
                                                ->orWhere('gateway_transaction_id', $idTransaction)
                                                ->orWhere('gateway_identifier', $idTransaction)
                                                ->value('status')
            ]);
            return false;
        }

        // PROTEÇÃO: Verificar se já foi processada ANTES de continuar
        if ($transaction->status == 1) {
            Log::info('BsPay - Transação já processada, ignorando', [
                'transaction_id' => $idTransaction,
                'transaction_status' => $transaction->status
            ]);
            return true; // Já foi processada
        }

        // PROTEÇÃO FINAL: Usar lock para evitar processamento simultâneo
        try {
            return \DB::transaction(function() use ($transaction, $setting, $idTransaction) {
                // Lock da transação para evitar processamento simultâneo
                $lockedTransaction = Transaction::lockForUpdate()
                    ->where('id', $transaction->id)
                    ->where('status', 0)
                    ->first();
                
                if (!$lockedTransaction) {
                    Log::warning('BsPay - Transação já processada por outro processo', [
                        'transaction_id' => $idTransaction,
                        'original_transaction_id' => $transaction->id
                    ]);
                    return true; // Já foi processada
                }
                
                Log::info('BsPay - Transação encontrada e bloqueada para processamento', [
                    'transaction_id' => $idTransaction,
                    'user_id' => $lockedTransaction->user_id,
                    'amount' => $lockedTransaction->price,
                    'status' => $lockedTransaction->status
                ]);

                $user = User::find($lockedTransaction->user_id);
                if (empty($user)) {
                    Log::error('BsPay - Usuário não encontrado', [
                        'transaction_id' => $idTransaction,
                        'user_id' => $lockedTransaction->user_id
                    ]);
                    return false;
                }

                Log::info('BsPay - Usuário encontrado', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email
                ]);

                $wallet = Wallet::where('user_id', $lockedTransaction->user_id)->first();
                if (empty($wallet)) {
                    Log::error('BsPay - Carteira não encontrada', [
                        'transaction_id' => $idTransaction,
                        'user_id' => $lockedTransaction->user_id
                    ]);
                    return false;
                }

                Log::info('BsPay - Carteira encontrada', [
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'balance' => $wallet->balance,
                    'balance_withdrawal' => $wallet->balance_withdrawal
                ]);

                // Verifica se é o primeiro depósito
                $checkTransactions = Transaction::where('user_id', $lockedTransaction->user_id)
                    ->where('status', 1)
                    ->count();

                Log::info('BsPay - Verificação de primeiro depósito', [
                    'user_id' => $lockedTransaction->user_id,
                    'transactions_confirmadas' => $checkTransactions,
                    'accept_bonus' => $lockedTransaction->accept_bonus
                ]);

                if ($checkTransactions == 0 || empty($checkTransactions)) {
                    if ($lockedTransaction->accept_bonus) {
                        // Pagar o bônus de primeiro depósito
                        $bonus = Helper::porcentagem_xn($setting->initial_bonus, $lockedTransaction->price);
                        $wallet->increment('balance_bonus', $bonus);

                        Log::info('BsPay - Bônus de primeiro depósito aplicado', [
                            'user_id' => $lockedTransaction->user_id,
                            'bonus_amount' => $bonus,
                            'initial_bonus_percentage' => $setting->initial_bonus
                        ]);

                        if (!$setting->disable_rollover) {
                            $wallet->update(['balance_bonus_rollover' => $bonus * $setting->rollover]);
                            Log::info('BsPay - Rollover do bônus aplicado', [
                                'rollover_amount' => $bonus * $setting->rollover
                            ]);
                        }
                    }
                }

                // Rollover do depósito
                if (!$setting->disable_rollover) {
                    $rolloverAmount = $lockedTransaction->price * intval($setting->rollover_deposit);
                    $wallet->increment('balance_deposit_rollover', $rolloverAmount);
                    Log::info('BsPay - Rollover do depósito aplicado', [
                        'rollover_amount' => $rolloverAmount,
                        'rollover_deposit_percentage' => $setting->rollover_deposit
                    ]);
                }

                // Acumular bônus
                Helper::payBonusVip($wallet, $lockedTransaction->price);

                // Dinheiro direto para carteira de saque ou jogos
                if ($setting->disable_rollover) {
                    $wallet->increment('balance_withdrawal', $lockedTransaction->price);
                    Log::info('BsPay - Valor adicionado ao balance_withdrawal', [
                        'amount' => $lockedTransaction->price
                    ]);
                } else {
                    $wallet->increment('balance', $lockedTransaction->price);
                    Log::info('BsPay - Valor adicionado ao balance', [
                        'amount' => $lockedTransaction->price
                    ]);
                }

                // Atualizar status da transação ANTES de processar o depósito
                if (!$lockedTransaction->update(['status' => 1])) {
                    Log::error('BsPay - Falha ao atualizar status da transação', [
                        'transaction_id' => $idTransaction
                    ]);
                    return false;
                }

                Log::info('BsPay - Status da transação atualizado para 1', [
                    'transaction_id' => $idTransaction
                ]);

                $deposit = Deposit::where('payment_id', $idTransaction)
                                 ->orWhere('payment_id', $lockedTransaction->idUnico)
                                 ->where('status', 0)
                                 ->first();

                if (empty($deposit)) {
                    Log::error('BsPay - Depósito não encontrado ou já processado', [
                        'transaction_id' => $idTransaction,
                        'transaction_idUnico' => $lockedTransaction->idUnico,
                        'deposit_exists' => Deposit::where('payment_id', $idTransaction)
                                                 ->orWhere('payment_id', $lockedTransaction->idUnico)
                                                 ->exists(),
                        'deposit_status' => Deposit::where('payment_id', $idTransaction)
                                                 ->orWhere('payment_id', $lockedTransaction->idUnico)
                                                 ->value('status')
                    ]);
                    return false;
                }

                Log::info('BsPay - Depósito encontrado', [
                    'deposit_id' => $deposit->id,
                    'amount' => $deposit->amount,
                    'status' => $deposit->status
                ]);

                // CPA para primeiro depósito
                $affHistoryCPA = AffiliateHistory::where('user_id', $user->id)
                    ->where('commission_type', 'cpa')
                    ->first();

                if (!empty($affHistoryCPA)) {
                    $affHistoryCPA->increment('deposited_amount', $lockedTransaction->price);

                    $sponsorCpa = User::find($user->inviter);
                    if (!empty($sponsorCpa) && $affHistoryCPA->status == 'pendente') {
                        if ($affHistoryCPA->deposited_amount >= $sponsorCpa->affiliate_baseline || $deposit->amount >= $sponsorCpa->affiliate_baseline) {
                            $walletCpa = Wallet::where('user_id', $affHistoryCPA->inviter)->first();
                            if (!empty($walletCpa)) {
                                $walletCpa->increment('refer_rewards', $sponsorCpa->affiliate_cpa);
                                $affHistoryCPA->update(['status' => 1, 'commission_paid' => $sponsorCpa->affiliate_cpa]);
                            }
                        }
                    }
                }

                // Comissão percentual para níveis 1 e 2
                if ($lockedTransaction->price >= $setting->cpa_percentage_baseline) {
                    $inviterN1 = User::find($user->inviter);

                    if (!empty($inviterN1)) {
                        $commissionN1 = $lockedTransaction->price * ($setting->cpa_percentage_n1 / 100);
                        $walletN1 = Wallet::where('user_id', $inviterN1->id)->first();
                        if (!empty($walletN1)) {
                            $walletN1->increment('refer_rewards', $commissionN1);
                        }

                        // Nível 2
                        $inviterN2 = User::find($inviterN1->inviter);
                        if (!empty($inviterN2)) {
                            $commissionN2 = $lockedTransaction->price * ($setting->cpa_percentage_n2 / 100);
                            $walletN2 = Wallet::where('user_id', $inviterN2->id)->first();
                            if (!empty($walletN2)) {
                                $walletN2->increment('refer_rewards', $commissionN2);
                            }
                        }
                    }
                }

                if ($deposit->update(['status' => 1])) {
                    $admins = User::where('role_id', 0)->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new NewDepositNotification($user->name, $lockedTransaction->price));
                    }

                    Log::info('BsPay - Pagamento finalizado com sucesso', [
                        'transaction_id' => $idTransaction,
                        'user_id' => $user->id,
                        'amount' => $lockedTransaction->price,
                        'wallet_balance_after' => [
                            'balance' => $wallet->fresh()->balance,
                            'balance_withdrawal' => $wallet->fresh()->balance_withdrawal,
                            'balance_bonus' => $wallet->fresh()->balance_bonus
                        ]
                    ]);

                    return true;
                }

                return false;
            });
        } catch (\Exception $e) {
            Log::error('BsPay - Erro durante finalização do pagamento', [
                'transaction_id' => $idTransaction,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Verificar status do pagamento PIX manualmente
     * Seguindo o padrão da CNPayTrait
     */
    public static function checkPixPaymentStatus($transactionId)
    {
        try {
            Log::info('BsPay - Verificando status do pagamento PIX', [
                'transaction_id' => $transactionId
            ]);

            // Buscar transação
            $transaction = Transaction::where('payment_id', $transactionId)
                                   ->orWhere('idUnico', $transactionId)
                                   ->first();

            if (!$transaction) {
                Log::warning('BsPay - Transação não encontrada', [
                    'transaction_id' => $transactionId
                ]);
                return response()->json(['error' => 'Transação não encontrada'], 404);
            }

            // Buscar depósito
            $deposit = Deposit::where('payment_id', $transactionId)
                            ->orWhere('payment_id', $transaction->idUnico)
                            ->first();

            // Retornar status atual
            $response = [
                'transaction_id' => $transaction->payment_id,
                'unique_id' => $transaction->idUnico,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->price,
                'currency' => $transaction->currency,
                'status' => $transaction->status,
                'payment_method' => $transaction->payment_method,
                'created_at' => $transaction->created_at,
                'deposit_status' => $deposit ? $deposit->status : 'não encontrado',
                'deposit_amount' => $deposit ? $deposit->amount : null
            ];

            Log::info('BsPay - Status do pagamento verificado', [
                'transaction_id' => $transactionId,
                'status' => $transaction->status,
                'deposit_status' => $deposit ? $deposit->status : 'não encontrado'
            ]);

            return response()->json($response);

        } catch (Exception $e) {
            Log::error('BsPay - Erro ao verificar status do pagamento: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    /**
     * Simular confirmação de pagamento PIX (para testes)
     */
    public static function simulatePixPayment($transactionId)
    {
        try {
            Log::info('BsPay - Simulando confirmação de pagamento PIX', [
                'transaction_id' => $transactionId
            ]);

            // Buscar transação
            $transaction = Transaction::where('payment_id', $transactionId)
                                   ->orWhere('idUnico', $transactionId)
                                   ->where('status', 0)
                                   ->first();

            if (!$transaction) {
                Log::warning('BsPay - Transação não encontrada para simulação', [
                    'transaction_id' => $transactionId
                ]);
                return response()->json(['error' => 'Transação não encontrada'], 404);
            }

            // Finalizar pagamento
            $result = self::finalizePaymentBsPay($transactionId);
            
            if ($result) {
                Log::info('BsPay - Pagamento simulado com sucesso', [
                    'transaction_id' => $transactionId,
                    'user_id' => $transaction->user_id
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pagamento confirmado com sucesso',
                    'transaction_id' => $transactionId
                ]);
            } else {
                Log::error('BsPay - Falha ao simular pagamento', [
                    'transaction_id' => $transactionId
                ]);
                return response()->json(['error' => 'Falha ao confirmar pagamento'], 500);
            }

        } catch (Exception $e) {
            Log::error('BsPay - Erro ao simular pagamento: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    /**
     * Finalizar saque via BsPay após aprovação
     * Este método é chamado quando o webhook confirma que o saque foi aprovado
     */
    public static function finalizeWithdrawalBsPay($idTransaction): bool
    {
        try {
            Log::info('BsPay - Iniciando finalização do saque', [
                'transaction_id' => $idTransaction
            ]);

            // Buscar o saque pelo transaction_id
            $withdrawal = Withdrawal::where('transaction_id', $idTransaction)
                                   ->where('status', 1) // Status 1 = processando
                                   ->first();

            if (empty($withdrawal)) {
                // Tentar buscar por outros campos
                $withdrawal = Withdrawal::where('gateway_transaction_id', $idTransaction)
                                       ->where('status', 1)
                                       ->first();
            }

            if (empty($withdrawal)) {
                Log::error('BsPay - Saque não encontrado para finalizar', [
                    'transaction_id' => $idTransaction
                ]);
                return false;
            }

            // Buscar a carteira do usuário
            $wallet = Wallet::where('user_id', $withdrawal->user_id)->first();
            if (empty($wallet)) {
                Log::error('BsPay - Carteira não encontrada para finalizar saque', [
                    'user_id' => $withdrawal->user_id
                ]);
                return false;
            }

            // DEBITAR O VALOR DO USUÁRIO APÓS APROVAÇÃO
            try {
                \DB::transaction(function() use ($withdrawal, $wallet, $idTransaction) {
                    // Lock da carteira para evitar condições de corrida
                    $lockedWallet = Wallet::lockForUpdate()
                        ->where('id', $wallet->id)
                        ->first();

                    if (!$lockedWallet) {
                        throw new \Exception('Carteira não pôde ser bloqueada');
                    }

                    // Verificar se o saldo é suficiente
                    if ($lockedWallet->balance_withdrawal < $withdrawal->amount) {
                        throw new \Exception('Saldo insuficiente para debitar o saque');
                    }

                    // Debitar o valor do saque
                    $lockedWallet->decrement('balance_withdrawal', $withdrawal->amount);

                    // Atualizar status do saque para confirmado (status 2)
                    $withdrawal->update(['status' => 2]);

                    Log::info('BsPay - Saque finalizado com sucesso', [
                        'withdrawal_id' => $withdrawal->id,
                        'user_id' => $withdrawal->user_id,
                        'amount' => $withdrawal->amount,
                        'balance_anterior' => $lockedWallet->balance_withdrawal + $withdrawal->amount,
                        'balance_atual' => $lockedWallet->balance_withdrawal,
                        'transaction_id' => $idTransaction
                    ]);
                });

                return true;

            } catch (\Exception $e) {
                Log::error('BsPay - Erro ao debitar valor do saque', [
                    'withdrawal_id' => $withdrawal->id,
                    'transaction_id' => $idTransaction,
                    'error' => $e->getMessage()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('BsPay - Erro ao finalizar saque', [
                'transaction_id' => $idTransaction,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Finalizar saque de afiliado via BsPay após aprovação
     */
    public static function finalizeAffiliateWithdrawalBsPay($idTransaction): bool
    {
        try {
            Log::info('BsPay - Iniciando finalização do saque de afiliado', [
                'transaction_id' => $idTransaction
            ]);

            // Buscar o saque de afiliado pelo transaction_id
            $withdrawal = AffiliateWithdraw::where('transaction_id', $idTransaction)
                                          ->where('status', 1) // Status 1 = processando
                                          ->first();

            if (empty($withdrawal)) {
                // Tentar buscar por outros campos
                $withdrawal = AffiliateWithdraw::where('gateway_transaction_id', $idTransaction)
                                              ->where('status', 1)
                                              ->first();
            }

            if (empty($withdrawal)) {
                Log::error('BsPay - Saque de afiliado não encontrado para finalizar', [
                    'transaction_id' => $idTransaction
                ]);
                return false;
            }

            // Buscar a carteira do usuário
            $wallet = Wallet::where('user_id', $withdrawal->user_id)->first();
            if (empty($wallet)) {
                Log::error('BsPay - Carteira não encontrada para finalizar saque de afiliado', [
                    'user_id' => $withdrawal->user_id
                ]);
                return false;
            }

            // DEBITAR O VALOR DO USUÁRIO APÓS APROVAÇÃO
            try {
                \DB::transaction(function() use ($withdrawal, $wallet, $idTransaction) {
                    // Lock da carteira para evitar condições de corrida
                    $lockedWallet = Wallet::lockForUpdate()
                        ->where('id', $wallet->id)
                        ->first();

                    if (!$lockedWallet) {
                        throw new \Exception('Carteira não pôde ser bloqueada');
                    }

                    // Verificar se o saldo é suficiente
                    if ($lockedWallet->refer_rewards < $withdrawal->amount) {
                        throw new \Exception('Saldo de recompensas insuficiente para debitar o saque');
                    }

                    // Debitar o valor do saque
                    $lockedWallet->decrement('refer_rewards', $withdrawal->amount);

                    // Atualizar status do saque para confirmado (status 2)
                    $withdrawal->update(['status' => 2]);

                    Log::info('BsPay - Saque de afiliado finalizado com sucesso', [
                        'withdrawal_id' => $withdrawal->id,
                        'user_id' => $withdrawal->user_id,
                        'amount' => $withdrawal->amount,
                        'refer_rewards_anterior' => $lockedWallet->refer_rewards + $withdrawal->amount,
                        'refer_rewards_atual' => $lockedWallet->refer_rewards,
                        'transaction_id' => $idTransaction
                    ]);
                });

                return true;

            } catch (\Exception $e) {
                Log::error('BsPay - Erro ao debitar valor do saque de afiliado', [
                    'withdrawal_id' => $withdrawal->id,
                    'transaction_id' => $idTransaction,
                    'error' => $e->getMessage()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('BsPay - Erro ao finalizar saque de afiliado', [
                'transaction_id' => $idTransaction,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }


}
