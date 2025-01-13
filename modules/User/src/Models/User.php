<?php

namespace Desafio\User\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    protected $fillable = [
        'name',
        'cpf_cnpj',
        'email',
        'password'
    ];

    public $timestamps = false;

    public function account(): HasOne
    {
        return $this->hasOne(Account::class);
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => Hash::make($value)
        );
    }

    protected function cpfCnpj(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => $this->justNumbers($value)
        );
    }

    /**
     * Remove all characters that are not numbers
     * 
     * @param string $value
     * 
     * @return string
     */
    public function justNumbers(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Identifies the type of document passed 1 = cnpj, 0 = cpf
     * 
     * @return int
     */
    public function getTypeAccount(): int
    {
        return strlen($this->cpf_cnpj) == 11 
            ? Account::TYPE_NORMAL 
                : Account::TYPE_SHOPKEEPER;
    }
}