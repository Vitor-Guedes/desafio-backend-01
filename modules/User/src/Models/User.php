<?php

namespace Desafio\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'cpf_cnpj',
        'email',
        'password'
    ];

    public $timestamps = false;

    protected $hidden = [
        'password'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account(): HasOne
    {
        return $this->hasOne(Account::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function cpfCnpj(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => $this->justNumbers($value)
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => Hash::make($value)
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