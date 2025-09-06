<?php

use App\Http\Controllers\Api\Profile\ProfileController;
use App\Http\Controllers\BauController;
use App\Http\Controllers\SliderTextController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepositanteController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Missions\MissionController;
use App\Http\Controllers\Api\Profile\AffiliateController;
use App\Http\Controllers\AccountWithdrawController;
use App\Http\Controllers\SenSaqueController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Api\Profile\VipController;
use App\Http\Controllers\MissionDepositController;
use App\Http\Controllers\Gateway\SuitPayController;
use App\Http\Controllers\PostNotificationsController;
use App\Http\Controllers\BonusInitialController;
use App\Http\Controllers\Api\Profile\WalletController;
use App\Http\Controllers\Gateway\BsPayController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Rotas antigas duplicadas/fora de padrão removidas (spin-wheel)
// Já existe abaixo uma rota /user; evitando duplicidade
Route::get('/check-deposit-min', [BonusInitialController::class, 'checkDeposit']);

Route::middleware('auth:api')->group(function () {
    // Verificar e atualizar bônus se necessário
    Route::get('profile/wallet/update-bonus-if-needed', [WalletController::class, 'updateBonusIfNeeded']);

    // Transferir saldo para saque se necessário
    Route::get('profile/wallet/transfer-balance-to-withdrawal-if-needed', [WalletController::class, 'transferBalanceToWithdrawalIfNeeded']);

    // Transferir bônus para saque se necessário
    Route::get('profile/wallet/transfer-bonus-to-withdrawal-if-needed', [WalletController::class, 'transferBonusToWithdrawalIfNeeded']);

    // Verificar e atualizar bônus, saldo e transferências
    Route::get('profile/wallet/check-and-update-bonus', [WalletController::class, 'checkAndUpdateBonus']);
});
Route::get('/transaction/status/by-token', [App\Http\Controllers\Api\Wallet\TransactionStatusController::class, 'checkStatusByToken']);

Route::get('/mission-deposits', [MissionDepositController::class, 'index']);
Route::get('/user-deposits', [MissionDepositController::class, 'userDeposits']);

Route::get('/events', [EventController::class, 'index']);

Route::post('/verify-pin', [SenSaqueController::class, 'verifyPin']);

Route::post('/sen-saque', [SenSaqueController::class, 'store']);

Route::get('/verify-senha-saque', [SenSaqueController::class, 'checkUserHasPin']);

// Deprecated closure removed; use /api/auth/me


Route::middleware('auth:api')->group(function () {
    Route::get('/user-deposits', [MissionDepositController::class, 'userDeposits']);
    Route::get('/missions/check-status', [MissionDepositController::class, 'checkMissionStatus']);
    Route::post('/missions/collect/{id}', [MissionDepositController::class, 'collectMission']);
});
Route::post('/profile/collect', [VipController::class, 'collect']);



Route::post('/account_withdraw', [AccountWithdrawController::class, 'store']);

// CNPay routes are auto-loaded from routes/api/cnpay.php

// Auth routes are auto-loaded from routes/api/auth/auth.php

// Profile, wallet, and missions routes are auto-loaded from routes/api/**


// Categories routes auto-loaded from routes/api/categories/index.php

// Games and gateways routes are auto-loaded from routes/api/**

// Search routes auto-loaded from routes/api/search/search.php

Route::prefix('profile')
    ->group(function () {
        Route::post('/getLanguage', [ProfileController::class, 'getLanguage']);
        Route::put('/updateLanguage', [ProfileController::class, 'updateLanguage']);
        Route::post('/upload-avatar', [ProfileController::class, 'uploadAvatar']);
        Route::post('/update-facebook-id', [ProfileController::class, 'updateFacebookId']);
        Route::post('/update-whatsapp-id', [ProfileController::class, 'updateWhatsappId']);
        Route::post('/update-telegram-id', [ProfileController::class, 'updateTelegramId']);
        Route::post('/update-aniversario', [ProfileController::class, 'updateAniversario']);
    });

Route::prefix('providers')
    ->group(function () {

    });
// Em routes/api.php
Route::get('/post-notifications', [PostNotificationsController::class, 'index']);
Route::get('/slider-text', [SliderTextController::class, 'index']);

// Settings routes auto-loaded from routes/api/settings/*

// Spin routes auto-loaded from routes/api/spin/index.php


// No closures: delegate to controller
Route::middleware('auth:api')->get('/user/cpf', [ProfileController::class, 'getCpf']);


