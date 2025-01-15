<?php

namespace Desafio\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Desafio\Transaction\Transaction;
use Illuminate\Http\Response;
use Exception;

class UserController extends Controller
{
    public function login()
    {
        $credentials = request()->only(['email', 'password']);

        if (auth('user_account')->check()) {
            return response()->json([], Response::HTTP_ALREADY_REPORTED);
        }

        if (auth('user_account')->attempt($credentials)) {
            return response()->json([], Response::HTTP_OK);
        }

        return response()->json([], Response::HTTP_UNAUTHORIZED);
    }

    public function transfer()
    {
        try {
            $payer = request()->input('payer');
            $payee = request()->input('payee');
            $amount = request()->input('value');
            
            $transation = Transaction::transfer()->make($amount, $payer, $payee);

            return response()->json([
                'success' => $transation->successful(),
                'message' => $transation->message(),
                'transation' => $transation->toArray()
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}