<?php

namespace Desafio\Transaction\Services;

use Desafio\Transaction\Services\Authorize as AuthorizeService;
use Desafio\Transaction\Notifications\TransferReceive;
use Desafio\Transaction\Traits\ExternalService;
use Desafio\Transaction\Models\Transaction;
use Desafio\User\AccountService;
use Desafio\User\Models\Account;
use Exception;

class Transfer
{
    use ExternalService;

    /** @var int $amount */
    public $amount;

    /** @var \Desafio\User\Models\User $payer */
    public $payer;

    /** @var \Desafio\User\Models\User $payer */
    public $payee;

    public function __construct(
        protected AuthorizeService $authorizeService,
        protected AccountService $accountService,
        protected Transaction $transaction,
    ) { }

    /**
     * @return Transaction
     */
    public function make(): Transaction
    {
        try {
            $this->validateAccounts();

            $this->accountService->valideEnoughValue($this->payer, $this->amount);

            $this->accountService->decrement($this->payer, $this->amount);
            $this->accountService->increment($this->payee, $this->amount)
                ->notify(fn ($accountService) => 
                    $this->payee->notify(new TransferReceive($this->transaction))
                );

            $this->registerSuccess();
        } catch (Exception $e) {
            $this->registerFail($e);
        }

        return $this->transaction;
    }

    /**
     * @throws Exception
     * 
     * @return void
     */
    public function validateAccounts(): void
    {
        if ($this->payer->account->type != Account::TYPE_NORMAL) {
            throw new Exception(__('transaction::app.transfer.fail.account_type.shopkeeper'));
        }

        if ($this->payer->id === $this->payee->id) {
            throw new Exception(__('transaction::app.transfer.fail.invalid_payee'));
        }
    }

    /**
     * @return void
     */
    public function unauthorized(): void
    {
        $this->register('fail', __('transaction::app.transfer.fail.external_service'));
    }

    /**
     * @param string $status
     * @param string $reason
     * 
     * @return void
     */
    protected function register(string $status = 'success', string $reason = '')
    {
        $this->transaction->fill([
            'type' => 'transfer',
            'status' => $status,
            'amount' => $this->amount,
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'reason' => $reason
        ]);
        $this->transaction->save();
    }

    /**
     * @return void
     */
    public function registerSuccess(): void
    {
        $this->register('success', __('transaction::app.transfer.success'));
    }

    /**
     * @param Exception $e
     * 
     * @return void
     */
    protected function registerFail(Exception $e): void
    {
        $this->register('fail', $e->getMessage());
    }
}