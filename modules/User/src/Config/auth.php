<?php

return [
    'guards' => [
        'user_account' => [
            'driver'   => 'session',
            'provider' => 'user_accounts',
        ]
    ],

    'providers' => [
        'user_accounts' => [
            'driver' => 'eloquent',
            'model' => Desafio\User\Models\User::class
        ]
    ]
];