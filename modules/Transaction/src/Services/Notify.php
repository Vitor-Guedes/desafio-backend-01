<?php

namespace Desafio\Transaction\Services;

use Illuminate\Support\Facades\Http;

class Notify
{
    /**
     * @return bool
     */
    public function canNotify(): bool
    {
        $response = Http::post(config('external-service.urls.notify'));
        return $response->successful() 
            ? $response->json('data.authorization', false) 
                : false;
    }
}