<?php

use Desafio\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::post('transfer', 'transfer')->name('api.transfer');
});
