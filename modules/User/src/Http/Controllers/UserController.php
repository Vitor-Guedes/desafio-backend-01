<?php

namespace Desafio\User\Http\Controllers;

use Desafio\Transaction\TransactionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function transfer(TransactionService $transationService)
    {
        $transationService
            ->transfer(...request()->only(['value', 'payer', 'payee']))
            ->authorize(fn ($transferService) => $transferService->make());
            
        return response()->json([], Response::HTTP_OK);
    }
}