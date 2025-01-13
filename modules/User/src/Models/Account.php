<?php

namespace Desafio\User\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'user_accounts';

    const TYPE_NORMAL = 1;

    const TYPE_SHOPKEEPER = 2;

    protected $fillable = [
        'amount',
        'type'
    ];

    public $timestamps = false;
}