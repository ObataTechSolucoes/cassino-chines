<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GatewaySelectionController extends Controller
{
    /**
     * Selecionar gateway automaticamente para depósito
     */
    public function selectGateway(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'gateway' => 'cnpay',
                'available_gateways' => ['cnpay'],
                'default_gateway' => 'cnpay',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao selecionar gateway: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar gateways disponíveis
     */
    public function listGateways(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'gateways' => [
                    'cnpay' => [
                        'name' => 'CNPay',
                        'enabled' => true,
                        'is_default' => true,
                    ],
                ],
                'default_gateway' => 'cnpay',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao listar gateways: ' . $e->getMessage()
            ], 500);
        }
    }
}
