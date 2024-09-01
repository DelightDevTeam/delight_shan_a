<?php

use App\Services\DemoGameListService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\Live22\CashBonuController;
use App\Http\Controllers\Api\Live22\PlaceBetController;
use App\Http\Controllers\Api\Live22\RollBackController;
use App\Http\Controllers\Api\Live22\GameLoginController;
use App\Http\Controllers\Api\Live22\GameResultController;
use App\Http\Controllers\Api\Live22\GetBalanceController;
use App\Http\Controllers\Api\Live22\GetGameListController;

Route::post('login', [AuthController::class, 'login']);

Route::post('GetBalance', [GetBalanceController::class, 'getBalance']);
Route::post('Bet', [PlaceBetController::class, 'placeBet']);
Route::post('GameResult', [GameResultController::class, 'gameResult']);
Route::post('Rollback', [RollBackController::class, 'rollBack']);
Route::post('CashBonus', [CashBonuController::class, 'cashBonu']);
Route::get('GetGameList/{productId}/', [GameLoginController::class, 'getGameList']);
Route::get('GetGameType', [GameLoginController::class, 'getGameType']);
Route::get('gameProductType/{productId}', [GameLoginController::class, 'getProductType']);
Route::get('GetGameList/{productId}/{gameTypeId}', [GameLoginController::class, 'getGameList']);
Route::get('GetHasDemo', [GameLoginController::class, 'getHasDemo']);

Route::get('LaunchGameDemo', [GameLoginController::class, 'launchGameDemoPlay']);
Route::get('GameLists', [GetGameListController::class, 'getGames']);
Route::get('DemoGameList', function (DemoGameListService $service) {
    $lang = request()->get('lang', 'en-us'); // Default to 'en-us' if not specified
    return $service->getDemoGameList($lang);
});
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
         Route::post('GameLogin', [GameLoginController::class, 'Gamelogin']);
         Route::post('GetBalance', [GetBalanceController::class, 'getBalance']);
     });

});
