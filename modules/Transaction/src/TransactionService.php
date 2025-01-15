<?php

namespace Desafio\Transaction;

use Desafio\Transaction\Services\Authorize as AuthorizeService;
use Desafio\Transaction\Services\Transfer as TransferService;
use Desafio\Transaction\Models\Transaction;
use Desafio\User\AccountService;
use Desafio\User\UserService;

class TransactionService
{
    public function __construct(
        protected AuthorizeService $authorizeService,
        protected AccountService $accountService,
        protected Transaction $transaction,
        protected UserService $userService
    ) { }

    /**
     * @param float $amount
     * @param int $payer
     * @param int $payee
     * 
     * @return TransferService
     */
    public function transfer(float $value, int $payer, int $payee): TransferService
    {
        $transferService = new TransferService(
            authorizeService: $this->authorizeService,
            accountService: $this->accountService,
            transaction: $this->transaction
        );

        $transferService->amount = convertToCents($value);
        $transferService->payer = $this->userService->load($payer);
        $transferService->payee = $this->userService->load($payee);

        return $transferService;
    }
}