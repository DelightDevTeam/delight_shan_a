<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\Live22\GameLoginController;

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

Route::post('login', [AuthController::class, 'login']);
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

    Route::group(['prefix' => 'live22sm'], function () {
       Route::post('/game/login', [GameLoginController::class, 'Gamelogin'])->name('api.game.login');
    });
});
