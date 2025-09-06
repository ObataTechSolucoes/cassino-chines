<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
            // Buscar configurações dos gateways
            $settings = Setting::first();
            
            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'error' => 'Configurações não encontradas'
                ], 400);
            }

            // Verificar quais gateways estão ativos
            $activeGateways = [];
            
            if ($settings->suitpay_is_enable) {
                $activeGateways[] = 'suitpay';
            }
            
            if ($settings->bspay_is_enable) {
                $activeGateways[] = 'bspay';
            }
            
            if ($settings->digitopay_is_enable) {
                $activeGateways[] = 'digitopay';
            }
            
            if ($settings->ezzebank_is_enable) {
                $activeGateways[] = 'ezzepay';
            }
            
            if ($settings->cnpay_is_enable) {
                $activeGateways[] = 'cnpay';
            }

            // Se nenhum gateway estiver ativo
            if (empty($activeGateways)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Nenhum gateway de pagamento está ativo'
                ], 400);
            }

            // Selecionar gateway padrão ou primeiro disponível
            $selectedGateway = $settings->default_gateway ?? $activeGateways[0];
            
            // Verificar se o gateway padrão está ativo
            if (!in_array($selectedGateway, $activeGateways)) {
                $selectedGateway = $activeGateways[0]; // Usar o primeiro disponível
            }

            // Retornar gateway selecionado
            return response()->json([
                'success' => true,
                'gateway' => $selectedGateway,
                'available_gateways' => $activeGateways,
                'default_gateway' => $settings->default_gateway
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
            $settings = Setting::first();
            
            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'error' => 'Configurações não encontradas'
                ], 400);
            }

            $gateways = [
                'suitpay' => [
                    'name' => 'SuitPay',
                    'enabled' => (bool) $settings->suitpay_is_enable,
                    'is_default' => ($settings->default_gateway ?? 'suitpay') === 'suitpay'
                ],
                'bspay' => [
                    'name' => 'BsPay',
                    'enabled' => (bool) $settings->bspay_is_enable,
                    'is_default' => ($settings->default_gateway ?? 'suitpay') === 'bspay'
                ],
                'digitopay' => [
                    'name' => 'DigitoPay',
                    'enabled' => (bool) $settings->digitopay_is_enable,
                    'is_default' => ($settings->default_gateway ?? 'suitpay') === 'digitopay'
                ],
                'ezzepay' => [
                    'name' => 'EzzePay',
                    'enabled' => (bool) $settings->ezzebank_is_enable,
                    'is_default' => ($settings->default_gateway ?? 'suitpay') === 'ezzepay'
                ],
                'cnpay' => [
                    'name' => 'CNPay',
                    'enabled' => (bool) $settings->cnpay_is_enable,
                    'is_default' => ($settings->default_gateway ?? 'suitpay') === 'cnpay'
                ]
            ];

            return response()->json([
                'success' => true,
                'gateways' => $gateways,
                'default_gateway' => $settings->default_gateway ?? 'suitpay'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao listar gateways: ' . $e->getMessage()
            ], 500);
        }
    }
}
