<?php

return [

    'error_botification' => env('TELEGRAM_ERROR_NOTIFICATION', 1),
    'bot_token' => env('TELEGRAM_TOKEN', ''),   
    'chat_id' => env('TELEGRAM_NOTIFICATION_CHAT_ID', ''), 
 
];
