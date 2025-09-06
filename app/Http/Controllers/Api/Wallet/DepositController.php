<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Traits\Gateways\SuitpayTrait;
use App\Traits\Gateways\BsPayTrait;
use App\Traits\Gateways\DigitoPayTrait;
use App\Traits\Gateways\EzzepayTrait;
use App\Traits\Gateways\CNPayTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    use SuitpayTrait, BsPayTrait, DigitoPaytrait, EzzepayTrait, CNPayTrait;

    /**
     * @param Request $request
     * @return array|false[]
     */
    public function submitPayment(Request $request)
    {
        // Log da requisição recebida
        Log::info('DepositController - submitPayment chamado', [
            'user_id' => auth('api')->id(),
            'request_data' => $request->all(),
            'gateway' => $request->gateway,
            'amount' => $request->amount,
            'timestamp' => now()->toISOString()
        ]);

        // Se não foi especificado gateway, selecionar automaticamente
        if (!$request->gateway) {
            Log::info('DepositController - Gateway não especificado, selecionando automaticamente');
            
            $gatewaySelection = app(\App\Http\Controllers\Api\Wallet\GatewaySelectionController::class);
            $gatewayResponse = $gatewaySelection->selectGateway($request);
            
            if (!$gatewayResponse->getData()->success) {
                Log::warning('DepositController - Nenhum gateway disponível para seleção automática');
                return response()->json(['error' => 'Nenhum gateway disponível'], 400);
            }
            
            $request->merge(['gateway' => $gatewayResponse->getData()->gateway]);
            Log::info('DepositController - Gateway selecionado automaticamente', [
                'gateway_selecionado' => $request->gateway
            ]);
        }
         
        switch ($request->gateway) {
           
            case 'suitpay':
                Log::info('DepositController - Usando gateway SuitPay');
                return self::suitPayRequestQrcode($request);
                
            case 'bspay':
                Log::info('DepositController - Usando gateway BsPay');
                return $this->requestQrcodeBsPay($request);
                
            case 'ezzepay':
                Log::info('DepositController - Usando gateway EzzePay');
                return self::requestQrcodeEzze($request);
                
            case 'digitopay':
                Log::info('DepositController - Usando gateway DigitoPay');
                return self::requestQrcodeDigito($request);
                
            case 'cnpay':
                Log::info('DepositController - Usando gateway CNPay');
                return self::requestQrcodeCNPay($request);
                
            default:
                Log::warning('DepositController - Gateway não suportado', [
                    'gateway' => $request->gateway,
                    'user_id' => auth('api')->id()
                ]);
                return response()->json(['error' => 'Gateway not supported: ' . $request->gateway], 400);
        }
    }

    public function consultStatusTransactionPix(Request $request)
    {
        Log::info('DepositController - consultStatusTransactionPix chamado', [
            'user_id' => auth('api')->id(),
            'request_data' => $request->all(),
            'gateway' => $request->gateway
        ]);

        // Verificar se é uma transação CNPay
        if ($request->gateway === 'cnpay' || $request->has('cnpay_transaction')) {
            Log::info('DepositController - Consultando status CNPay');
            return $this->consultCNPayStatus($request);
        }
        
        // Para outros gateways, usar método padrão
        Log::info('DepositController - Usando método padrão para consulta de status');
        return self::consultStatusTransaction($request);
    }

    /**
     * Consultar status de transação CNPay
     */
    private function consultCNPayStatus(Request $request)
    {
        try {
            Log::info('DepositController - consultCNPayStatus iniciado', [
                'request_data' => $request->all(),
                'user_id' => auth('api')->id()
            ]);

            $request->validate([
                'payment_id' => 'required|string',
                'gateway' => 'required|string|in:cnpay'
            ]);

            $paymentId = $request->payment_id;
            
            Log::info('DepositController - Verificando status CNPay', [
                'payment_id' => $paymentId,
                'user_id' => auth('api')->id()
            ]);
            
            // Usar o trait CNPay para verificar status
            $status = self::checkCNPayPaymentStatus($paymentId);
            
            Log::info('DepositController - Status CNPay verificado', [
                'payment_id' => $paymentId,
                'status_result' => $status,
                'user_id' => auth('api')->id()
            ]);
            
            if ($status) {
                return response()->json([
                    'success' => true,
                    'status' => $status['status'] ?? 'unknown',
                    'data' => $status
                ]);
            }

            Log::warning('DepositController - Não foi possível verificar status CNPay', [
                'payment_id' => $paymentId,
                'user_id' => auth('api')->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Não foi possível verificar o status da transação'
            ], 400);

        } catch (\Exception $e) {
            Log::error('DepositController - Erro ao verificar status CNPay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_id' => $request->payment_id ?? 'não fornecido',
                'user_id' => auth('api')->id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao verificar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = auth('api')->id();

        Log::info('DepositController - index chamado', [
            'user_id' => $userId,
            'request_data' => $request->all()
        ]);

        // Inicia a consulta base
        $query = Deposit::whereUserId($userId);

        // Filtra por data se o parâmetro 'filter' estiver presente
        if ($request->has('filter')) {
            $filter = $request->input('filter');

            switch ($filter) {
                case 'today':
                    $startOfDay = Carbon::today();
                    $endOfDay = Carbon::tomorrow()->subSecond();
                    $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
                    break;

                case 'week':
                    $startOfWeek = Carbon::now()->startOfWeek();
                    $endOfWeek = Carbon::now()->endOfWeek();
                    $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                    break;

                case 'month':
                    $startOfMonth = Carbon::now()->startOfMonth();
                    $endOfMonth = Carbon::now()->endOfMonth();
                    $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                    break;

                case 'year':
                    $startOfYear = Carbon::now()->startOfYear();
                    $endOfYear = Carbon::now()->endOfYear();
                    $query->whereBetween('created_at', [$startOfYear, $endOfYear]);
                    break;

                default:
                    // Caso o filtro não seja reconhecido, retorna todos os resultados
                    break;
            }
        }

        // Pagina os resultados
        $deposits = $query->paginate();

        Log::info('DepositController - index concluído', [
            'user_id' => $userId,
            'total_deposits' => $deposits->total(),
            'current_page' => $deposits->currentPage()
        ]);

        return response()->json(['deposits' => $deposits], 200);
    }

}

