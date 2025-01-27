<?php

namespace Desafio\User\Providers;

use Desafio\User\Models\User;
use Desafio\User\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        User::observe(UserObserver::class);
    }

    public function register()
    {
        $this->replaceConfigRecursivelyFrom(__DIR__ . '/../Config/auth.php', 'auth');
    }
}