<?php

namespace Desafio\Transaction;

class Transaction
{
    protected static $instance;

    protected $transactions = [];

    protected $transactionService = [
        'transfer' => '\\Desafio\\Transaction\\Transactions\\Transfer'
    ];

    /**
     * @return object
     */
    public static function transfer()
    {
        return static::getInstance()->factory(__FUNCTION__);
    }

    /**
     * @return \Desafio\Transaction\Transaction
     */
    protected static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self;
        }
        return static::$instance;
    }

    /**
     * @param string $type
     * 
     * @return object
     */
    protected function factory(string $type)
    {
        if (isset($this->transactions[$type])) {
            return $this->transactions[$type];
        }
        return $this->transactions[$type] = new $this->transactionService[$type];
    }
}