<?php

namespace Desafio\User;

use Desafio\Transaction\Services\Notify as NotifyService;
use Desafio\Transaction\Traits\ExternalService;
use Desafio\User\Models\User;
use Exception;

class AccountService
{
    use ExternalService;

    public function __construct(
        protected NotifyService $notifyService
    ) { }

    /**
     * @param User $user
     * @param int $amount
     * 
     * @throws Exception
     * 
     * @return void
     */
    public function valideEnoughValue(User $user, int $amount): void
    {
        if ($user->account->mount < convertToCents($amount)) {
            throw new Exception(__('transaction::app.transfer.fail.insufficient_amount'));
        }
    }

    /**
     * @param User $userAccount
     * @param int $amount
     * 
     * @return AccountService
     */
    public function decrement($userAccount, int $amount): AccountService
    {
        $balance = (int) $userAccount->account->amount;

        $userAccount->account->amount = $balance - $amount;
        $userAccount->account->save();

        return $this;
    }

    /**
     * @param User $userAccount
     * @param int $amount
     * 
     * @return AccountService
     */
    public function increment($userAccount, int $amount): AccountService
    {
        $balance = (int) $userAccount->account->amount;

        $userAccount->account->amount = $balance + $amount;
        $userAccount->account->save();

        return $this;
    }
}