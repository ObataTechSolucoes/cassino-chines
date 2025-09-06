<?php

namespace App\Traits\Gateways;

use App\Models\Gateway;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CNPayTrait
{
    private static $uriCNPay;
    private static $publicKeyCNPay;
    private static $secretKeyCNPay;
    private static $webhookUrlCNPay;
    private static $proxyConfig;

    /**
     * Inicializar configurações do CNPay
     */
    public static function initCNPay()
    {
        try {
        $setting = Gateway::first();
        if ($setting) {
            // Log das configurações brutas do banco
            Log::info('CNPay - Configurações brutas do banco', [
                'setting_id' => $setting->id ?? 'não definido',
                'setting_attributes' => $setting->getAttributes(),
                'cnpay_uri_raw' => $setting->getAttributes()['cnpay_uri'] ?? 'não definido',
                'cnpay_public_key_raw' => $setting->getAttributes()['cnpay_public_key'] ?? 'não definido',
                'cnpay_secret_key_raw' => $setting->getAttributes()['cnpay_secret_key'] ?? 'não definido',
                'cnpay_webhook_url_raw' => $setting->getAttributes()['cnpay_webhook_url'] ?? 'não definido',
            ]);
            
            self::$uriCNPay = $setting->getAttributes()['cnpay_uri'] ?? null;
            self::$publicKeyCNPay = $setting->getAttributes()['cnpay_public_key'] ?? null;
            self::$secretKeyCNPay = $setting->getAttributes()['cnpay_secret_key'] ?? null;
            self::$webhookUrlCNPay = $setting->getAttributes()['cnpay_webhook_url'] ?? null;
            
            // Configurações de proxy (pode ser configurado via variáveis de ambiente)
            // MAS só se estiver ativo no painel admin
            $proxyEnabled = \App\Models\ProxySetting::where('proxy_enabled', true)->exists();
            
            if ($proxyEnabled) {
                self::$proxyConfig = [
                    'http_proxy' => env('HTTP_PROXY', env('HTTP_PROXY_HOST')),
                    'https_proxy' => env('HTTPS_PROXY', env('HTTPS_PROXY_HOST')),
                    'proxy_user' => env('PROXY_USER'),
                    'proxy_pass' => env('PROXY_PASS'),
                    'proxy_port' => env('PROXY_PORT', '8080'),
                    'proxy_type' => env('PROXY_TYPE', 'http'), // http, https, socks4, socks5
                ];
            } else {
                // Proxy desativado no painel - não usar variáveis de ambiente
                self::$proxyConfig = [
                    'http_proxy' => null,
                    'https_proxy' => null,
                    'proxy_user' => null,
                    'proxy_pass' => null,
                    'proxy_port' => null,
                    'proxy_type' => 'http',
                ];
                
                Log::info('CNPay - Proxy desativado no painel admin, ignorando variáveis de ambiente');
            }
            
            // Log das configurações carregadas
            Log::info('CNPay - Configurações carregadas', [
                'uri' => self::$uriCNPay,
                'uri_length' => self::$uriCNPay ? strlen(self::$uriCNPay) : 0,
                'public_key' => self::$publicKeyCNPay ? 'configurado' : 'não configurado',
                'public_key_length' => self::$publicKeyCNPay ? strlen(self::$publicKeyCNPay) : 0,
                'secret_key' => self::$secretKeyCNPay ? 'configurado' : 'não configurado',
                'secret_key_length' => self::$secretKeyCNPay ? strlen(self::$secretKeyCNPay) : 0,
                'webhook_url' => self::$webhookUrlCNPay,
                'webhook_url_length' => self::$webhookUrlCNPay ? strlen(self::$webhookUrlCNPay) : 0,
                'all_configured' => !!(self::$uriCNPay && self::$publicKeyCNPay && self::$secretKeyCNPay),
                'proxy_config' => self::$proxyConfig
            ]);
        } else {
            Log::warning('CNPay - Nenhuma configuração de gateway encontrada');
                throw new \Exception('Configurações do gateway não encontradas no banco de dados');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Erro específico de banco de dados
            Log::error('CNPay - Erro de conexão com banco de dados: ' . $e->getMessage(), [
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'sql_state' => $e->getSqlState() ?? 'não disponível'
            ]);
            
            throw new \Exception('Sistema temporariamente indisponível - banco de dados offline: ' . $e->getMessage());
            
        } catch (\PDOException $e) {
            // Erro PDO (conexão)
            Log::error('CNPay - Erro PDO (conexão com banco): ' . $e->getMessage(), [
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'sql_state' => $e->getCode()
            ]);
            
            throw new \Exception('Sistema temporariamente indisponível - erro de conexão com banco: ' . $e->getMessage());
            
        } catch (\Exception $e) {
            // Outros erros
            Log::error('CNPay - Erro inesperado na inicialização: ' . $e->getMessage(), [
                'error_type' => get_class($e),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Erro inesperado na inicialização do CNPay: ' . $e->getMessage());
        }
    }

    /**
     * Configurar cliente HTTP com proxy se configurado
     */
    private static function getHttpClient($useProxy = true)
    {
        $client = Http::timeout(30);
        
        // Configurar proxy se disponível e solicitado
        if ($useProxy && self::$proxyConfig && (self::$proxyConfig['http_proxy'] || self::$proxyConfig['https_proxy'])) {
            $proxyUrl = self::buildProxyUrl();
            
            if ($proxyUrl) {
                Log::info('CNPay - 🔧 PROXY ATIVADO', [
                    'proxy_url' => $proxyUrl,
                    'proxy_type' => self::$proxyConfig['proxy_type'],
                    'proxy_host' => self::$proxyConfig['http_proxy'] ?? self::$proxyConfig['https_proxy'],
                    'proxy_port' => self::$proxyConfig['proxy_port'],
                    'proxy_user' => self::$proxyConfig['proxy_user'] ? 'configurado' : 'não configurado',
                    'proxy_pass' => self::$proxyConfig['proxy_pass'] ? 'configurado' : 'não configurado',
                    'use_proxy_param' => $useProxy,
                    'proxy_config_exists' => !!(self::$proxyConfig),
                    'http_proxy_set' => !!(self::$proxyConfig['http_proxy']),
                    'https_proxy_set' => !!(self::$proxyConfig['https_proxy'])
                ]);
                
                // Configurar proxy no cliente HTTP
                $client->withOptions([
                    'proxy' => $proxyUrl,
                    'verify' => false, // Desabilitar verificação SSL para proxy
                    'timeout' => 60, // Aumentar timeout para proxy
                ]);
                
                Log::info('CNPay - ✅ Opções do proxy aplicadas ao cliente HTTP');
            } else {
                Log::warning('CNPay - ⚠️ Proxy solicitado mas URL não pôde ser construída', [
                    'proxy_config' => self::$proxyConfig,
                    'use_proxy_param' => $useProxy
                ]);
            }
        } else {
            Log::info('CNPay - 🌐 Cliente HTTP SEM PROXY', [
                'use_proxy_param' => $useProxy,
                'proxy_config_exists' => !!(self::$proxyConfig),
                'proxy_config' => self::$proxyConfig ? 'configurado' : 'não configurado'
            ]);
        }
        
        return $client;
    }

    /**
     * Construir URL do proxy
     */
    private static function buildProxyUrl()
    {
        if (!self::$proxyConfig) {
            return null;
        }
        
        $proxyHost = self::$proxyConfig['http_proxy'] ?? self::$proxyConfig['https_proxy'];
        
        if (!$proxyHost) {
            return null;
        }
        
        $proxyUrl = self::$proxyConfig['proxy_type'] . '://';
        
        if (self::$proxyConfig['proxy_user'] && self::$proxyConfig['proxy_pass']) {
            $proxyUrl .= self::$proxyConfig['proxy_user'] . ':' . self::$proxyConfig['proxy_pass'] . '@';
        }
        
        $proxyUrl .= $proxyHost;
        
        if (self::$proxyConfig['proxy_port']) {
            $proxyUrl .= ':' . self::$proxyConfig['proxy_port'];
        }
        
        return $proxyUrl;
    }

    /**
     * Criar pagamento via CNPay com estratégias de bypass
     */
    public static function createCNPayPayment($amount, $currency = 'BRL', $description = 'Pagamento', $externalId = null, $clientData = null)
    {
        try {
            Log::info('CNPayTrait - createCNPayPayment iniciado', [
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
                'external_id' => $externalId,
                'client_data' => $clientData,
                'timestamp' => now()->toISOString(),
                'auth_api' => auth('api')->check(),
                'auth_web' => auth()->check(),
                'user_id_api' => auth('api')->id(),
                'user_id_web' => auth()->id()
            ]);

            // 🚨 VERIFICAÇÃO DE SEGURANÇA - Padrão dos outros gateways
            if (!auth('api')->check() && !auth()->check()) {
                Log::error('CNPay - Usuário não autenticado', [
                    'amount' => $amount,
                    'currency' => $currency,
                    'description' => $description,
                    'auth_api' => auth('api')->check(),
                    'auth_web' => auth()->check()
                ]);
                throw new \Exception('Usuário não autenticado');
            }

            Log::info('CNPayTrait - Usuário autenticado, inicializando CNPay');

            self::initCNPay();

            Log::info('CNPayTrait - CNPay inicializado', [
                'uri_configured' => !!(self::$uriCNPay),
                'public_key_configured' => !!(self::$publicKeyCNPay),
                'secret_key_configured' => !!(self::$secretKeyCNPay),
                'webhook_configured' => !!(self::$webhookUrlCNPay)
            ]);

            if (!self::$uriCNPay || !self::$publicKeyCNPay || !self::$secretKeyCNPay) {
                Log::error('CNPayTrait - Configurações do CNPay não encontradas', [
                    'uri' => self::$uriCNPay ? 'configurado' : 'não configurado',
                    'public_key' => self::$publicKeyCNPay ? 'configurado' : 'não configurado',
                    'secret_key' => self::$secretKeyCNPay ? 'configurado' : 'não configurado'
                ]);
                throw new \Exception('Configurações do CNPay não encontradas');
            }

            // 🚨 PAYLOAD MOLDADO EXATAMENTE COMO SOLICITADO
            $payload = [
                'identifier' => uniqid('cnpay_', true),
                'amount' => $amount,
                'client' => [
                    'name' => $clientData['name'] ?? 'João da Silva',
                    'email' => $clientData['email'] ?? 'cn_cassino@mail.com',
                    'phone' => '(11) 99999-9999', // 🎯 TELEFONE FIXO
                    'document' => '64289113028' // 🎯 CPF FIXO
                ],
                'products' => [
                    [
                        'id' => 'adicao_saldo_' . uniqid(),
                        'name' => 'Adição de Saldo - Carteira Digital',
                        'quantity' => 1,
                        'price' => $amount
                    ]
                ],
                'dueDate' => now()->addHours(24)->format('Y-m-d'),
                'metadata' => [
                    'transaction_type' => 'deposit',
                    'gateway' => 'cnpay',
                    'payment_method' => 'pix',
                    'user_id' => auth('api')->id() ?? auth()->id(),
                    'description' => $description
                ],
                'callbackUrl' => self::$webhookUrlCNPay
            ];

            // Log do payload corrigido
            Log::info('CNPay - Payload corrigido com campos obrigatórios', [
                'payload' => $payload,
                'payload_json' => json_encode($payload, JSON_PRETTY_PRINT),
                'client_data_received' => $clientData,
                'using_proxy' => !!(self::$proxyConfig && (self::$proxyConfig['http_proxy'] || self::$proxyConfig['https_proxy'])),
                'auth_info' => [
                    'user_authenticated' => auth()->check(),
                    'user_id' => auth()->id(),
                    'session_id' => session()->getId(),
                    'request_url' => request()->url(),
                    'request_method' => request()->method()
                ]
            ]);

            // Verificar conectividade primeiro
            try {
                Log::info('CNPay - Iniciando testes de conectividade...');
                
                // Teste 1: GET simples na URI base (SEM PROXY)
                $testClient = self::getHttpClient(false);
                $testResponse = $testClient->timeout(10)->get(self::$uriCNPay);
                
                Log::info('CNPay - Teste 1: GET na URI base (SEM PROXY)', [
                    'uri' => self::$uriCNPay,
                    'status' => $testResponse->status(),
                    'successful' => $testResponse->successful(),
                    'body_length' => strlen($testResponse->body()),
                    'body_preview' => substr($testResponse->body(), 0, 200),
                    'headers' => $testResponse->headers()
                ]);
                
                if ($testResponse->successful()) {
                    Log::info('CNPay - ✅ Conectividade OK (SEM PROXY)');
                } else {
                    Log::warning('CNPay - ❌ Conectividade falhou (SEM PROXY)', [
                        'status' => $testResponse->status(),
                        'body' => $testResponse->body()
                    ]);
                    
                    // Teste 2: GET simples na URI base (COM PROXY)
                    Log::info('CNPay - Teste 2: GET na URI base (COM PROXY)');
                    $testClientProxy = self::getHttpClient(true);
                    $testResponseProxy = $testClientProxy->timeout(10)->get(self::$uriCNPay);
                    
                    Log::info('CNPay - Teste 2: GET na URI base (COM PROXY)', [
                        'uri' => self::$uriCNPay,
                        'status' => $testResponseProxy->status(),
                        'successful' => $testResponseProxy->successful(),
                        'body_length' => strlen($testResponseProxy->body()),
                        'body_preview' => substr($testResponseProxy->body(), 0, 200),
                        'headers' => $testResponseProxy->headers()
                    ]);
                    
                    // Teste 3: POST simples no endpoint (SEM PROXY)
                    Log::info('CNPay - Teste 3: POST simples no endpoint (SEM PROXY)');
                    $testPostResponse = $testClient->timeout(10)->post(self::$uriCNPay . '/gateway/pix/receive', [
                        'test' => 'connection'
                    ]);
                    
                    Log::info('CNPay - Teste 3: POST simples no endpoint (SEM PROXY)', [
                        'uri' => self::$uriCNPay . '/gateway/pix/receive',
                        'status' => $testPostResponse->status(),
                        'successful' => $testPostResponse->successful(),
                        'body_length' => strlen($testPostResponse->body()),
                        'body_preview' => substr($testPostResponse->body(), 0, 200),
                        'headers' => $testPostResponse->headers()
                    ]);
                    
                    // Teste 4: POST simples no endpoint (COM PROXY)
                    Log::info('CNPay - Teste 4: POST simples no endpoint (COM PROXY)');
                    $testPostResponseProxy = $testClientProxy->timeout(10)->post(self::$uriCNPay . '/gateway/pix/receive', [
                        'test' => 'connection'
                    ]);
                    
                    Log::info('CNPay - Teste 4: POST simples no endpoint (COM PROXY)', [
                        'uri' => self::$uriCNPay . '/gateway/pix/receive',
                        'status' => $testPostResponseProxy->status(),
                        'successful' => $testPostResponseProxy->successful(),
                        'body_length' => strlen($testPostResponseProxy->body()),
                        'body_preview' => substr($testPostResponseProxy->body(), 0, 200),
                        'headers' => $testPostResponseProxy->headers()
                    ]);
                }
                
            } catch (\Exception $e) {
                Log::error('CNPay - Erro nos testes de conectividade: ' . $e->getMessage(), [
                    'error_type' => get_class($e),
                    'error_code' => $e->getCode(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Tentar diferentes estratégias de bypass
            $strategies = self::getBypassStrategies();
            $lastError = null;

            Log::info('CNPayTrait - Iniciando tentativas com estratégias de bypass', [
                'total_strategies' => count($strategies),
                'strategies' => array_column($strategies, 'name')
            ]);

            // 🚨 SEMPRE USAR PROXY DIRETAMENTE
            Log::info('CNPay - 🚀 INICIANDO REQUISIÇÕES DIRETAMENTE COM PROXY');
            
            foreach ($strategies as $index => $strategy) {
                try {
                    Log::info('CNPay - Tentativa ' . ($index + 1) . ' com estratégia: ' . $strategy['name'] . ' (COM PROXY)', [
                        'uri_completa' => self::$uriCNPay . '/gateway/pix/receive',
                        'method' => 'POST',
                        'strategy' => $strategy['name'],
                        'headers' => $strategy['headers'],
                        'payload' => $payload,
                        'using_proxy' => true
                    ]);
                    
                    $client = self::getHttpClient(true); // 🎯 SEMPRE COM PROXY
                    
                    // Log de confirmação do proxy
                    Log::info('CNPay - 🔧 REQUISIÇÃO COM PROXY', [
                        'strategy' => $strategy['name'],
                        'use_proxy' => true,
                        'proxy_config_exists' => !!(self::$proxyConfig),
                        'proxy_config' => self::$proxyConfig ? 'configurado' : 'não configurado',
                        'proxy_url' => self::buildProxyUrl()
                    ]);
                    
                    // Endpoint único e correto da API CNPay
                    $endpoints = [
                        '/gateway/pix/receive'
                    ];
                    
                    $response = null;
                    $endpointUsed = null;
                    
                    foreach ($endpoints as $endpoint) {
                        try {
                            Log::info('CNPay - Tentando endpoint: ' . $endpoint . ' (COM PROXY)', [
                                'uri_completa' => self::$uriCNPay . $endpoint,
                                'strategy' => $strategy['name'],
                                'proxy_status' => 'ATIVADO'
                            ]);
                            
                            $response = $client->withHeaders($strategy['headers'])
                                ->post(self::$uriCNPay . $endpoint, $payload);
                            
                            $endpointUsed = $endpoint;
                            break; // Se chegou aqui, não houve erro de conexão
                            
                        } catch (\Exception $e) {
                            Log::warning('CNPay - Endpoint falhou: ' . $endpoint . ' (COM PROXY)', [
                                'endpoint' => $endpoint,
                                'error' => $e->getMessage(),
                                'strategy' => $strategy['name'],
                                'proxy_status' => 'ATIVADO'
                            ]);
                            continue; // Tentar próximo endpoint
                        }
                    }
                    
                    if (!$response) {
                        throw new \Exception('Todos os endpoints falharam para a estratégia: ' . $strategy['name'] . ' (COM PROXY)');
                    }

                    Log::info('CNPayTrait - Resposta recebida da estratégia ' . $strategy['name'] . ' (COM PROXY)', [
                        'strategy' => $strategy['name'],
                        'status' => $response->status(),
                        'successful' => $response->successful(),
                        'body_length' => strlen($response->body()),
                        'headers' => $response->headers(),
                        'endpoint_used' => $endpointUsed,
                        'proxy_used' => true
                    ]);

                    if ($response->successful()) {
                        Log::info('CNPay - ✅ Sucesso com estratégia: ' . $strategy['name'] . ' (COM PROXY)', [
                            'strategy' => $strategy['name'],
                            'status' => $response->status(),
                            'endpoint_used' => $endpointUsed,
                            'using_proxy' => true,
                            'proxy_url' => self::buildProxyUrl()
                        ]);
                        
                        // Processar resposta bem-sucedida
                        $result = self::processSuccessfulResponse($response, $amount, $currency, $externalId);
                        
                        Log::info('CNPayTrait - Resposta processada com sucesso (COM PROXY)', [
                            'result' => $result,
                            'success' => $result['success'] ?? false,
                            'proxy_used' => true
                        ]);
                        
                        return $result;
                    }

                    // Se não foi bem-sucedido, tentar próxima estratégia
                    $lastError = 'Status ' . $response->status() . ' com estratégia ' . $strategy['name'] . ' (COM PROXY)';
                    Log::warning('CNPay - Estratégia falhou: ' . $strategy['name'] . ' (COM PROXY)', [
                        'strategy' => $strategy['name'],
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'endpoint_used' => $endpointUsed,
                        'using_proxy' => true,
                        'proxy_url' => self::buildProxyUrl()
                    ]);
                    
                } catch (\Exception $e) {
                    $lastError = 'Erro na estratégia ' . $strategy['name'] . ' (COM PROXY): ' . $e->getMessage();
                    Log::error('CNPay - Erro na estratégia: ' . $strategy['name'] . ' (COM PROXY)', [
                        'strategy' => $strategy['name'],
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'using_proxy' => true,
                        'proxy_url' => self::buildProxyUrl()
                    ]);
                }
            }
            
            // Se todas as estratégias falharam, usar método original como fallback
            Log::warning('CNPay - Todas as estratégias falharam, tentando método original como fallback');
            
            $client = self::getHttpClient(true); // 🎯 SEMPRE COM PROXY
            $response = $client->withHeaders([
                'X-Public-Key' => self::$publicKeyCNPay,
                'X-Secret-Key' => self::$secretKeyCNPay,
                'Content-Type' => 'application/json',
                'User-Agent' => 'Laravel-HTTP-Client',
            ])->post(self::$uriCNPay . '/gateway/pix/receive', $payload);

            Log::info('CNPayTrait - Resposta do método fallback', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_length' => strlen($response->body())
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log da resposta bem-sucedida
                Log::info('CNPay - Pagamento criado com sucesso', [
                    'amount' => $amount,
                    'currency' => $currency,
                    'response_status' => $response->status(),
                    'response_headers' => $response->headers(),
                    'response_body' => $data,
                    'response_body_json' => json_encode($data, JSON_PRETTY_PRINT),
                    'using_proxy' => !!(self::$proxyConfig && (self::$proxyConfig['http_proxy'] || self::$proxyConfig['https_proxy']))
                ]);
                
                // Criar transação no sistema com estrutura correta da API
                $userId = auth('api')->user()->id ?? auth()->id();
                
                // Log detalhado da autenticação
                Log::info('CNPay - Criando transação', [
                    'user_id_from_api_auth' => auth('api')->user()->id ?? 'não autenticado',
                    'user_id_from_web_auth' => auth()->id(),
                    'user_authenticated_api' => auth('api')->check(),
                    'user_authenticated_web' => auth()->check(),
                    'fallback_user_id' => $userId ?? 1,
                    'amount' => $amount,
                    'currency' => $currency
                ]);
                
                // Gerar token seguro como nos outros gateways
                $token = bin2hex(random_bytes(16));
                
                Log::info('CNPayTrait - Token gerado', [
                    'token' => $token,
                    'token_length' => strlen($token),
                    'token_type' => gettype($token),
                    'amount' => $amount,
                    'currency' => $currency,
                    'external_id' => $externalId
                ]);
                
                // Usar o método padrão dos outros gateways
                $token = self::generateCNPayTransaction(
                    $data['transactionId'] ?? $data['id'] ?? $externalId,
                    $amount,
                    false // accept_bonus
                );
                
                // Criar depósito usando o método padrão
                self::generateCNPayDeposit(
                    $data['transactionId'] ?? $data['id'] ?? $externalId,
                    $amount
                );
                
                // Buscar a transação criada para retornar o ID
                $transaction = Transaction::where('payment_id', $data['transactionId'] ?? $data['id'] ?? $externalId)
                    ->where('gateway_name', 'cnpay')
                    ->first();
                
                Log::info('CNPayTrait - Transação criada no banco', [
                    'transaction_id' => $transaction->id,
                    'payment_id' => $transaction->payment_id,
                    'user_id' => $transaction->user_id,
                    'token_saved' => $transaction->token,
                    'token_saved_check' => !empty($transaction->token) ? 'NÃO VAZIO' : 'VAZIO',
                    'token_original' => $token,
                    'token_match' => $transaction->token === $token ? 'SIM' : 'NÃO'
                ]);

                Log::info('CNPayTrait - Transação criada no banco', [
                    'transaction_id' => $transaction->id,
                    'payment_id' => $transaction->payment_id,
                    'user_id' => $transaction->user_id
                ]);

                // Retornar apenas os 3 dados essenciais como outros gateways
                return [
                    'success' => true,
                    'qrcode' => $data['pix']['code'] ?? $data['qr_code'] ?? $data['order']['url'] ?? null,
                    'token' => $token
                ];
            }

            // Log detalhado da resposta de erro
            $errorBody = $response->body();
            $errorJson = null;
            
            try {
                $errorJson = json_decode($errorBody, true);
            } catch (\Exception $e) {
                $errorJson = ['raw_body' => $errorBody];
            }
            
            Log::error('CNPay - Erro na API', [
                'amount' => $amount,
                'currency' => $currency,
                'response_status' => $response->status(),
                'response_headers' => $response->headers(),
                'response_headers_json' => json_encode($response->headers(), JSON_PRETTY_PRINT),
                'response_body' => $errorBody,
                'response_body_json' => $errorJson,
                'response_body_json_pretty' => json_encode($errorJson, JSON_PRETTY_PRINT),
                'uri' => self::$uriCNPay,
                'endpoint' => '/gateway/pix/receive',
                'using_proxy' => !!(self::$proxyConfig && (self::$proxyConfig['http_proxy'] || self::$proxyConfig['https_proxy'])),
                'request_info' => [
                    'method' => 'POST',
                    'uri_completa' => self::$uriCNPay . '/gateway/pix/receive',
                    'payload_enviado' => $payload,
                    'headers_enviados' => [
                        'X-Public-Key' => self::$publicKeyCNPay ? 'configurado' : 'não configurado',
                        'X-Secret-Key' => self::$secretKeyCNPay ? 'configurado' : 'não configurado',
                        'Content-Type' => 'application/json',
                        'User-Agent' => 'Laravel-HTTP-Client',
                    ]
                ]
            ]);

            throw new \Exception('Erro na API do CNPay: ' . $errorBody);

        } catch (\Exception $e) {
            Log::error('CNPayTrait - Erro ao criar pagamento CNPay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
                'external_id' => $externalId,
                'auth_api' => auth('api')->check(),
                'auth_web' => auth()->check(),
                'user_id_api' => auth('api')->id(),
                'user_id_web' => auth()->id()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar status do pagamento
     */
    public static function checkCNPayPaymentStatus($paymentId)
    {
        try {
            // Verificar se o banco está disponível antes de inicializar
        try {
            self::initCNPay();
            } catch (\Exception $e) {
                Log::error('CNPay - Erro ao inicializar (banco indisponível): ' . $e->getMessage());
                
                // Retornar erro específico para banco indisponível
                return [
                    'success' => false,
                    'error' => 'Sistema temporariamente indisponível - banco de dados offline',
                    'payment_id' => $paymentId,
                    'database_error' => true
                ];
            }

            if (!self::$uriCNPay || !self::$publicKeyCNPay || !self::$secretKeyCNPay) {
                throw new \Exception('Configurações do CNPay não encontradas');
            }

            // Log detalhado da requisição de status
            Log::info('CNPay - Verificando status do pagamento', [
                'uri_completa' => self::$uriCNPay . '/gateway/pix/receive/' . $paymentId,
                'method' => 'GET',
                'payment_id' => $paymentId,
                'using_proxy' => !!(self::$proxyConfig && (self::$proxyConfig['http_proxy'] || self::$proxyConfig['https_proxy'])),
                'headers' => [
                    'X-Public-Key' => self::$publicKeyCNPay ? 'configurado' : 'não configurado',
                    'X-Secret-Key' => self::$secretKeyCNPay ? 'configurado' : 'não configurado',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Laravel-HTTP-Client',
                ],
                'configuracoes' => [
                    'uri_base' => self::$uriCNPay,
                    'public_key_length' => self::$publicKeyCNPay ? strlen(self::$publicKeyCNPay) : 0,
                    'secret_key_length' => self::$secretKeyCNPay ? strlen(self::$secretKeyCNPay) : 0,
                ]
            ]);

            $client = self::getHttpClient(true); // 🎯 SEMPRE COM PROXY
            $response = $client->withHeaders([
                'X-Public-Key' => self::$publicKeyCNPay,
                'X-Secret-Key' => self::$secretKeyCNPay,
                'Content-Type' => 'application/json',
                'User-Agent' => 'Laravel-HTTP-Client',
            ])->get(self::$uriCNPay . '/gateway/pix/receive/' . $paymentId);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log da resposta bem-sucedida
                Log::info('CNPay - Status verificado com sucesso', [
                    'payment_id' => $paymentId,
                    'response_status' => $response->status(),
                    'response_headers' => $response->headers(),
                    'response_body' => $data,
                    'response_body_json' => json_encode($data, JSON_PRETTY_PRINT),
                    'mapped_status' => self::mapCNPayStatus($data['status'] ?? 'unknown'),
                    'using_proxy' => !!(self::$proxyConfig && (self::$proxyConfig['http_proxy'] || self::$proxyConfig['https_proxy']))
                ]);
                
                // Mapear status do CNPay para status do sistema
                $status = self::mapCNPayStatus($data['status'] ?? 'unknown');
                
                return [
                    'success' => true,
                    'status' => $status,
                    'status_text' => self::getStatusText($status),
                    'original_status' => $data['status'] ?? 'unknown',
                    'payment_id' => $paymentId,
                    'amount' => $data['amount'] ?? null,
                    'currency' => $data['currency'] ?? 'BRL',
                    'created_at' => $data['created_at'] ?? null,
                    'updated_at' => $data['updated_at'] ?? null,
                    'raw_data' => $data
                ];
            }

            // Log detalhado da resposta de erro
            $errorBody = $response->body();
            $errorJson = null;
            
            try {
                $errorJson = json_decode($errorBody, true);
            } catch (\Exception $e) {
                $errorJson = ['raw_body' => $errorBody];
            }
            
            Log::error('CNPay - Erro ao verificar status', [
                'payment_id' => $paymentId,
                'response_status' => $response->status(),
                'response_headers' => $response->headers(),
                'response_headers_json' => json_encode($response->headers(), JSON_PRETTY_PRINT),
                'response_body' => $errorBody,
                'response_body_json' => $errorJson,
                'response_body_json_pretty' => json_encode($errorJson, JSON_PRETTY_PRINT),
                'uri' => self::$uriCNPay,
                'endpoint' => '/gateway/pix/receive/' . $paymentId,
                'using_proxy' => !!(self::$proxyConfig && (self::$proxyConfig['http_proxy'] || self::$proxyConfig['https_proxy'])),
                'request_info' => [
                    'method' => 'GET',
                    'uri_completa' => self::$uriCNPay . '/gateway/pix/receive/' . $paymentId,
                    'headers_enviados' => [
                        'X-Public-Key' => self::$publicKeyCNPay ? 'configurado' : 'não configurado',
                        'X-Secret-Key' => self::$secretKeyCNPay ? 'configurado' : 'não configurado',
                        'Content-Type' => 'application/json',
                        'User-Agent' => 'Laravel-HTTP-Client',
                    ]
                ]
            ]);

            // Se a resposta não for bem-sucedida, tentar extrair informações de erro
            $errorData = $errorJson;
            $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'Erro desconhecido';
            
            throw new \Exception('Erro na API do CNPay: ' . $errorMessage . ' (Status: ' . $response->status() . ')');

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status CNPay: ' . $e->getMessage(), [
                'payment_id' => $paymentId,
                'uri' => self::$uriCNPay ?? 'não configurado',
                'using_proxy' => !!(self::$proxyConfig && (self::$proxyConfig['http_proxy'] || self::$proxyConfig['https_proxy']))
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'payment_id' => $paymentId
            ];
        }
    }

    /**
     * Mapear status do CNPay para status do sistema
     */
    private static function mapCNPayStatus($cnpayStatus)
    {
        $statusMap = [
            'pending' => 0,      // pending
            'processing' => 4,   // processing
            'paid' => 1,         // confirmed
            'completed' => 1,    // confirmed
            'success' => 1,      // confirmed
            'cancelled' => 2,    // cancelled
            'expired' => 2,      // cancelled
            'failed' => 3,       // failed
            'rejected' => 2      // cancelled
        ];

        return $statusMap[$cnpayStatus] ?? 0; // default to pending
    }

    /**
     * Converter código de status para texto
     */
    private static function getStatusText(int $statusCode): string
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

    /**
     * Processar webhook do CNPay
     */
    public static function processCNPayWebhook($payload, $headers)
    {
        try {
            self::initCNPay();

            // Log do webhook recebido
            Log::info('CNPay - Webhook recebido', [
                'headers' => $headers,
                'payload' => $payload,
                'payload_json' => json_encode($payload, JSON_PRETTY_PRINT),
                'signature_header' => $headers['X-Signature'] ?? 'não encontrado'
            ]);

            // Verificar assinatura do webhook (se aplicável)
            if (isset($headers['X-Signature'])) {
                $signature = $headers['X-Signature'];
                $expectedSignature = hash_hmac('sha256', json_encode($payload), self::$secretKeyCNPay);
                
                Log::info('CNPay - Verificação de assinatura', [
                    'received_signature' => $signature,
                    'expected_signature' => $expectedSignature,
                    'signature_valid' => hash_equals($signature, $expectedSignature)
                ]);
                
                if (!hash_equals($signature, $expectedSignature)) {
                    throw new \Exception('Assinatura do webhook inválida');
                }
            }

            $paymentId = $payload['id'] ?? null;
            $status = $payload['status'] ?? null;
            $amount = $payload['amount'] ?? null;

            if (!$paymentId || !$status) {
                throw new \Exception('Dados do webhook incompletos');
            }

            // Buscar transação
            $transaction = Transaction::where('payment_id', $paymentId)->first();
            
            if (!$transaction) {
                throw new \Exception('Transação não encontrada: ' . $paymentId);
            }

            // Atualizar status da transação
            switch ($status) {
                case 'paid':
                case 'completed':
                    $transaction->update(['status' => 1]); // 1 = confirmed
                    break;
                    
                case 'cancelled':
                case 'expired':
                    $transaction->update(['status' => 2]); // 2 = cancelled
                    break;
                    
                case 'pending':
                    $transaction->update(['status' => 0]); // 0 = pending
                    break;
                    
                default:
                    Log::warning('Status desconhecido do CNPay: ' . $status);
            }

            // Atualizar resposta do gateway
            $transaction->update([
                'gateway_response' => json_encode($payload)
            ]);

            Log::info('Webhook CNPay processado com sucesso: ' . $paymentId);

            return [
                'success' => true,
                'transaction_id' => $transaction->id,
                'status' => $status
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook CNPay: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obter estratégias de bypass para contornar Cloudflare
     */
    private static function getBypassStrategies()
    {
        return [
            [
                'name' => 'Headers CNPay Padrão',
                'headers' => [
                    'X-Public-Key' => self::$publicKeyCNPay,
                    'X-Secret-Key' => self::$secretKeyCNPay,
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'CNPay-Integration/1.0 (Laravel)',
                    'Accept' => 'application/json',
                ]
            ],
            [
                'name' => 'Autenticação Bearer Token',
                'headers' => [
                    'Authorization' => 'Bearer ' . self::$secretKeyCNPay,
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'CNPay-Integration/1.0 (Laravel)',
                    'Accept' => 'application/json',
                ]
            ],
            [
                'name' => 'Headers Alternativos',
                'headers' => [
                    'X-API-Key' => self::$publicKeyCNPay,
                    'X-API-Secret' => self::$secretKeyCNPay,
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'CNPay-Integration/1.0 (Laravel)',
                    'Accept' => 'application/json',
                ]
            ],
            [
                'name' => 'Headers Simples',
                'headers' => [
                    'X-Public-Key' => self::$publicKeyCNPay,
                    'X-Secret-Key' => self::$secretKeyCNPay,
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'CNPay-Integration/1.0 (Laravel)',
                    'Accept' => 'application/json',
                ]
            ]
        ];
    }

    /**
     * Processar resposta bem-sucedida
     */
    private static function processSuccessfulResponse($response, $amount, $currency, $externalId)
    {
        try {
            Log::info('CNPayTrait - processSuccessfulResponse iniciado', [
                'amount' => $amount,
                'currency' => $currency,
                'external_id' => $externalId,
                'response_status' => $response->status()
            ]);

        $data = $response->json();
            
            Log::info('CNPayTrait - Dados da resposta JSON', [
                'data' => $data,
                'data_keys' => array_keys($data),
                'has_transaction_id' => isset($data['transactionId']),
                'has_id' => isset($data['id']),
                'has_order' => isset($data['order']),
                'has_pix' => isset($data['pix'])
            ]);
        
        // Log da resposta bem-sucedida
        Log::info('CNPay - Pagamento criado com sucesso', [
            'amount' => $amount,
            'currency' => $currency,
            'response_status' => $response->status(),
            'response_headers' => $response->headers(),
            'response_body' => $data,
            'response_body_json' => json_encode($data, JSON_PRETTY_PRINT)
        ]);
        
        // Criar transação no sistema com estrutura correta da API
        $userId = auth('api')->user()->id ?? auth()->id();
        
        // Log detalhado da autenticação
        Log::info('CNPay - Processando resposta bem-sucedida', [
            'user_id_from_api_auth' => auth('api')->user()->id ?? 'não autenticado',
            'user_id_from_web_auth' => auth()->id(),
            'user_authenticated_api' => auth('api')->check(),
            'user_authenticated_web' => auth()->check(),
            'fallback_user_id' => $userId ?? 1,
            'amount' => $amount,
            'currency' => $currency
        ]);
        
            Log::info('CNPayTrait - Criando transação no banco', [
                'payment_id' => $data['transactionId'] ?? $data['id'] ?? $externalId,
                'reference' => $data['order']['id'] ?? $data['reference'] ?? uniqid('cnpay_'),
                'user_id' => $userId ?? 1,
                'amount' => $amount,
                'currency' => $currency
            ]);
            
            // Usar o método padrão dos outros gateways
            $token = self::generateCNPayTransaction(
                $data['transactionId'] ?? $data['id'] ?? $externalId,
                $amount,
                false // accept_bonus
            );
            
            // Criar depósito usando o método padrão
            self::generateCNPayDeposit(
                $data['transactionId'] ?? $data['id'] ?? $externalId,
                $amount
            );
            
            // Buscar a transação criada
            $transaction = Transaction::where('payment_id', $data['transactionId'] ?? $data['id'] ?? $externalId)
                ->where('gateway_name', 'cnpay')
                ->first();

            Log::info('CNPayTrait - Transação criada com sucesso', [
            'transaction_id' => $transaction->id,
                'payment_id' => $transaction->payment_id,
                'reference' => $transaction->reference,
                'user_id' => $transaction->user_id,
                'status' => $transaction->status
            ]);

            // Retornar apenas os 3 campos essenciais como outros gateways
            $result = [
                'success' => true,
                'qrcode' => $data['pix']['code'] ?? $data['qr_code'] ?? $data['order']['url'] ?? null,
                'token' => $token
            ];

            Log::info('CNPayTrait - Resultado preparado para retorno', [
                'result' => $result,
                'success' => $result['success'],
                'has_qrcode' => !!($result['qrcode']),
                'has_token' => !!($result['token'])
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('CNPayTrait - Erro ao processar resposta bem-sucedida', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'amount' => $amount,
                'currency' => $currency,
                'external_id' => $externalId,
                'response_status' => $response->status()
            ]);
            
            throw $e;
        }
    }

    /**
     * Obter configurações do CNPay
     */
    public static function getCNPayConfig()
    {
        self::initCNPay();
        
        return [
            'uri' => self::$uriCNPay,
            'public_key' => self::$publicKeyCNPay,
            'secret_key' => self::$secretKeyCNPay,
            'webhook_url' => self::$webhookUrlCNPay,
            'is_configured' => !!(self::$uriCNPay && self::$publicKeyCNPay && self::$secretKeyCNPay)
        ];
    }

    /**
     * Obter configurações de proxy
     */
    public static function getProxyConfig()
    {
        return [
            'http_proxy' => self::$proxyConfig['http_proxy'] ?? null,
            'https_proxy' => self::$proxyConfig['https_proxy'] ?? null,
            'proxy_user' => self::$proxyConfig['proxy_user'] ?? null,
            'proxy_pass' => self::$proxyConfig['proxy_pass'] ?? null,
            'proxy_port' => self::$proxyConfig['proxy_port'] ?? null,
            'proxy_type' => self::$proxyConfig['proxy_type'] ?? 'http'
        ];
    }

    /**
     * Gerar transação no banco de dados
     * Seguindo o padrão dos outros gateways
     */
    private static function generateCNPayTransaction($idTransaction, $amount, $accept_bonus = false)
    {
        try {
            $setting = \Helper::getSetting();
            $token = bin2hex(random_bytes(16)); // Token seguro com 32 caracteres hex

            Log::info('CNPay - Gerando transação no banco', [
                'payment_id' => $idTransaction,
                'user_id' => auth('api')->user()->id,
                'amount' => $amount,
                'currency' => $setting->currency_code ?? 'BRL',
                'accept_bonus' => $accept_bonus,
                'token' => $token
            ]);

            $transaction = Transaction::create([
                'payment_id' => $idTransaction,
                'user_id' => auth('api')->user()->id,
                'payment_method' => 'pix',
                'price' => $amount,
                'currency' => $setting->currency_code ?? 'BRL',
                'status' => 0,
                'token' => $token,
                'accept_bonus' => $accept_bonus,
                'gateway_name' => 'cnpay',
                'gateway_response' => json_encode(['created_via' => 'generateCNPayTransaction']),
            ]);

            Log::info('CNPay - Transação criada com sucesso', [
                'transaction_id' => $transaction->id,
                'payment_id' => $transaction->payment_id,
                'token' => $token
            ]);

            return $token;

        } catch (\Exception $e) {
            Log::error('CNPay - Erro ao gerar transação: ' . $e->getMessage(), [
                'error_type' => get_class($e),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Erro ao criar transação: ' . $e->getMessage());
        }
    }

    /**
     * Gerar depósito no banco de dados
     * Seguindo o padrão dos outros gateways
     */
    private static function generateCNPayDeposit($idTransaction, $amount)
    {
        try {
            $userId = auth('api')->user()->id;
            $wallet = \App\Models\Wallet::where('user_id', $userId)->first();

            if (!$wallet) {
                Log::error('CNPay - Carteira não encontrada para o usuário', [
                    'user_id' => $userId
                ]);
                throw new \Exception('Carteira do usuário não encontrada');
            }

            Log::info('CNPay - Gerando depósito no banco', [
                'payment_id' => $idTransaction,
                'user_id' => $userId,
                'amount' => $amount,
                'currency' => $wallet->currency ?? 'BRL',
                'symbol' => $wallet->symbol ?? 'R$'
            ]);

            $deposit = \App\Models\Deposit::create([
                'payment_id' => $idTransaction,
                'user_id' => $userId,
                'amount' => $amount,
                'type' => 'pix',
                'currency' => $wallet->currency ?? 'BRL',
                'symbol' => $wallet->symbol ?? 'R$',
                'status' => 0
            ]);

            Log::info('CNPay - Depósito criado com sucesso', [
                'deposit_id' => $deposit->id,
                'payment_id' => $deposit->payment_id,
                'amount' => $deposit->amount
            ]);

            return $deposit;

        } catch (\Exception $e) {
            Log::error('CNPay - Erro ao gerar depósito: ' . $e->getMessage(), [
                'error_type' => get_class($e),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Erro ao criar depósito: ' . $e->getMessage());
        }
    }

    /**
     * Finalizar pagamento (quando webhook confirma pagamento)
     * Seguindo o padrão dos outros gateways
     */
    public static function finalizeCNPayPayment($idTransaction): bool
    {
        try {
            Log::info('CNPay - Finalizando pagamento', [
                'idTransaction' => $idTransaction
            ]);

            $transaction = Transaction::where('payment_id', $idTransaction)->where('status', 0)->first();
            
            if (!$transaction) {
                Log::warning('CNPay - Transação não encontrada ou já finalizada', [
                    'idTransaction' => $idTransaction
                ]);
                return false;
            }

            $setting = \Helper::getSetting();
            $user = \App\Models\User::find($transaction->user_id);
            $wallet = \App\Models\Wallet::where('user_id', $transaction->user_id)->first();

            if (!$wallet) {
                Log::error('CNPay - Carteira não encontrada para finalizar pagamento', [
                    'user_id' => $transaction->user_id,
                    'transaction_id' => $transaction->id
                ]);
                return false;
            }

            // Verifica se é o primeiro depósito
            $checkTransactions = Transaction::where('user_id', $transaction->user_id)
                ->where('status', 1)
                ->count();

            if ($checkTransactions == 0 || empty($checkTransactions)) {
                if ($transaction->accept_bonus) {
                    // Pagar o bônus de primeiro depósito
                    $bonus = \Helper::porcentagem_xn($setting->initial_bonus, $transaction->price);
                    $wallet->increment('balance_bonus', $bonus);

                    if (!$setting->disable_rollover) {
                        $wallet->update(['balance_bonus_rollover' => $bonus * $setting->rollover]);
                    }

                    Log::info('CNPay - Bônus de primeiro depósito aplicado', [
                        'user_id' => $transaction->user_id,
                        'bonus_amount' => $bonus,
                        'transaction_amount' => $transaction->price
                    ]);
                }
            }

            // Rollover do depósito
            if (!$setting->disable_rollover) {
                $wallet->increment('balance_deposit_rollover', ($transaction->price * intval($setting->rollover_deposit)));
            }

            // Acumular bônus VIP
            \Helper::payBonusVip($wallet, $transaction->price);

            // Dinheiro direto para carteira de saque ou jogos
            if ($setting->disable_rollover) {
                $wallet->increment('balance_withdrawal', $transaction->price);
            } else {
                $wallet->increment('balance', $transaction->price);
            }

            if ($transaction->update(['status' => 1])) {
                $deposit = \App\Models\Deposit::where('payment_id', $idTransaction)->where('status', 0)->first();

                if ($deposit) {
                    // CPA para primeiro depósito
                    $affHistoryCPA = \App\Models\AffiliateHistory::where('user_id', $user->id)
                        ->where('commission_type', 'cpa')
                        ->first();

                    if ($affHistoryCPA) {
                        $affHistoryCPA->increment('deposited_amount', $transaction->price);

                        $sponsorCpa = \App\Models\User::find($user->inviter);
                        if ($sponsorCpa && $affHistoryCPA->status == 'pendente') {
                            if ($affHistoryCPA->deposited_amount >= $sponsorCpa->affiliate_baseline || $deposit->amount >= $sponsorCpa->affiliate_baseline) {
                                $walletCpa = \App\Models\Wallet::where('user_id', $affHistoryCPA->inviter)->first();
                                if ($walletCpa) {
                                    $walletCpa->increment('refer_rewards', $sponsorCpa->affiliate_cpa);
                                    $affHistoryCPA->update(['status' => 1, 'commission_paid' => $sponsorCpa->affiliate_cpa]);
                                }
                            }
                        }
                    }

                    // Comissão percentual para níveis 1 e 2
                    if ($transaction->price >= $setting->cpa_percentage_baseline) {
                        $inviterN1 = \App\Models\User::find($user->inviter);

                        if ($inviterN1) {
                            $commissionN1 = $transaction->price * ($setting->cpa_percentage_n1 / 100);
                            $walletN1 = \App\Models\Wallet::where('user_id', $inviterN1->id)->first();
                            if ($walletN1) {
                                $walletN1->increment('refer_rewards', $commissionN1);
                            }
                        }

                        $inviterN2 = \App\Models\User::find($inviterN1->inviter ?? null);
                        if ($inviterN2) {
                            $commissionN2 = $transaction->price * ($setting->cpa_percentage_n2 / 100);
                            $walletN2 = \App\Models\Wallet::where('user_id', $inviterN2->id)->first();
                            if ($walletN2) {
                                $walletN2->increment('refer_rewards', $commissionN2);
                            }
                        }
                    }

                    // Atualizar status do depósito
                    $deposit->update(['status' => 1]);

                    // Notificação de novo depósito
                    try {
                        $user->notify(new \App\Notifications\NewDepositNotification($deposit));
                    } catch (\Exception $e) {
                        Log::warning('CNPay - Erro ao enviar notificação: ' . $e->getMessage());
                    }

                    Log::info('CNPay - Pagamento finalizado com sucesso', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->user_id,
                        'amount' => $transaction->price,
                        'wallet_balance' => $wallet->balance,
                        'wallet_bonus' => $wallet->balance_bonus
                    ]);

                    return true;
                }
            }

            Log::error('CNPay - Erro ao finalizar pagamento', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('CNPay - Erro ao finalizar pagamento: ' . $e->getMessage(), [
                'error_type' => get_class($e),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'idTransaction' => $idTransaction
            ]);
            return false;
        }
    }

    /**
     * Consultar status da transação
     * Seguindo o padrão dos outros gateways
     */
    public static function consultCNPayStatusTransaction($request)
    {
        try {
            Log::info('CNPay - Consultando status da transação', [
                'request_data' => $request->all()
            ]);

            $transaction = Transaction::where('token', $request->token)->first();
            
            if (!$transaction) {
                Log::warning('CNPay - Transação não encontrada pelo token', [
                    'token' => $request->token
                ]);
                return response()->json(['status' => 'Transação não encontrada'], 400);
            }

            Log::info('CNPay - Transação encontrada', [
                'transaction_id' => $transaction->id,
                'payment_id' => $transaction->payment_id,
                'status' => $transaction->status
            ]);

            // Verificar status no CNPay
            $status = self::checkCNPayPaymentStatus($transaction->payment_id);
            
            if ($status && isset($status['status'])) {
                if ($status['status'] === 'PAID' || $status['status'] === 'PAID_OUT' || $status['status'] === 'PAYMENT_ACCEPT') {
                    if (self::finalizeCNPayPayment($transaction->payment_id)) {
                        return response()->json(['status' => 'PAID']);
                    }
                    return response()->json(['status' => $status['status']], 400);
                }
                return response()->json(['status' => $status['status']], 400);
            }

            return response()->json(['status' => 'Erro na consulta do status'], 500);

        } catch (\Exception $e) {
            Log::error('CNPay - Erro ao consultar status: ' . $e->getMessage(), [
                'error_type' => get_class($e),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Gerar QR Code PIX via CNPay - Padrão dos outros gateways
     */
    public function requestQrcodeCNPay($request)
    {
        try {
            $setting = \App\Helpers\Core::getSetting();
            $rules = [
                'amount' => ['required', 'numeric', 'min:' . $setting->min_deposit, 'max:' . $setting->max_deposit],
                'cpf'    => ['required', 'string', 'max:255'],
            ];

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            Log::info('CNPay - Iniciando geração de QR Code PIX', [
                'amount' => $request->amount,
                'cpf' => $request->cpf,
                'user_id' => auth('api')->id(),
                'user_name' => auth('api')->user()->name
            ]);

            self::initCNPay();

            if (!self::$uriCNPay || !self::$publicKeyCNPay || !self::$secretKeyCNPay) {
                Log::error('CNPay - Configurações não encontradas para geração de QR Code');
                return response()->json(['error' => 'Configurações do CNPay não encontradas'], 500);
            }

            $idUnico = uniqid('cnpay_');
            
            // Payload moldado exatamente como solicitado
            $payload = [
                'identifier' => $idUnico,
                'amount' => (float) $request->input("amount"),
                'client' => [
                    'name' => 'João da Silva',
                    'email' => auth('api')->user()->email,
                    'phone' => '(11) 99999-9999', // 🎯 TELEFONE FIXO
                    'document' => '64289113028' // 🎯 CPF FIXO
                ],
                'products' => [
                    [
                        'id' => 'adicao_saldo_' . $idUnico,
                        'name' => 'Adição de Saldo - Carteira Digital',
                        'quantity' => 1,
                        'price' => (float) $request->input("amount")
                    ]
                ],
                'dueDate' => now()->addHours(24)->format('Y-m-d'),
                'metadata' => [
                    'transaction_type' => 'deposit',
                    'gateway' => 'cnpay',
                    'payment_method' => 'pix',
                    'user_id' => auth('api')->id(),
                    'description' => 'Depósito via PIX CNPay'
                ],
                'callbackUrl' => url('/cnpay/callback', [], true)
            ];

            Log::info('CNPay - Payload para geração de QR Code', [
                'payload' => $payload,
                'endpoint' => self::$uriCNPay . '/gateway/pix/receive'
            ]);

            $client = self::getHttpClient(true); // 🎯 SEMPRE COM PROXY
            $response = $client->withHeaders([
                'X-Public-Key' => self::$publicKeyCNPay,
                'X-Secret-Key' => self::$secretKeyCNPay,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post(self::$uriCNPay . '/gateway/pix/receive', $payload);

            Log::info('CNPay - Resposta da API', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_length' => strlen($response->body())
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('CNPay - QR Code gerado com sucesso', [
                    'response_data' => $responseData,
                    'transaction_id' => $responseData['transaction_id'] ?? $idUnico
                ]);

                // Gerar transação seguindo padrão dos outros gateways
                $token = self::generateTransactionCNPay(
                    $responseData['transaction_id'] ?? $idUnico,
                    $request->input("amount"),
                    $idUnico,
                    $request->input('accept_bonus') ?? false
                );

                // Gerar depósito seguindo padrão dos outros gateways
                self::generateDepositCNPay(
                    $responseData['transaction_id'] ?? $idUnico,
                    $request->input("amount")
                );

                return response()->json([
                    'status' => true,
                    'idTransaction' => $responseData['transaction_id'] ?? $idUnico,
                    'qrcode' => $responseData['qrcode'] ?? $responseData['pix_code'] ?? 'QR Code não disponível',
                    'token' => $token
                ]);
            }

            Log::error('CNPay - Falha na geração de QR Code', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return response()->json(['error' => 'Falha na geração do QR Code PIX'], 500);

        } catch (\Exception $e) {
            Log::error('CNPay - Erro na geração de QR Code', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Erro interno na geração do QR Code'], 500);
        }
    }

    /**
     * Gerar transação CNPay - Padrão dos outros gateways
     */
    private static function generateTransactionCNPay($idTransaction, $amount, $id, $accept_bonus = false)
    {
        try {
            $setting = \App\Helpers\Core::getSetting();
            $token = bin2hex(random_bytes(16)); // token seguro

            \App\Models\Transaction::create([
                'payment_id' => $idTransaction,
                'user_id' => auth('api')->user()->id,
                'payment_method' => 'pix',
                'price' => $amount,
                'currency' => $setting->currency_code ?? 'BRL',
                'status' => 0,
                'idUnico' => $id,
                'accept_bonus' => $accept_bonus,
                'token' => $token,
            ]);

            Log::info('CNPay - Transação criada com sucesso', [
                'transaction_id' => $idTransaction,
                'amount' => $amount,
                'user_id' => auth('api')->user()->id
            ]);

            return $token;
        } catch (\Exception $e) {
            Log::error('CNPay - Erro ao criar transação', [
                'error' => $e->getMessage(),
                'transaction_id' => $idTransaction
            ]);
            throw $e;
        }
    }

    /**
     * Gerar depósito CNPay - Padrão dos outros gateways
     */
    private static function generateDepositCNPay($idTransaction, $amount)
    {
        try {
            $userId = auth('api')->user()->id;
            $wallet = \App\Models\Wallet::where('user_id', $userId)->first();

            if (!$wallet) {
                Log::error('CNPay - Carteira não encontrada para usuário', ['user_id' => $userId]);
                throw new \Exception('Carteira não encontrada');
            }

            \App\Models\Deposit::create([
                'payment_id' => $idTransaction,
                'user_id'   => $userId,
                'amount'    => $amount,
                'type'      => 'pix',
                'currency'  => $wallet->currency ?? 'BRL',
                'symbol'    => $wallet->symbol ?? 'R$',
                'status'    => 0
            ]);

            Log::info('CNPay - Depósito criado com sucesso', [
                'deposit_id' => $idTransaction,
                'amount' => $amount,
                'user_id' => $userId
            ]);

        } catch (\Exception $e) {
            Log::error('CNPay - Erro ao criar depósito', [
                'error' => $e->getMessage(),
                'transaction_id' => $idTransaction
            ]);
            throw $e;
        }
    }

    /**
     * Finalizar pagamento CNPay - Padrão dos outros gateways
     */
    public static function finalizePaymentCNPay($idTransaction): bool
    {
        try {
            $transaction = \App\Models\Transaction::where('payment_id', $idTransaction)->where('status', 0)->first();
            $setting = \App\Helpers\Core::getSetting();

            if (empty($transaction)) {
                Log::warning('CNPay - Transação não encontrada para finalização', ['transaction_id' => $idTransaction]);
                return false;
            }

            $user = \App\Models\User::find($transaction->user_id);
            $wallet = \App\Models\Wallet::where('user_id', $transaction->user_id)->first();

            if (empty($wallet)) {
                Log::error('CNPay - Carteira não encontrada para finalização', ['user_id' => $transaction->user_id]);
                return false;
            }

            // Verifica se é o primeiro depósito
            $checkTransactions = \App\Models\Transaction::where('user_id', $transaction->user_id)
                ->where('status', 1)
                ->count();

            if ($checkTransactions == 0 || empty($checkTransactions)) {
                if ($transaction->accept_bonus) {
                    // Pagar o bônus de primeiro depósito
                    $bonus = \App\Helpers\Core::porcentagem_xn($setting->initial_bonus ?? 0, $transaction->price);
                    $wallet->increment('balance_bonus', $bonus);

                    if (!($setting->disable_rollover ?? false)) {
                        $wallet->update(['balance_bonus_rollover' => $bonus * ($setting->rollover ?? 1)]);
                    }
                }
            }

            // Rollover do depósito
            if (!($setting->disable_rollover ?? false)) {
                $wallet->increment('balance_deposit_rollover', ($transaction->price * intval($setting->rollover_deposit ?? 1)));
            }

            // Acumular bônus VIP
            \App\Helpers\Core::payBonusVip($wallet, $transaction->price);

            // Dinheiro direto para carteira de saque ou jogos
            if ($setting->disable_rollover ?? false) {
                $wallet->increment('balance_withdrawal', $transaction->price);
            } else {
                $wallet->increment('balance', $transaction->price);
            }

            if ($transaction->update(['status' => 1])) {
                $deposit = \App\Models\Deposit::where('payment_id', $idTransaction)->where('status', 0)->first();

                if (!empty($deposit)) {
                    // Processar comissões de afiliados (se implementado)
                    // ... código de comissões similar aos outros gateways ...

                    if ($deposit->update(['status' => 1])) {
                        // Notificar administradores
                        $admins = \App\Models\User::where('role_id', 0)->get();
                        foreach ($admins as $admin) {
                            // $admin->notify(new \App\Notifications\NewDepositNotification($user->name, $transaction->price));
                        }

                        Log::info('CNPay - Pagamento finalizado com sucesso', [
                            'transaction_id' => $idTransaction,
                            'user_id' => $transaction->user_id,
                            'amount' => $transaction->price
                        ]);

                        return true;
                    }
                    return false;
                }
                return false;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('CNPay - Erro ao finalizar pagamento', [
                'error' => $e->getMessage(),
                'transaction_id' => $idTransaction
            ]);
            return false;
        }
    }
}
