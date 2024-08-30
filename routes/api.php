<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Live22\CashBonuController;
use App\Http\Controllers\Api\Live22\GameLoginController;
use App\Http\Controllers\Api\Live22\GameResultController;
use App\Http\Controllers\Api\Live22\GetBalanceController;
use App\Http\Controllers\Api\Live22\PlaceBetController;
use App\Http\Controllers\Api\Live22\RollBackController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::post('GetBalance', [GetBalanceController::class, 'getBalance']);
Route::post('Bet', [PlaceBetController::class, 'placeBet']);
Route::post('GameResult', [GameResultController::class, 'gameResult']);
Route::post('Rollback', [RollBackController::class, 'rollBack']);
Route::post('CashBonus', [CashBonuController::class, 'cashBonu']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('home', [AuthController::class, 'home']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'getUser']);
    Route::get('paymentType', [PaymentTypeController::class, 'index']);
    Route::get('agent-bank', [PaymentTypeController::class, 'getAgentBank']);
    Route::post('withdraw', [TransferController::class, 'withdraw']);
    Route::post('deposit', [TransferController::class, 'deposit']);
    Route::post('transactions', [TransactionController::class, 'index']);
    Route::get('deposit-history', [TransferController::class, 'depositHistory']);
    Route::get('withdraw-history', [TransferController::class, 'withdrawHistory']);

    // Route::group(['prefix' => 'live22sm'], function () {
    //    Route::post('/game/login', [GameLoginController::class, 'Gamelogin'])->name('api.game.login');
    //    Route::post('/game/get-balance', [GetBalanceController::class, 'getBalance'])->name('game.get-balance');
    // });
    Route::post('GameLogin', [GameLoginController::class, 'Gamelogin']);

});
