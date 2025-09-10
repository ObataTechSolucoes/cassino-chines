<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Traits\Gateways\CNPayTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    use CNPayTrait;

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

        // Forçar o uso do gateway CNPay
        Log::info('DepositController - Forçando uso do gateway CNPay');
        return self::requestQrcodeCNPay($request);
    }

    public function consultStatusTransactionPix(Request $request)
    {
        Log::info('DepositController - consultStatusTransactionPix chamado', [
            'user_id' => auth('api')->id(),
            'request_data' => $request->all(),
        ]);

        return $this->consultCNPayStatus($request);
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

