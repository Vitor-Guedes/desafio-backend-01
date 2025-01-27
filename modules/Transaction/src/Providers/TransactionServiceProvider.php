<?php

namespace Desafio\Transaction\Providers;

use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/external-service.php', 'external-service');
    }
}