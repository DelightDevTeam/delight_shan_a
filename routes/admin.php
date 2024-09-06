<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DepositRequestController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\SeniorController;
use App\Http\Controllers\Admin\WithdrawRequestController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'admin', 'as' => 'admin.',
    'middleware' => ['auth', 'checkBanned'],
], function () {

    Route::post('balance-up', [HomeController::class, 'balanceUp'])->name('balanceUp');
    Route::get('logs/{id}', [HomeController::class, 'logs'])
        ->name('logs');

    Route::resource('banners', BannerController::class);
    Route::resource('bannerText', BannerTextController::class);
    Route::resource('senior', SeniorController::class);
    Route::resource('bank', BankController::class);
    Route::resource('promotion', PromotionController::class);
    Route::resource('contact', ContactController::class);
    Route::post('senior/{senior}/ban', [SeniorController::class, 'ban'])->name('senior.ban');
    Route::get('senior/{senior}/deposit', [SeniorController::class, 'deposit'])->name('senior.deposit');
    Route::post('senior/{senior}/deposit', [SeniorController::class, 'makeDeposit'])->name('senior.makeDeposit');
    Route::get('senior/{senior}/withdraw', [SeniorController::class, 'withdraw'])->name('senior.withdraw');
    Route::post('senior/{senior}/withdraw', [SeniorController::class, 'makeWithdraw'])->name('senior.makeWithdraw');
    Route::resource('master', MasterController::class);
    Route::post('master/{master}/ban', [MasterController::class, 'ban'])->name('master.ban');
    Route::get('master/{master}/deposit', [MasterController::class, 'deposit'])->name('master.deposit');
    Route::post('master/{master}/deposit', [MasterController::class, 'makeDeposit'])->name('master.makeDeposit');
    Route::get('master/{master}/withdraw', [MasterController::class, 'withdraw'])->name('master.withdraw');
    Route::post('master/{master}/withdraw', [MasterController::class, 'makeWithdraw'])->name('master.makeWithdraw');
    Route::resource('agent', AgentController::class);
    Route::post('agent/{agent}/ban', [AgentController::class, 'ban'])->name('agent.ban');
    Route::get('agent/{agent}/deposit', [AgentController::class, 'deposit'])->name('agent.deposit');
    Route::post('agent/{agent}/deposit', [AgentController::class, 'makeDeposit'])->name('agent.makeDeposit');
    Route::get('agent/{agent}/withdraw', [AgentController::class, 'withdraw'])->name('agent.withdraw');
    Route::post('agent/{agent}/withdraw', [AgentController::class, 'makeWithdraw'])->name('agent.makeWithdraw');
    Route::resource('player', PlayerController::class);
    Route::post('player/{player}/ban', [PlayerController::class, 'ban'])->name('player.ban');
    Route::get('player/{player}/deposit', [PlayerController::class, 'deposit'])->name('player.deposit');
    Route::post('player/{player}/deposit', [PlayerController::class, 'makeDeposit'])->name('player.makeDeposit');
    Route::get('player/{player}/withdraw', [PlayerController::class, 'withdraw'])->name('player.withdraw');
    Route::post('player/{player}/withdraw', [PlayerController::class, 'makeWithdraw'])->name('player.makeWithdraw');
    Route::get('paymentType', [PaymentTypeController::class, 'index'])->name('paymentType.index');

    Route::group(['prefix' => 'deposit', 'as' => 'deposit.'], function () {
        Route::get('/', [DepositRequestController::class, 'index'])->name('index');
        Route::post('/{deposit}/approve', [DepositRequestController::class, 'approve'])->name('approve');
        Route::post('/{deposit}/reject', [DepositRequestController::class, 'reject'])->name('reject');
    });
    Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.'], function () {
        Route::get('/', [WithdrawRequestController::class, 'index'])->name('index');
        Route::post('/{withdraw}/approve', [WithdrawRequestController::class, 'approve'])->name('approve');
        Route::post('/{withdraw}/reject', [WithdrawRequestController::class, 'reject'])->name('reject');
    });
});
