<?php

use App\Http\Controllers\BauController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\AccountWithdrawController;
use App\Http\Controllers\Api\Profile\WalletController;
use Illuminate\Http\Request;






// Processamento 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|Sme
*/

Route::post('/account_withdraw', [AccountWithdrawController::class, 'store']);
Route::get('/api/musics', [MusicController::class, 'index']);


Route::put('/api/bau/{id}/abrir', [BauController::class, 'abrirBau']);
// ROTA DE SAQUE 
Route::get('/withdrawal/{id}', [WalletController::class, 'withdrawalFromModal'])->name('withdrawal');



// Additional web route files are auto-loaded by RouteServiceProvider










