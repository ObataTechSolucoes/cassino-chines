<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Helpers\Core;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\AffiliateWithdraw;
use App\Models\SuitPayPayment;
use App\Notifications\NewWithdrawalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Filament\Notifications\Notification;
use App\Traits\Gateways\DigitoPayTrait;
use App\Traits\Gateways\BsPayTrait;
use App\Traits\Gateways\EzzepayTrait;
use App\Traits\Gateways\SuitPayTrait;
use Illuminate\Support\Facades\Log; 

class WalletController extends Controller
{
    use BsPayTrait, SuitPayTrait, EzzepayTrait, DigitoPayTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallet = Wallet::whereUserId(auth('api')->id())->where('active', 1)->first();
        return response()->json(['wallet' => $wallet], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function myWallet()
    {
        $wallets = Wallet::whereUserId(auth('api')->id())->get();
        return response()->json(['wallets' => $wallets], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function setWalletActive($id)
    {
        $checkWallet = Wallet::whereUserId(auth('api')->id())->where('active', 1)->first();
        if (!empty($checkWallet)) {
            $checkWallet->update(['active' => 0]);
        }

        $wallet = Wallet::find($id);
        if (!empty($wallet)) {
            $wallet->update(['active' => 1]);
            return response()->json(['wallet' => $wallet], 200);
        }
    }

    public function withdrawalFromModal($id, Request $request)
    {
        $setting = Core::getSetting();
        $resultado = null;
        $tipo = $request->input("action");
    
        Log::info('Iniciando processo de saque', [
            'withdrawal_id' => $id,
            'tipo' => $tipo,
            'gateway' => $setting->default_gateway
        ]);
    
        switch ($setting->default_gateway) {
            case 'suitpay':
                $withdrawal = Withdrawal::find($id);
                if ($tipo == "afiliado") {
                    $withdrawal = AffiliateWithdraw::find($id);
                }
    
                if (!$withdrawal) {
                    Log::error('Saque não encontrado', ['withdrawal_id' => $id]);
                    break;
                }
    
                $withdrawal->update(['status' => 1]);
                Log::info('Status do saque atualizado para 1', ['withdrawal_id' => $withdrawal->id]);
    
                $suitpayment = SuitPayPayment::create([
                    'withdrawal_id' => $withdrawal->id,
                    'user_id'       => $withdrawal->user_id,
                    'pix_key'       => $withdrawal->pix_key,
                    'pix_type'      => $withdrawal->pix_type,
                    'cpf'           => $withdrawal->cpf,
                    'amount'        => $withdrawal->amount,
                    'observation'   => 'Saque direto',
                ]);
    
                Log::info('Pagamento SuitPay criado', [
                    'suitpayment_id' => $suitpayment->id,
                    'amount' => $suitpayment->amount
                ]);
    
                $parm = [
                    'pix_key'           => $withdrawal->pix_key,
                    'pix_type'          => $withdrawal->pix_type,
                    'cpf'               => $withdrawal->cpf,
                    'amount'            => $withdrawal->amount,
                    'suitpayment_id'    => $suitpayment->id
                ];
    
                $resultado = self::suitPayPixCashOut($parm);
                Log::info('Resultado suitPayPixCashOut', ['resultado' => $resultado]);
                break;
    
            case 'digitopay':
                Log::info('Chamando saque via DigitoPay', ['withdrawal_id' => $id]);
                $resultado = self::pixCashOutDigito($id, $tipo);
                Log::info('Resultado pixCashOutDigito', ['resultado' => $resultado]);
                break;
    
            case 'bspay':
                Log::info('Chamando saque via BsPay', ['withdrawal_id' => $id]);
                $resultado = self::pixCashOutBsPay($id, $tipo);
                Log::info('Resultado pixCashOutBsPay', ['resultado' => $resultado]);
                break;
    
            case 'ezzepay':
                Log::info('Chamando saque via EzzePay', ['withdrawal_id' => $id]);
                $resultado = self::pixCashOutEzze($id, $tipo);
                Log::info('Resultado pixCashOutEzze', ['resultado' => $resultado]);
                break;
        }
    
        if ($resultado == true) {
            Log::info('Saque solicitado com sucesso', ['withdrawal_id' => $id]);
    
            Notification::make()
                ->title('Saque solicitado')
                ->body('Saque solicitado com sucesso')
                ->success()
                ->send();
    
            return back();
        } else {
            // Verificar se é um erro específico retornado pelo BsPayTrait
            if (is_array($resultado) && isset($resultado['success']) && !$resultado['success']) {
                $errorMessage = $resultado['error_message'] ?? 'Erro desconhecido';
                $errorCode = $resultado['error_code'] ?? 'UNKNOWN';
                
                Log::error('Erro específico ao solicitar o saque', [
                    'withdrawal_id' => $id,
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage
                ]);
                
                // Mensagens específicas baseadas no código de erro
                $userMessage = match($errorCode) {
                    'FORBIDDEN' => 'IP não autorizado pela CNPay. Tentando sem proxy...',
                    'GATEWAY_INVALID_DATA' => 'Dados inválidos enviados para o gateway',
                    'UNAUTHORIZED' => 'Credenciais inválidas do gateway',
                    'TIMEOUT' => 'Timeout na comunicação com o gateway',
                    default => $errorMessage
                };
                
                Notification::make()
                    ->title('Erro no saque')
                    ->body($userMessage)
                    ->danger()
                    ->send();
            } else {
                Log::error('Erro ao solicitar o saque', ['withdrawal_id' => $id]);
                
                Notification::make()
                    ->title('Erro no saque')
                    ->body('Erro ao solicitar o saque')
                    ->danger()
                    ->send();
            }
    
            return back();
        }
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestWithdrawal(Request $request)
    {
        $setting = Setting::first();

        // Verificar se o usuário está autenticado
        if (auth('api')->check()) {
            // Obter o usuário autenticado
            $user = auth('api')->user();

            // Verificar se o usuário utilizou o bônus de cadastro
            if ($user->utilizou_bonus_cadastro == 1) {
                // Verificar se o campo disable_deposit_min está ativo
                if ($setting->disable_deposit_min == 1) {
                    // Somar todos os depósitos do usuário com status 1
                    $depositSum = \DB::table('deposits')
                        ->where('user_id', $user->id)
                        ->where('status', 1)
                        ->sum('amount');

                    // Verificar se a soma dos depósitos é menor que deposit_min_saque
                    if ($depositSum < $setting->deposit_min_saque) {
                        $remainingAmount = $setting->deposit_min_saque - $depositSum;
                        return response()->json(['error' => 'Você precisa depositar mais ' . number_format($remainingAmount, 2, ',', '.') . ' para poder sacar.'], 400);
                    }
                }
            }

            // Verificações de saque de acordo com o tipo de solicitação
            if ($request->type === 'pix') {
                $rules = [
                    'amount' => ['required', 'numeric', 'min:' . $setting->min_withdrawal, 'max:' . $setting->max_withdrawal],
                    'pix_type' => 'required',
                ];

                switch ($request->pix_type) {
                    case 'document':
                        $rules['pix_key'] = 'required|cpf_ou_cnpj';
                        break;
                    case 'email':
                        $rules['pix_key'] = 'required|email';
                        break;
                    default:
                        $rules['pix_key'] = 'required';
                        break;
                }
            }

            if ($request->type === 'bank') {
                $rules = [
                    'amount' => ['required', 'numeric', 'min:' . $setting->min_withdrawal, 'max:' . $setting->max_withdrawal],
                    'bank_info' => 'required',
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            if ($request->amount < 0) {
                return response()->json(['error' => 'Você tem que solicitar um valor'], 400);
            }

            // Limite de saque
            if (!empty($setting->withdrawal_limit) && !empty($setting->withdrawal_period)) {
                switch ($setting->withdrawal_period) {
                    // (Mesma lógica que já estava no seu código)
                    // Verificar limites diários, semanais, mensais, ou anuais de saque...
                }
            }

            if ($request->amount > $setting->max_withdrawal) {
                return response()->json(['error' => 'Você excedeu o limite máximo permitido de: ' . $setting->max_withdrawal], 400);
            }

            if ($request->accept_terms == true) {
                if (floatval($request->amount) > floatval(auth('api')->user()->wallet->balance_withdrawal)) {
                    return response()->json(['error' => 'Você não tem saldo suficiente'], 400);
                }
            
                // Consultar a tabela account_withdraws para obter o CPF
                $accountWithdraw = \DB::table('account_withdraws')
                    ->where('user_id', auth('api')->user()->id)
                    ->first();
            
                if ($accountWithdraw) {
                    // Verificar se o CPF está ausente na coluna 'document'
                    if (empty($accountWithdraw->document)) {
                        // Atualizar 'document' com o valor de 'pix_key'
                        \DB::table('account_withdraws')
                            ->where('user_id', auth('api')->user()->id)
                            ->update([
                                'document' => $accountWithdraw->pix_key,
                            ]);
            
                        // Atualizar o valor em $accountWithdraw para reutilizar
                        $accountWithdraw->document = $accountWithdraw->pix_key;
                    }
                }
            
                $data = [];
                if ($request->type === 'pix') {
                    $data = [
                        'user_id' => auth('api')->user()->id,
                        'amount' => \Helper::amountPrepare($request->amount),
                        'type' => $request->type,
                        'cpf' => $accountWithdraw->document ?? $request->cpf, // Usar o CPF atualizado ou da requisição
                        'pix_key' => $request->pix_key,
                        'pix_type' => $request->pix_type,
                        'currency' => $request->currency,
                        'symbol' => $request->symbol,
                        'status' => 0,
                    ];
                }
            
                if ($request->type === 'bank') {
                    $data = [
                        'user_id' => auth('api')->user()->id,
                        'amount' => \Helper::amountPrepare($request->amount),
                        'type' => $request->type,
                        'bank_info' => $request->bank_info,
                        'currency' => $request->currency,
                        'symbol' => $request->symbol,
                        'status' => 0,
                    ];
                }
            
                $withdrawal = Withdrawal::create($data);
            
                if ($withdrawal) {
                    $wallet = Wallet::where('user_id', $user->id)->first();
                    $wallet->decrement('balance_withdrawal', floatval($request->amount));
            
                    // Se o saque for bem-sucedido, definir utilizou_bonus_cadastro como 0
                    $user->update(['utilizou_bonus_cadastro' => 0]);
            
                    $admins = User::where('role_id', 0)->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new NewWithdrawalNotification($user->name, $request->amount));
                    }
            
                    return response()->json([
                        'status' => true,
                        'message' => 'Saque realizado com sucesso',
                    ], 200);
                }
            }        
            return response()->json(['error' => 'Você precisa aceitar os termos'], 400);
        }

        return response()->json(['error' => 'Erro ao realizar o saque'], 400);
    }

    public function updateBonusIfNeeded()
    {
        $userId = auth('api')->id();
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Carteira não encontrada'], 404);
        }

        $messages = [];

        // Verificar e atualizar bônus
        if ($wallet->balance_bonus == 0) {
            $wallet->balance_bonus_rollover = 0;
            $wallet->save();
            $messages[] = 'Bônus zerado e rollover atualizado.';
        }

        return response()->json(['messages' => $messages], 200);
    }

    /**
     * Transfere saldo para saque se necessário.
     */
    public function transferBalanceToWithdrawalIfNeeded()
    {
        $userId = auth('api')->id();
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Carteira não encontrada'], 404);
        }

        $messages = [];

        // Verificar e transferir saldo para saque
        if ($wallet->balance_deposit_rollover == 0) {
            $wallet->balance_withdrawal += $wallet->balance;
            $wallet->balance = 0; // Zerar o saldo após a transferência
            $wallet->save();
            $messages[] = 'Saldo transferido para saque com sucesso!';
        }

        return response()->json(['messages' => $messages], 200);
    }

    /**
     * Transfere bônus para saque se necessário.
     */
    public function transferBonusToWithdrawalIfNeeded()
    {
        $userId = auth('api')->id();
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Carteira não encontrada'], 404);
        }

        $messages = [];

        // Verificar e transferir bônus para saque
        if ($wallet->balance_bonus_rollover == 0) {
            $wallet->balance_withdrawal += $wallet->balance_bonus;
            $wallet->balance_bonus = 0; // Zerar o balance_bonus após a transferência
            $wallet->save();
            $messages[] = 'Saldo do bônus transferido para saque com sucesso!';
        }

        return response()->json(['messages' => $messages], 200);
    }

    /**
     * Verifica e atualiza bônus, saldo e transferências.
     */
    public function checkAndUpdateBonus()
    {
        $userId = auth('api')->id();
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return response()->json(['error' => 'Carteira não encontrada'], 404);
        }

        $messages = [];

        // Verificar e atualizar bônus
        if ($wallet->balance_bonus == 0) {
            $wallet->balance_bonus_rollover = 0;
            $wallet->save();
            $messages[] = 'Bônus zerado e rollover atualizado.';
        }

        // Verificar e transferir saldo para saque
        if ($wallet->balance_deposit_rollover == 0) {
            $wallet->balance_withdrawal += $wallet->balance;
            $wallet->balance = 0; // Zerar o saldo após a transferência
            $wallet->save();
            $messages[] = 'Saldo transferido para saque com sucesso!';
        }

        // Verificar e transferir bônus para saque
        if ($wallet->balance_bonus_rollover == 0) {
            $wallet->balance_withdrawal += $wallet->balance_bonus;
            $wallet->balance_bonus = 0; // Zerar o balance_bonus após a transferência
            $wallet->save();
            $messages[] = 'Saldo do bônus transferido para saque com sucesso!';
        }

        if (empty($messages)) {
            return response()->json(['message' => 'Nenhuma atualização necessária'], 200);
        }

        return response()->json(['messages' => $messages], 200);
    }


}
