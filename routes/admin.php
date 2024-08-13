<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Admin\SeniorController;

Route::group([
    'prefix' => 'admin', 'as' => 'admin.',
    'middleware' => ['auth', 'checkBanned']
], function () {

    Route::post('balance-up', [HomeController::class, 'balanceUp'])->name('balanceUp');
    Route::get('logs/{id}', [HomeController::class, 'logs'])
        ->name('logs');
       // Players
    
    Route::resource('banners', BannerController::class);
    Route::resource('bannerText', BannerTextController::class);
    Route::resource('senior', SeniorController::class);
    Route::post('senior/{id}/ban', [SeniorController::class, 'ban'])->name('senior.ban');
});
