<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\BsPayPayment;
use App\Models\AffiliateWithdraw;
use App\Traits\Affiliates\AffiliateHistoryTrait;
use App\Traits\Gateways\BsPayTrait;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use App\Helpers\Core as Helper;

class BsPayController extends Controller
{
    use BsPayTrait, AffiliateHistoryTrait;


    /**
     * @dev victormsalatiel
     * @param Request $request
     * @return null
     */
    public function getQRCodePix(Request $request)
    {
        return self::requestQrcode($request);
    }
    public function callbackMethodPayment(Request $request)
    {
        $data = $request->all();
        \DB::table('debug')->insert(['text' => json_encode($request->all())]);

        return response()->json([], 200);
    }
  
    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function callbackMethod(Request $request)
    {
        \Log::info('BsPay - Processando callback...');
        
        // Tentar diferentes formatos de dados
        $rawContent = $request->getContent();
        $data = json_decode($rawContent, true);
        
        \DB::table('debug')->insert(['text' => $rawContent]);
        \Log::info('Webhook recebido: ', $data);
        
        // Verificar se não é uma resposta de sucesso ou dados inválidos (evitar loop)
        if (isset($data['success']) && $data['success'] === true) {
            \Log::info('BsPay - Resposta de sucesso recebida, ignorando para evitar loop', [
                'data' => $data
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Resposta de sucesso ignorada para evitar loop'
            ], 200);
        }
        
        // Verificar se tem dados mínimos necessários para um webhook
        if (empty($data) || !is_array($data)) {
            \Log::warning('BsPay - Dados vazios ou inválidos recebidos', [
                'data' => $data,
                'data_type' => gettype($data)
            ]);
            return response()->json([
                'error' => 'Dados inválidos',
                'message' => 'Payload vazio ou formato inválido'
            ], 400);
        }
        
        // Verificar se não é uma resposta de erro ou mensagem de sistema
        if (isset($data['error']) || (isset($data['message']) && strpos($data['message'], 'Pagamento processado') !== false)) {
            \Log::info('BsPay - Resposta de sistema recebida, ignorando', [
                'data' => $data
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Resposta de sistema ignorada'
            ], 200);
        }
    
        \Log::info('BsPay - Dados recebidos no callback', [
            'data' => $data,
            'request_body' => $request->requestBody,
            'request_all' => $request->all(),
            'content_type' => $request->header('Content-Type')
        ]);
    
        // FORMATO 1: CNPay TRANSACTION_CREATED (novo evento)
        if (isset($data['event']) && $data['event'] === 'TRANSACTION_CREATED' && isset($data['transaction'])) {
            \Log::info('BsPay - Formato CNPay detectado (TRANSACTION_CREATED)');
            
            $transactionId = $data['transaction']['id'] ?? null;
            $status = $data['transaction']['status'] ?? null;
            $amount = $data['transaction']['amount'] ?? 0;
            
            if ($transactionId) {
                \Log::info('BsPay - Transação criada via CNPay', [
                    'transaction_id' => $transactionId,
                    'status' => $status,
                    'amount' => $amount
                ]);
                
                // Para TRANSACTION_CREATED, apenas logamos e retornamos sucesso
                // A finalização acontecerá quando recebermos TRANSACTION_PAID
                return response()->json([
                    'success' => true,
                    'message' => 'Transação criada registrada',
                    'webhook_format' => 'CNPay_TRANSACTION_CREATED',
                    'transaction_id' => $transactionId
                ], 200);
            }
        }
        
        // FORMATO 1: CNPay (quando BsPay usa CNPay como proxy)
        if (isset($data['event']) && $data['event'] === 'TRANSACTION_PAID' && isset($data['transaction'])) {
            \Log::info('BsPay - Formato CNPay detectado (TRANSACTION_PAID)');
            
            $transactionId = $data['transaction']['id'] ?? null;
            $status = $data['transaction']['status'] ?? null;
            $amount = $data['transaction']['amount'] ?? 0;
            $identifier = $data['transaction']['identifier'] ?? null;
            
            \Log::info('BsPay - Dados extraídos do webhook CNPay', [
                'transaction_id' => $transactionId,
                'status' => $status,
                'amount' => $amount,
                'identifier' => $identifier,
                'trackProps' => $data['trackProps'] ?? null
            ]);
            
            // Extrair informações adicionais do trackProps
            $trackProps = $data['trackProps'] ?? [];
            $userId = $trackProps['user_id'] ?? null;
            $description = $trackProps['description'] ?? null;
            
            \Log::info('BsPay - Informações adicionais extraídas', [
                'user_id' => $userId,
                'description' => $description,
                'gateway' => $trackProps['gateway'] ?? null
            ]);
            
            // Aceitar tanto PAID quanto COMPLETED como status válidos
            if ($transactionId && in_array($status, ['PAID', 'COMPLETED'])) {
                \Log::info('BsPay - PIX pago via CNPay', [
                    'transaction_id' => $transactionId,
                    'status' => $status,
                    'amount' => $amount,
                    'identifier' => $identifier
                ]);
                
                // LÓGICA DE RECONCILIAÇÃO INTELIGENTE
                $processedTransaction = null;
                
                // 1. PRIMEIRA TENTATIVA: Buscar por transaction_id do gateway
                if ($transactionId) {
                    $transaction = \App\Models\Transaction::where('gateway_transaction_id', $transactionId)
                                                          ->where('status', 0)
                                                          ->first();
                    
                    if ($transaction) {
                        \Log::info('BsPay - Transação encontrada por gateway_transaction_id', [
                            'transaction_id' => $transaction->id,
                            'gateway_transaction_id' => $transactionId,
                            'user_id' => $transaction->user_id,
                            'amount' => $transaction->price
                        ]);
                        
                        // Atualizar com o identifier se não tiver
                        if (!$transaction->gateway_identifier && $identifier) {
                            $transaction->update(['gateway_identifier' => $identifier]);
                        }
                        
                        $processedTransaction = $transaction;
                    }
                }
                
                // 2. SEGUNDA TENTATIVA: Buscar por identifier
                if (!$processedTransaction && $identifier) {
                    $transaction = \App\Models\Transaction::where('gateway_identifier', $identifier)
                                                          ->where('status', 0)
                                                          ->first();
                    
                    if ($transaction) {
                        \Log::info('BsPay - Transação encontrada por identifier', [
                            'transaction_id' => $transaction->id,
                            'gateway_identifier' => $identifier,
                            'user_id' => $transaction->user_id,
                            'amount' => $transaction->price
                        ]);
                        
                        // Atualizar com o transaction_id se não tiver
                        if (!$transaction->gateway_transaction_id && $transactionId) {
                            $transaction->update(['gateway_transaction_id' => $transactionId]);
                        }
                        
                        $processedTransaction = $transaction;
                    }
                }
                
                // 3. TERCEIRA TENTATIVA: Buscar por payment_id
                if (!$processedTransaction) {
                    $transaction = \App\Models\Transaction::where('payment_id', $transactionId)
                                                          ->where('status', 0)
                                                          ->first();
                    
                    if ($transaction) {
                        \Log::info('BsPay - Transação encontrada por payment_id', [
                            'transaction_id' => $transaction->id,
                            'payment_id' => $transactionId,
                            'user_id' => $transaction->user_id,
                            'amount' => $transaction->price
                        ]);
                        
                        // Atualizar com os identificadores do gateway
                        $updateData = [];
                        if ($identifier) $updateData['gateway_identifier'] = $identifier;
                        if ($transactionId) $updateData['gateway_transaction_id'] = $transactionId;
                        
                        if (!empty($updateData)) {
                            $transaction->update($updateData);
                        }
                        
                        $processedTransaction = $transaction;
                    }
                }
                
                // 4. QUARTA TENTATIVA: Buscar por user_id + valor + gateway (ÚLTIMO RECURSO)
                if (!$processedTransaction && $userId) {
                    \Log::info('BsPay - Tentando encontrar transação pelo user_id + valor', [
                        'user_id' => $userId,
                        'amount' => $amount
                    ]);
                    
                    // Buscar transação pendente para este usuário com valor similar
                    $pendingTransaction = \App\Models\Transaction::where('user_id', $userId)
                                                                   ->where('status', 0)
                                                                   ->where('price', $amount)
                                                                   ->where('gateway_name', 'bspay')
                                                                   ->orderBy('created_at', 'desc') // Pegar a mais recente
                                                                   ->first();
                    
                    if ($pendingTransaction) {
                        \Log::info('BsPay - Transação pendente encontrada por user_id + valor (ÚLTIMO RECURSO)', [
                            'transaction_id' => $pendingTransaction->id,
                            'payment_id' => $pendingTransaction->payment_id,
                            'user_id' => $userId,
                            'amount' => $amount,
                            'created_at' => $pendingTransaction->created_at
                        ]);
                        
                        // ATUALIZAR TODOS OS IDENTIFICADORES para evitar futuras duplicações
                        $updateData = [
                            'gateway_identifier' => $identifier,
                            'gateway_transaction_id' => $transactionId
                        ];
                        
                        $pendingTransaction->update($updateData);
                        
                        $processedTransaction = $pendingTransaction;
                    }
                }
                
                // PROCESSAR A TRANSAÇÃO ENCONTRADA
                if ($processedTransaction) {
                    \Log::info('BsPay - Processando transação encontrada', [
                        'transaction_id' => $processedTransaction->id,
                        'payment_id' => $processedTransaction->payment_id,
                        'gateway_identifier' => $processedTransaction->gateway_identifier,
                        'gateway_transaction_id' => $processedTransaction->gateway_transaction_id,
                        'user_id' => $processedTransaction->user_id,
                        'amount' => $processedTransaction->price,
                        'status' => $processedTransaction->status
                    ]);
                    
                    // FINALIZAR PAGAMENTO
                    if (self::finalizePaymentBsPay($processedTransaction->payment_id, $processedTransaction->user_id, $processedTransaction->price)) {
                        \Log::info('BsPay - Pagamento finalizado com sucesso!', [
                            'transaction_id' => $processedTransaction->id,
                            'finalization_method' => 'processed_transaction'
                        ]);
                        
                            return response()->json([
                                'success' => true,
                            'message' => 'Pagamento processado com sucesso',
                            'transaction_id' => $processedTransaction->payment_id,
                            'reconciliation_method' => 'exact_match'
                            ], 200);
                    } else {
                        \Log::error('BsPay - Falha ao finalizar pagamento da transação encontrada', [
                            'transaction_id' => $processedTransaction->id
                        ]);
                    }
                }
                
                // SE NENHUMA TRANSAÇÃO FOI ENCONTRADA
                \Log::error('BsPay - Nenhuma transação pendente encontrada para processar', [
                    'transaction_id' => $transactionId,
                    'identifier' => $identifier,
                    'user_id' => $userId,
                    'amount' => $amount,
                    'search_methods_tried' => [
                        'gateway_transaction_id' => $transactionId,
                        'gateway_identifier' => $identifier,
                        'payment_id' => $transactionId,
                        'user_id_amount_gateway' => $userId ? 'sim' : 'não'
                    ]
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma transação pendente encontrada',
                    'error' => 'Transação não encontrada para processamento'
                ], 400);
            }
        }
        
        // FORMATO 2: CNPay padrão (id, status, amount)
        elseif (isset($data['id']) && isset($data['status'])) {
            \Log::info('BsPay - Formato CNPay padrão detectado');
            
            $transactionId = $data['id'];
            $status = $data['status'];
            $amount = $data['amount'] ?? 0;
            
            // Normalizar status
            $normalizedStatus = strtolower($status);
            
            if (in_array($normalizedStatus, ['paid', 'completed', 'approved', 'ok', 'success', 'confirmed'])) {
                // APLICAR MESMA LÓGICA DE RECONCILIAÇÃO
                $processedTransaction = null;
                
                // 1. Buscar por gateway_transaction_id
                if ($transactionId) {
                    $transaction = \App\Models\Transaction::where('gateway_transaction_id', $transactionId)
                                                          ->where('status', 0)
                                                          ->first();
                    if ($transaction) $processedTransaction = $transaction;
                }
                
                // 2. Buscar por payment_id
                if (!$processedTransaction) {
                    $transaction = \App\Models\Transaction::where('payment_id', $transactionId)
                                                          ->where('status', 0)
                                                          ->first();
                    if ($transaction) $processedTransaction = $transaction;
                }
                
                // 3. Buscar por user_id + valor (último recurso)
                if (!$processedTransaction && $userId) {
                    $pendingTransaction = \App\Models\Transaction::where('user_id', $userId)
                                                                   ->where('status', 0)
                                                                   ->where('price', $amount)
                                                                   ->where('gateway_name', 'bspay')
                                                                   ->orderBy('created_at', 'desc')
                                                                   ->first();
                    if ($pendingTransaction) {
                        $pendingTransaction->update(['gateway_transaction_id' => $transactionId]);
                        $processedTransaction = $pendingTransaction;
                    }
                }
                
                // PROCESSAR
                if ($processedTransaction) {
                                    if (self::finalizePaymentBsPay($processedTransaction->payment_id, $processedTransaction->user_id, $processedTransaction->price)) {
                    \Log::info('BsPay - Pagamento finalizado com sucesso via CNPay (padrão)!', [
                        'transaction_id' => $processedTransaction->id,
                        'reconciliation_method' => 'standard_format'
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Pagamento processado com sucesso',
                        'webhook_format' => 'CNPay_Standard',
                        'transaction_id' => $processedTransaction->payment_id
                    ], 200);
                    }
                }
                
                \Log::error('BsPay - Falha ao finalizar pagamento via CNPay (padrão)', [
                    'transaction_id' => $transactionId,
                    'user_id' => $userId,
                    'amount' => $amount
                ]);
                return response()->json(['error' => 'Falha ao processar pagamento'], 500);
            } else {
                \Log::info('BsPay - Status não indica pagamento aprovado', [
                    'status' => $status,
                    'normalized_status' => $normalizedStatus
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Status não indica pagamento aprovado: ' . $status
                ], 200);
            }
        }
        
        // FORMATO 4: BsPay original (legado)
        elseif (isset($data['transactionId']) && $data['transactionType'] == 'RECEIVEPIX') {
            \Log::info('BsPay - Formato original detectado (RECEIVEPIX)');
            
            if ($data['status'] == "PAID") {
                \Log::info('BsPay - Status PAID encontrado (formato original)!');
                
                // APLICAR MESMA LÓGICA DE RECONCILIAÇÃO
                $processedTransaction = null;
                $transactionId = $data['transactionId'];
                
                // 1. Buscar por gateway_transaction_id
                if ($transactionId) {
                    $transaction = \App\Models\Transaction::where('gateway_transaction_id', $transactionId)
                                                          ->where('status', 0)
                                                          ->first();
                    if ($transaction) $processedTransaction = $transaction;
                }
                
                // 2. Buscar por payment_id
                if (!$processedTransaction) {
                    $transaction = \App\Models\Transaction::where('payment_id', $transactionId)
                                                          ->where('status', 0)
                                                          ->first();
                    if ($transaction) $processedTransaction = $transaction;
                }
                
                // 3. Buscar por user_id + valor (último recurso)
                if (!$processedTransaction && isset($data['userId'])) {
                    $pendingTransaction = \App\Models\Transaction::where('user_id', $data['userId'])
                                                                   ->where('status', 0)
                                                                   ->where('price', $data['amount'] ?? 0)
                                                                   ->where('gateway_name', 'bspay')
                                                                   ->orderBy('created_at', 'desc')
                                                                   ->first();
                    if ($pendingTransaction) {
                        $pendingTransaction->update(['gateway_transaction_id' => $transactionId]);
                        $processedTransaction = $pendingTransaction;
                    }
                }
                
                // PROCESSAR
                if ($processedTransaction) {
                    if (self::finalizePaymentBsPay($processedTransaction->payment_id, $processedTransaction->user_id, $processedTransaction->price)) {
                        \Log::info('BsPay - Pagamento finalizado com sucesso (formato original)!', [
                            'transaction_id' => $processedTransaction->id,
                            'reconciliation_method' => 'original_format'
                        ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Pagamento processado com sucesso',
                        'webhook_format' => 'BsPay_Original',
                            'transaction_id' => $processedTransaction->payment_id
                    ], 200);
                    }
                }
                
                \Log::error('BsPay - Falha ao finalizar pagamento (formato original)', [
                    'transaction_id' => $transactionId,
                    'user_id' => $data['userId'] ?? 'não informado',
                    'amount' => $data['amount'] ?? 'não informado'
                ]);
                return response()->json(['error' => 'Falha ao processar pagamento'], 500);
            }
        }
        
        // FORMATO 5: Outros formatos
        else {
            \Log::warning('BsPay - Formato de callback não reconhecido', [
                'data_keys' => array_keys($data),
                'data_sample' => array_slice($data, 0, 3)
            ]);
            
            // Tentar extrair dados comuns
            $transactionId = $data['transaction_id'] ?? $data['txid'] ?? $data['id'] ?? null;
            $status = $data['status'] ?? $data['state'] ?? null;
            
            if ($transactionId && $status) {
                \Log::info('BsPay - Tentando processar com dados extraídos', [
                    'transaction_id' => $transactionId,
                    'status' => $status
                ]);
                
                // Normalizar status
                $normalizedStatus = strtolower($status);
                
                if (in_array($normalizedStatus, ['paid', 'completed', 'approved', 'ok', 'success', 'confirmed'])) {
                    // APLICAR MESMA LÓGICA DE RECONCILIAÇÃO
                    $processedTransaction = null;
                    
                    // 1. Buscar por gateway_transaction_id
                    if ($transactionId) {
                        $transaction = \App\Models\Transaction::where('gateway_transaction_id', $transactionId)
                                                              ->where('status', 0)
                                                              ->first();
                        if ($transaction) $processedTransaction = $transaction;
                    }
                    
                    // 2. Buscar por payment_id
                    if (!$processedTransaction) {
                        $transaction = \App\Models\Transaction::where('payment_id', $transactionId)
                                                              ->where('status', 0)
                                                              ->first();
                        if ($transaction) $processedTransaction = $transaction;
                    }
                    
                    // 3. Buscar por user_id + valor (último recurso)
                    if (!$processedTransaction && $userId) {
                        $pendingTransaction = \App\Models\Transaction::where('user_id', $userId)
                                                                       ->where('status', 0)
                                                                       ->where('price', $amount)
                                                                       ->where('gateway_name', 'bspay')
                                                                       ->orderBy('created_at', 'desc')
                                                                       ->first();
                        if ($pendingTransaction) {
                            $pendingTransaction->update(['gateway_transaction_id' => $transactionId]);
                            $processedTransaction = $pendingTransaction;
                        }
                    }
                    
                    // PROCESSAR
                    if ($processedTransaction) {
                        if (self::finalizePaymentBsPay($processedTransaction->payment_id, $processedTransaction->user_id, $processedTransaction->price)) {
                            \Log::info('BsPay - Pagamento finalizado com formato alternativo!', [
                                'transaction_id' => $processedTransaction->id,
                                'reconciliation_method' => 'alternative_format'
                            ]);
                        return response()->json([
                            'success' => true,
                            'message' => 'Pagamento processado com sucesso',
                                'webhook_format' => 'BsPay_Alternative',
                                'transaction_id' => $processedTransaction->payment_id
                        ], 200);
                        }
                    }
                    
                    \Log::error('BsPay - Falha ao finalizar pagamento com formato alternativo', [
                        'transaction_id' => $transactionId,
                        'user_id' => $userId,
                        'amount' => $amount
                    ]);
                    return response()->json(['error' => 'Falha ao processar pagamento'], 500);
                }
            }
        }
    
        \Log::warning('BsPay - Callback não processado - formato não reconhecido');
        return response()->json([
            'error' => 'Callback não processado corretamente',
            'message' => 'Formato de dados não reconhecido',
            'received_data' => $data
        ], 400);
    }

    /**
     * Verifica se uma transação já foi processada para evitar duplicação
     * @param string $transactionId
     * @param string|null $identifier
     * @return bool
     */
    private function isTransactionAlreadyProcessed($transactionId, $identifier = null)
    {
        // REMOVIDO: Verificação excessiva que estava bloqueando primeiro processamento
        // A proteção principal está no BsPayTrait com lock de transação
        
        // Apenas verificar se é um webhook duplicado (mesmo conteúdo)
        // A verificação de status da transação será feita no BsPayTrait
        
        return false; // Sempre permite processamento, proteção está no trait
    }
    

    /**
     * Show the form for creating a new resource.
     * @dev victormsalatiel
     */
    public function consultStatusTransactionPix(Request $request)
    {
        return self::bsPayConsultStatusTransaction($request);
    }

    /**
     * Display the specified resource.
     * @dev victormsalatiel
     */
    public function confirmWithdrawalUser($id)
    {
        $withdrawal = Withdrawal::find($id);
        if (!empty($withdrawal)) { 
            $bspayment = BsPayPayment::create([
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'pix_key' => $withdrawal->pix_key,
                'pix_type' => $withdrawal->pix_type,
                'amount' => $withdrawal->amount,
                'observation' => 'bspay',
            ]);

            if ($bspayment) {
                $parm = [
                    'pix_key' => $withdrawal->pix_key,
                    'pix_type' => $withdrawal->pix_type,
                    'amount' => $withdrawal->amount,
                    'bspayment_id' => $bspayment->id
                ];

                $resp = self::MakePayment($parm);

                if ($resp) {
                    $withdrawal->update(['status' => 1]);
                    Notification::make()
                        ->title('Saque solicitado')
                        ->body('Saque solicitado com sucesso')
                        ->success()
                        ->send();

                    return back();
                } else {
                    Notification::make()
                        ->title('Erro no saque')
                        ->body('Erro ao solicitar o saque')
                        ->danger()
                        ->send();

                    return back();
                }
            }
        }
    }
   public function confirmWithdrawalAffiliate($id)
    {
        $withdrawal = AffiliateWithdraw::find($id);

        if (!empty($withdrawal)) {
            $suitpayment = SuitPayPayment::create([
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'pix_key' => $withdrawal->pix_key,
                'pix_type' => $withdrawal->pix_type,
                'amount' => $withdrawal->amount,
                'observation' => 'suitpay',
            ]);

            if ($suitpayment) {
                $parm = [
                    'pix_key' => $withdrawal->pix_key,
                    'pix_type' => $withdrawal->pix_type,
                    'amount' => $withdrawal->amount,
                    'suitpayment_id' => $suitpayment->id
                ];

                $resp = self::suitPayPixCashOut($parm);

                if ($resp) {
                    $withdrawal->update(['status' => 1]);
                    Notification::make()
                        ->title('Saque solicitado')
                        ->body('Saque solicitado com sucesso')
                        ->success()
                        ->send();

                    return back();
                } else {
                    Notification::make()
                        ->title('Erro no saque')
                        ->body('Erro ao solicitar o saque')
                        ->danger()
                        ->send();

                    return back();
                }
            }
        }
    }

  /**
     * Display the specified resource.
     */
    public function withdrawalFromModal($id, $action)
    {
        if($action == 'user') {
            return $this->confirmWithdrawalUser($id);
        }

        if($action == 'affiliate') {
            return $this->confirmWithdrawalAffiliate($id);
        }


    }

    /**
     * Cancel Withdrawal
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelWithdrawalFromModal($id, $action)
    {
        if($action == 'user') {
            return $this->cancelWithdrawalUser($id);
        }

        if($action == 'affiliate') {
            return $this->cancelWithdrawalAffiliate($id);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function cancelWithdrawalAffiliate($id)
    {
        $withdrawal = AffiliateWithdraw::find($id);
        if(!empty($withdrawal)) {
            // PROTEÇÃO CONTRA CANCELAMENTO DUPLICADO
            if ($withdrawal->status == 2) {
                Notification::make()
                    ->title('Saque já cancelado')
                    ->body('Este saque já foi cancelado anteriormente')
                    ->warning()
                    ->send();
                return back();
            }
            
            $wallet = Wallet::where('user_id', $withdrawal->user_id)
                ->where('currency', $withdrawal->currency)
                ->first();

            if(!empty($wallet)) {
                // Usar transação para garantir atomicidade
                \DB::transaction(function() use ($withdrawal, $wallet) {
                    // Verificar novamente se não foi cancelado por outro processo
                    $freshWithdrawal = AffiliateWithdraw::lockForUpdate()->find($withdrawal->id);
                    if ($freshWithdrawal->status == 2) {
                        throw new \Exception('Saque já cancelado por outro processo');
                    }
                    
                    // Creditar o saldo de volta
                $wallet->increment('refer_rewards', $withdrawal->amount);

                    // Marcar como cancelado
                    $freshWithdrawal->update(['status' => 2]);
                    
                    // Log de auditoria
                    \Log::info('BsPay - Saque afiliado cancelado com sucesso', [
                        'withdrawal_id' => $withdrawal->id,
                        'user_id' => $withdrawal->user_id,
                        'amount' => $withdrawal->amount,
                        'wallet_balance_after' => $wallet->fresh()->refer_rewards
                    ]);
                });

                Notification::make()
                    ->title('Saque cancelado')
                    ->body('Saque cancelado com sucesso')
                    ->success()
                    ->send();

                return back();
            }
            return back();
        }
        return back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function cancelWithdrawalUser($id)
    {
        $withdrawal = Withdrawal::find($id);
        if(!empty($withdrawal)) {
            // PROTEÇÃO CONTRA CANCELAMENTO DUPLICADO
            if ($withdrawal->status == 2) {
                Notification::make()
                    ->title('Saque já cancelado')
                    ->body('Este saque já foi cancelado anteriormente')
                    ->warning()
                    ->send();
                return back();
            }
            
            $wallet = Wallet::where('user_id', $withdrawal->user_id)
                ->where('currency', $withdrawal->currency)
                ->first();

            if(!empty($wallet)) {
                // Usar transação para garantir atomicidade
                \DB::transaction(function() use ($withdrawal, $wallet) {
                    // Verificar novamente se não foi cancelado por outro processo
                    $freshWithdrawal = Withdrawal::lockForUpdate()->find($withdrawal->id);
                    if ($freshWithdrawal->status == 2) {
                        throw new \Exception('Saque já cancelado por outro processo');
                    }
                    
                    // Creditar o saldo de volta
                $wallet->increment('balance_withdrawal', $withdrawal->amount);

                    // Marcar como cancelado
                    $freshWithdrawal->update(['status' => 2]);
                    
                    // Log de auditoria
                    \Log::info('BsPay - Saque usuário cancelado com sucesso', [
                        'withdrawal_id' => $withdrawal->id,
                        'user_id' => $withdrawal->user_id,
                        'amount' => $withdrawal->amount,
                        'wallet_balance_after' => $wallet->fresh()->balance_withdrawal
                    ]);
                });

                Notification::make()
                    ->title('Saque cancelado')
                    ->body('Saque cancelado com sucesso')
                    ->success()
                    ->send();

                return back();
            }
            return back();
        }
        return back();
    }
  public function checkTransactionStatusByToken(Request $request)
    {
        // Validação dos parâmetros recebidos
        $request->validate([
            'token' => 'required|string',
        ]);

        // Obtém o token do request
        $token = $request->input('token');

        // Obtém o usuário autenticado
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'error' => 'Usuário não autenticado.',
            ], 401);
        }

        // Busca a transação na tabela usando o token e o user_id
        $transaction = Transaction::where('user_id', $user->id)
            ->where('token', $token)
            ->first();

        // Verifica se a transação foi encontrada
        if ($transaction) {
            $status = $transaction->status;
            $statusMessage = $status == 1 ? 'Confirmado' : 'Aguardando pagamento';

            return response()->json([
                'status' => $status,
                'status_message' => $statusMessage,
            ]);
        } else {
            return response()->json([
                'error' => 'Transação não encontrada.',
            ], 404);
        }
    }

    public function testCallback(Request $request)
    {
        \Log::info('Requisição recebida: ' . json_encode($request->all()));
        
        $transactionId = $request->input('transactionId');
        if (!$transactionId) {
            return response()->json(['error' => 'Transaction ID é obrigatório para o teste'], 400);
        }
    
        try {
            // Gera o mock com base nos dados reais do depósito
            $mockRequest = self::mockCallbackBsPay($transactionId);
    
            // Processa o callback
            return $this->callbackMethod($mockRequest);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    private static function mockCallbackBsPay($transactionId)
    {
        // Recuperar o depósito real baseado no transactionId
        $deposit = Deposit::where('payment_id', $transactionId)->first();

        if (!$deposit) {
            \Log::error("Depósito não encontrado para o transactionId: $transactionId");
            return false;
        }

        // Mock de dados de retorno para simular o comportamento do sistema
        $mockData = [
            'requestBody' => [
                'transactionType' => 'RECEIVEPIX',
                'transactionId' => $transactionId,
                'transactionType' => 'RECEIVEPIX',
                'status' => 'PAID',
                'amount' => $deposit->amount, // Pegue o valor do depósito real
                'paymentType' => 'PIX',
                'dateApproval' => now(),
                'creditParty' => [
                    'name' => $deposit->user->name,
                    'email' => $deposit->user->email,
                    'taxId' => $deposit->user->cpf
                ],
                'debitParty' => [
                    'bank' => 'BSPAY SOLUCOES DE PAGAMENTOS LTDA',
                    'taxId' => '46872831000154'
                ]
            ]
        ];

        // Simula a requisição real
        return new Request($mockData);
    }


}
