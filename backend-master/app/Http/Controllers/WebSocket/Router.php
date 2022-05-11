<?php 

namespace App\Http\Controllers\WebSocket;

use App\WebSockets\WebSocketHandler;
use BeyondCode\LaravelWebSockets\HttpApi\Controllers\FetchChannelController;
use BeyondCode\LaravelWebSockets\HttpApi\Controllers\FetchChannelsController;
use BeyondCode\LaravelWebSockets\HttpApi\Controllers\FetchUsersController;
use BeyondCode\LaravelWebSockets\HttpApi\Controllers\TriggerEventController;

class Router extends \BeyondCode\LaravelWebSockets\Server\Router
{
    public function echo()
    {
        $this->get('/', \App\Http\Controllers\WebSocket\WebSocketServer::class);
    }
}