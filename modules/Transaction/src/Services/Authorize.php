<?php

namespace Desafio\Transaction\Services;

use Illuminate\Support\Facades\Http;

class Authorize
{
    /**
     * @return bool
     */
    public function authorized(): bool
    {
        $response = Http::get(config('external-service.urls.authorize'));
        return $response->successful() 
            ? $response->json('data.authorization', false) 
                : false;
    }
}