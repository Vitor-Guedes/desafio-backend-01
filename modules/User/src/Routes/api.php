<?php

use Desafio\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::post('transfer', 'transfer')->name('api.transfer');
});

Route::prefix('tests')->group(function () {
    Route::get('/api/v2/authorize', function () {
        return response([
            'status' => 'success',
            'data' => [
                'authorization' => true
            ]
        ], 200);
    })->name('test.extenal-service.authorize');
});