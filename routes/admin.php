<?php

use App\Http\Controllers\Admin\PlayerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Admin\SeniorController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\AgentController;

Route::group([
    'prefix' => 'admin', 'as' => 'admin.',
    'middleware' => ['auth', 'checkBanned']
], function () {

    Route::post('balance-up', [HomeController::class, 'balanceUp'])->name('balanceUp');
    Route::get('logs/{id}', [HomeController::class, 'logs'])
        ->name('logs');

    Route::resource('banners', BannerController::class);
    Route::resource('bannerText', BannerTextController::class);
    Route::resource('senior', SeniorController::class);
    Route::post('senior/{id}/ban', [SeniorController::class, 'ban'])->name('senior.ban');
    Route::resource('master', MasterController::class);
    Route::post('master/{id}/ban', [MasterController::class, 'ban'])->name('master.ban');
    Route::resource('agent', AgentController::class);
    Route::post('agent/{id}/ban', [AgentController::class, 'ban'])->name('agent.ban');
    Route::resource('player', PlayerController::class);
    Route::post('player/{id}/ban', [PlayerController::class, 'ban'])->name('player.ban');
});
