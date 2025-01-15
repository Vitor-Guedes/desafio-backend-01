<?php

namespace Desafio\User;

use Desafio\User\Models\User;
use Exception;

class UserService
{
    public function load(int $id)
    {
        return User::with(['account'])->findOrFail($id);
    }
}