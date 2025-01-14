<?php

use Desafio\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('login', 'login')->name('api.user.login');
    });
});


Route::controller(UserController::class)->group(function () {
    Route::post('transfer', 'transfer')->name('api.transfer');
});
