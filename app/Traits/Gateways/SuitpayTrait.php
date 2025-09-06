<?php

namespace App\Traits\Gateways;
use Illuminate\Support\Facades\Log;
use App\Models\AffiliateHistory;
use App\Models\Deposit;
use App\Models\GamesKey;
use App\Models\Gateway;
use App\Models\Setting;
use App\Models\SuitPayPayment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\NewDepositNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Core;
use App\Helpers\Core as Helper;

trait SuitpayTrait
{
    /**
     * @var $uri
     * @var $clienteId
     * @var $clienteSecret
     */
    protected static string $uri;
    protected static string $clienteId;
    protected static string $clienteSecret;

    /**
     * Generate Credentials
     * Metodo para gerar credenciais
     * @return void
     */
    private static function suitPayGenerateCredentials()
    {
        $setting = Gateway::first();
        if (!empty($setting)) {
            self::$uri = $setting->getAttributes()['suitpay_uri'] ?? '';
            self::$clienteId = $setting->getAttributes()['suitpay_cliente_id'] ?? '';
            self::$clienteSecret = $setting->getAttributes()['suitpay_cliente_secret'] ?? '';
        }
    }

    /**
     * Request QRCODE
     * Metodo para solicitar uma QRCODE PIX
     * @return array
     */
    public static function suitPayRequestQrcode($request)
    {
        $setting = \Helper::getSetting();
        $rules = [
            'amount' => ['required', 'max:' . $setting->min_deposit, 'max:' . $setting->max_deposit],
            'cpf' => ['required', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        self::suitPayGenerateCredentials();

        $response = Http::withHeaders([
            'ci' => self::$clienteId,
            'cs' => self::$clienteSecret
        ])->post(self::$uri . 'gateway/request-qrcode', [
                    "requestNumber" => time(),
                    "dueDate" => Carbon::now()->addDay(),
                    "amount" => \Helper::amountPrepare($request->amount),
                    "shippingAmount" => 0.0,
                    "usernameCheckout" => "checkout",
                    "callbackUrl" => url('/suitpay/callback'),
                    "client" => [
                        "name" => auth('api')->user()->name,
                        "document" => \Helper::soNumero($request->cpf),
                        "phoneNumber" => \Helper::soNumero(auth('api')->user()->phone),
                        "email" => auth('api')->user()->email
                    ],
                ]);

        if ($response->successful()) {
            $responseData = $response->json();

            $token = self::suitPayGenerateTransaction($responseData['idTransaction'], \Helper::amountPrepare($request->amount), $request->accept_bonus);
            self::suitPayGenerateDeposit($responseData['idTransaction'], \Helper::amountPrepare($request->amount));

            return [
                'status' => true,
                'token' => $token, // Retornar token seguro
                'qrcode' => $responseData['paymentCode']
            ];
        }
    }

    /**
     * Consultar o status da transação
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function suitPayConsultStatusTransaction($request)
    {
        $transaction = Transaction::where('token', $request->token)->first(); // Usar token seguro
        if (!$transaction) {
            return response()->json(['status' => 'Transação não encontrada'], 400);
        }

        self::suitPayGenerateCredentials();

        $response = Http::withHeaders([
            'ci' => self::$clienteId,
            'cs' => self::$clienteSecret
        ])->post(self::$uri . 'gateway/consult-status-transaction', [
                    "typeTransaction" => "PIX",
                    "idTransaction" => $transaction->payment_id, // Usar idTransaction interno
                ]);

        if ($response->successful()) {
            $responseData = $response->json();

            if ($responseData == "PAID_OUT" || $responseData == "PAYMENT_ACCEPT") {
                if (self::suitPayFinalizePayment($request->idTransaction)) {
                    return response()->json(['status' => 'PAID']);
                }

                return response()->json(['status' => $responseData], 400);
            }

            return response()->json(['status' => $responseData], 400);
        } else {
            return response()->json(['status' => 'Erro na consulta do status'], 500);
        }
    }


    /**
     * @param $idTransaction
     * @return bool
     */
    public static function suitPayFinalizePayment($idTransaction): bool
    {
        $transaction = Transaction::where('payment_id', $idTransaction)->where('status', 0)->first();
        $setting = Helper::getSetting();

        if (!empty($transaction)) {
            $user = User::find($transaction->user_id);
            $wallet = Wallet::where('user_id', $transaction->user_id)->first();

            if (!empty($wallet)) {
                // Verifica se é o primeiro depósito
                $checkTransactions = Transaction::where('user_id', $transaction->user_id)
                    ->where('status', 1)
                    ->count();

                if ($checkTransactions == 0 || empty($checkTransactions)) {
                    if ($transaction->accept_bonus) {
                        // Pagar o bônus de primeiro depósito
                        $bonus = Helper::porcentagem_xn($setting->initial_bonus, $transaction->price);
                        $wallet->increment('balance_bonus', $bonus);

                        if (!$setting->disable_rollover) {
                            $wallet->update(['balance_bonus_rollover' => $bonus * $setting->rollover]);
                        }
                    }
                }

                // Rollover do depósito
                if (!$setting->disable_rollover) {
                    $wallet->increment('balance_deposit_rollover', ($transaction->price * intval($setting->rollover_deposit)));
                }

                // Acumular bônus
                Helper::payBonusVip($wallet, $transaction->price);

                // Dinheiro direto para carteira de saque ou jogos
                if ($setting->disable_rollover) {
                    $wallet->increment('balance_withdrawal', $transaction->price);
                } else {
                    $wallet->increment('balance', $transaction->price);
                }

                if ($transaction->update(['status' => 1])) {
                    $deposit = Deposit::where('payment_id', $idTransaction)->where('status', 0)->first();

                    if (!empty($deposit)) {
                        // CPA para primeiro depósito
                        $affHistoryCPA = AffiliateHistory::where('user_id', $user->id)
                            ->where('commission_type', 'cpa')
                            ->first();

                        if (!empty($affHistoryCPA)) {
                            $affHistoryCPA->increment('deposited_amount', $transaction->price);

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
                        if ($transaction->price >= $setting->cpa_percentage_baseline) {
                            $inviterN1 = User::find($user->inviter);

                            if (!empty($inviterN1)) {
                                $commissionN1 = $transaction->price * ($setting->cpa_percentage_n1 / 100);
                                $walletN1 = Wallet::where('user_id', $inviterN1->id)->first();
                                if (!empty($walletN1)) {
                                    $walletN1->increment('refer_rewards', $commissionN1);
                                }

                                // Nível 2
                                $inviterN2 = User::find($inviterN1->inviter);
                                if (!empty($inviterN2)) {
                                    $commissionN2 = $transaction->price * ($setting->cpa_percentage_n2 / 100);
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
                                $admin->notify(new NewDepositNotification($user->name, $transaction->price));
                            }

                            return true;
                        }
                        return false;
                    }
                    return false;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    /**
     * @param $idTransaction
     * @param $amount
     * @return void
     */
    private static function suitPayGenerateDeposit($idTransaction, $amount)
    {
        $userId = auth('api')->user()->id;
        $wallet = Wallet::where('user_id', $userId)->first();

        Deposit::create([
            'payment_id' => $idTransaction,
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'pix',
            'currency' => $wallet->currency,
            'symbol' => $wallet->symbol,
            'status' => 0
        ]);
    }

    /**
     * @param $idTransaction
     * @param $amount
     * @return void
     */
    private static function suitPayGenerateTransaction($idTransaction, $amount, $accept_bonus)
    {
        $setting = \Helper::getSetting();
        $token = bin2hex(random_bytes(16)); // Gerar token seguro

        Transaction::create([
            'payment_id' => $idTransaction,
            'user_id' => auth('api')->user()->id,
            'payment_method' => 'pix',
            'price' => $amount,
            'currency' => $setting->currency_code,
            'accept_bonus' => $accept_bonus,
            'status' => 0,
            'token' => $token, // Armazenar token seguro
        ]);

        return $token; // Retornar token seguro
    }
    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public static function suitPayPixCashOut(array $array): bool
    {
    
        self::suitPayGenerateCredentials();
    
    
        try {
            $endpoint = self::$uri . 'gateway/pix-payment';
    
            $response = Http::withHeaders([
                'ci' => self::$clienteId,
                'cs' => self::$clienteSecret
            ])->post($endpoint, [
                "value" => $array['amount'],
                "key" => $array['pix_key'],
                "typeKey" => "document", // fixo no seu código
                "documentValidation" => $array['cpf'],
                'callbackUrl' => url('/suitpay/payment'),
            ]);
    
    
            if ($response->successful()) {
                $responseData = $response->json();
    
                if (isset($responseData['response']) && $responseData['response'] === 'OK') {
    
                    $suitPayPayment = SuitPayPayment::lockForUpdate()->find($array['suitpayment_id']);
    
                    if (!empty($suitPayPayment)) {
                        if ($suitPayPayment->update(['status' => 1, 'payment_id' => $responseData['idTransaction']])) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
