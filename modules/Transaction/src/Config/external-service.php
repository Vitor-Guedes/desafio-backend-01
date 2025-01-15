<?php

return [
    'urls' => [
        'authorize' => env('EXTERNAL_AUTORIZE', 'https://util.devi.tools/api/v2/authorize'),
        'notify' => env('EXTERNAL_NOTIFY', 'https://util.devi.tools/api/v1/notify')
    ]
];