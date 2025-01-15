<?php

namespace Desafio\Transaction\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'payer',
        'payee',
        'amount',
        'status',
        'reason'
    ];
}