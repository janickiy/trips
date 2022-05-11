<?php

use BeyondCode\LaravelWebSockets\Dashboard\Http\Middleware\Authorize;

return [

    'endpoint' => env('WEB_SOCKET_PROTOCOL', 'wss') . '://' . env('WEB_SOCKET_URL', '') . ':' . env('WEB_SOCKET_PORT', '6001'),
    'admin_token' => env('WEB_SOCKET_ADMIN_TOKEN', '70bA4rtFed5Owe1P5PNRTdAnFO15xOKHLj'),

    'dashboard' => [
        'port' => env('WEB_SOCKET_PORT'),
    ],

    'trips_websocket_app' => env('PUSHER_APP_ID'),
     
    'apps' => [
        [
            'id' => env('PUSHER_APP_ID'),
            'name' => env('APP_NAME'),
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'enable_client_messages' => false,
            'enable_statistics' => true,            
        ],
    ],
    
    'app_provider' => BeyondCode\LaravelWebSockets\Apps\ConfigAppProvider::class,

    'allowed_origins' => [],

    'max_request_size_in_kb' => 5000, // The maximum request size in kilobytes that is allowed for an incoming WebSocket request.

    'path' => '1e4f4t7r7sd5sd5f4gs8hgfhgfs7s9se87sd9fdd4z5x5flsjlsjlesisoieuoiusd',

    'middleware' => [
        'web',
        Authorize::class,
    ],

    'statistics' => [
        'model' => \BeyondCode\LaravelWebSockets\Statistics\Models\WebSocketsStatisticsEntry::class,
        'logger' => BeyondCode\LaravelWebSockets\Statistics\Logger\HttpStatisticsLogger::class,
        'interval_in_seconds' => 60,
        'delete_statistics_older_than_days' => 60,
        'perform_dns_lookup' => false,
    ],

    'ssl' => [
        'local_cert' => env('SSL_FULLCHAIN', null),
        'local_pk' => env('SSL_PRIVKEY', null),
        'passphrase' => null,
        'verify_peer' => false,
    ],

    'channel_manager' => \BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManagers\ArrayChannelManager::class,
];
