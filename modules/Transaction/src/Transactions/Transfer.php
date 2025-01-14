<?php

namespace Desafio\Transaction\Transactions;

use Desafio\User\Models\User;
use Desafio\User\Models\Account;
use Illuminate\Support\Facades\Http;
use Desafio\Transaction\Models\Transaction;

class Transfer
{
    protected $payer;

    protected $payee;

    protected $message;

    protected $transaction;

    protected $externalService = 'https://util.devi.tools/api/v2/authorize';

    /**
     * @param float $value
     * @param int $from
     * @param int $to
     * 
     * @return Transfer
     */
    public function make(float $value, int $from, int $to)
    {
        $this->payer = User::findOrFail($from);
        $this->payee = User::findOrFail($to);

        if (! $this->hasAmount($value)) {
            $this->register([
                'amount' => $this->convertToCent($value),
                'status' => 'fail'
            ]);
            $this->message = __('transaction::app.transfer.fail.insufficient_amount');
            return $this;
        }

        if (! $this->typeAccountCanTransfer()) {
            $this->register([
                'amount' => $this->convertToCent($value),
                'status' => 'fail'
            ]);
            $this->message = __('transaction::app.transfer.fail.account_type.shopkeeper');
            return $this;
        }

        if (! $this->canTransfer()) {
            $this->register([
                'amount' => $this->convertToCent($value),
                'status' => 'fail'
            ]);
            $this->message = __('transaction::app.transfer.fail.external_service');
            return $this;
        }

        $this->decrement($this->payer, $value);
        $this->increment($this->payee, $value);

        $this->register([
            'amount' => $this->convertToCent($value),
            'status' => 'success'
        ]);
        $this->message = __('transaction::app.transfer.success');
        return $this;
    }

    /**
     * @param float $amount
     * 
     * @return bool
     */
    protected function hasAmount(float $amount): bool
    {
        return $this->payer->account->amount >= $this->convertToCent($amount);
    }

    /**
     * @return bool
     */
    protected function typeAccountCanTransfer(): bool
    {
        return $this->payer->account->type == Account::TYPE_NORMAL;
    }

    /**
     * @return bool
     */
    public function canTransfer(): bool
    {
        $response = Http::get($this->externalService);
        return $response->successful() ? $response->json('data.authorization', false) : false;
    }

    /**
     * @param aray $attributes
     * 
     * @return void
     */
    protected function register(array $attributes = []): void
    {
        $data = array_merge([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'type' => 'transfer'
        ], $attributes);
        $this->transaction = Transaction::create($data);
    }

    /**
     * @param User $payer
     * @param float $amount
     * 
     * @return void
     */
    protected function decrement(User $payer, float $amount): void
    {
        $balance = (int) $payer->account->amount;
        $_amount = (int) $this->convertToCent($amount);

        $payer->account->amount = $balance - $_amount;
        $payer->account->save();
    }

    /**
     * @param User $payee
     * @param float $amount
     * 
     * @return void
     */
    protected function increment(User $payee, float $amount): void
    {
        $balance = (int) $payee->account->amount;
        $_amount = (int) $this->convertToCent($amount);

        $payee->account->amount = $balance + $_amount;
        $payee->account->save();
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function successful(): bool
    {
        return $this->transaction->status === 'success';
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->transaction->toArray();
    }

    /**
     * @param $amount
     * 
     * @return int
     */
    protected function convertToCent(float $amount): int
    {
        return (int) ($amount * 100);
    }
}