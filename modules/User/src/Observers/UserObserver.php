<?php

namespace Desafio\User\Observers;

use Desafio\User\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        $user->account()->create([
            'amount' => 0,
            'type' => $user->getTypeAccount()
        ]);
    }
}