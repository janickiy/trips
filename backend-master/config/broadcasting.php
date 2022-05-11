<?php

return [

    'default' => env('BROADCAST_DRIVER', 'null'),

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
               // 'encrypted' => false,
                //'encrypted' => true,
                //'host' => env('WEB_SOCKET_URL'),
                'host' => env('WEB_SOCKET_URL'),
                'port' => env('WEB_SOCKET_PORT'),
                'scheme' => env('WEB_SOCKET_SHEME'),                
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ] 
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],
  
];
