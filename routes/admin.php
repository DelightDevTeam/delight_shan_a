<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\SeniorController;
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
    Route::post('senior/{senior}/ban', [SeniorController::class, 'ban'])->name('senior.ban');
    Route::resource('master', MasterController::class);
    Route::post('master/{master}/ban', [MasterController::class, 'ban'])->name('master.ban');
    Route::resource('agent', AgentController::class);
    Route::post('agent/{agent}/ban', [AgentController::class, 'ban'])->name('agent.ban');
    Route::resource('player', PlayerController::class);
    Route::post('player/{player}/ban', [PlayerController::class, 'ban'])->name('player.ban');
    Route::get('paymentType', [PaymentTypeController::class, 'index'])->name('paymentType.index');
    Route::get('senior/{senior}/deposit', [SeniorController::class, 'deposit'])->name('senior.deposit');
    Route::post('senior/{senior}/deposit', [SeniorController::class, 'makeDeposit'])->name('senior.makeDeposit');
    Route::get('senior/{senior}/withdraw', [SeniorController::class, 'withdraw'])->name('senior.withdraw');
    Route::post('senior/{senior}/withdraw', [SeniorController::class, 'makeWithdraw'])->name('senior.makeWithdraw');
});
