<?php

return [


    'default' => env('TWO_FACTOR_AUTH_DRIVER', 'null'),

    'providers' => [

        'messagebird' => [
            'driver' => 'messagebird',
            'key' => env('MESSAGEBIRD_ACCESS_KEY'),
            'options' => [
                'originator' => 'Me',
                'timeout' => 60,
                'language' => 'nl-nl',
            ],
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

    'enabled' => 'user',

    'routes' => [

        'get' => [
            'url' => '/auth/token',
            'name' => 'auth.token',
        ],

        'post' => '/auth/token',

    ],

    'model' => \App\Models\User::class,

];
