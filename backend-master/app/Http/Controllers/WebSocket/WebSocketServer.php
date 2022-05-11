<?php 

namespace App\Http\Controllers\WebSocket;

use BeyondCode\LaravelWebSockets\Apps\App;
use BeyondCode\LaravelWebSockets\Dashboard\DashboardLogger;
use BeyondCode\LaravelWebSockets\Facades\StatisticsLogger;
use BeyondCode\LaravelWebSockets\QueryParameters;
use BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManager;
use BeyondCode\LaravelWebSockets\WebSockets\Exceptions\ConnectionsOverCapacity;
use BeyondCode\LaravelWebSockets\WebSockets\Exceptions\UnknownAppKey;
use BeyondCode\LaravelWebSockets\WebSockets\Exceptions\WebSocketException;
use BeyondCode\LaravelWebSockets\WebSockets\Messages\PusherMessageFactory;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

use App\Http\Controllers\WebSocket\Traits\AppVersionTrait;
use App\Http\Controllers\WebSocket\Traits\ClientsTrait;
use App\Http\Controllers\WebSocket\Traits\AdminTrait;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Log;

use App\Jobs\LogUserDailyActivity;
use App\Statistics\Daily;

class WebSocketServer implements MessageComponentInterface
{
    use AppVersionTrait, ClientsTrait, AdminTrait;
  
    /**
     *  Список клиентов, подключенных к сокету:
     */
    protected $clients = [];

    public function __construct()
    {
        // Log::info('Start connection __construct');
        $this->clients = new \SplObjectStorage;
    }

    /**
     *  Установить ID соединения:
     */
    protected function generateSocketId(ConnectionInterface $connection)
    {
        // Log::info('generateSocketId');
        $socketId = sprintf('%d.%d', random_int(1, 1000000000), random_int(1, 1000000000));
        $connection->socketId = $socketId;
        return $this;
    } 
    
    /**
     *  Обработчик подключения:
     */
    public function onOpen(ConnectionInterface $connection)
    {
        // Log::info('onOpen');
        $connection->app = App::findById(config('websockets.trips_websocket_app'));
        $this->generateSocketId($connection);
        $accessCodeController = new AccessCodeController;
        
        // Временный код доступа:
        $queryRequest = \GuzzleHttp\Psr7\parse_query($connection->httpRequest->getUri()->getQuery());
        
        
        // Log::info('Временный код доступа');
        
        if(isset($queryRequest['code'])) {
            $code = $queryRequest['code'];
        } else {
            
            // admin token:
            if(isset($queryRequest['admin'])) {

                if(strlen($queryRequest['admin']) > 10 and $queryRequest['admin'] == config('websockets.admin_token')) {

                    // Сохраняю соединение:
                    $this->rememberAdminSocketId($connection->socketId);
                    $this->clients->attach($connection);
                    return false;
                }

            }            

            $connection->close();
            return false;
        }
        
        // Log::info('Удаляю устаревшие коды и ищу текущего пользователя');
        
        // Удаляю устаревшие коды и ищу текущего пользователя:
        $user = $accessCodeController->getUserByCode($code);
        
        if($user instanceof \Exception) {
            $this->sendError($connection, 'Server error. Check server logs.');
            $connection->close();
            return false;
            
        }

        // Если пользователь не найден:
        if($user == null) {
            // Дисконнект:
            $connection->close();
            return false;
        }

        // Сохраняю пользователя для дальшейшей обработки
        $this->rememberSocketId($user->created_by_user_id, $connection->socketId);
        
        // Уничтожаю код доступа:
        $accessCodeController->destroyCode($code);
        
        // Отправляю приветствие:
        $connection->send(json_encode([
            'event' => 'welcome',
            "status_code" => 200,
        ]));
        
        // Сохраняю соединение:
        $this->clients->attach($connection);
        
        unset($accessCodeController);
        
        $this->logUserDailyActivity($user->created_by_user_id);
    }

   
    public function onClose(ConnectionInterface $connection)
    {
        $this->forgetSocketId($connection->socketId);
        $this->unSubsctibeSocketToUpdates($connection->socketId);
        
        // Удаляю соединение:
        $this->clients->detach($connection);
    }

    public function onMessage(ConnectionInterface $connection, MessageInterface $message)
    {
        $playload = json_decode($message->getPayload());
        
        if(!is_object($playload)) {
            $connection->send(json_encode([
                "event"          => "error",
                "status_code"    => 400,
                "status_message" => 'Empty playload.',
                'data' => [],
            ]));
            
            return false;
        }
        
        // Проверяю версию фронтенда:
        if(!property_exists($playload, 'app_version')) {
            $connection->send(json_encode([
                "event"          => "error",
                "status_code"    => 400,
                "status_message" => 'Wrong app_version.',
                'data' => [],
            ]));

            Log::warning('Wrong app version!', [
                'file' => __FILE__,
                'line' => __LINE__,
                'user_id' => $this->getUserBySocketId($connection->socketId)
            ]);
            
            return false;
        } else {
            
            if(!$this->appVersionIsAllowed($playload->app_version)) {
                $connection->send(json_encode([
                    "event"          => "error",
                    "status_code"    => 400,
                    "status_message" => 'Wrong app_version.',
                    'data' => [],
                ]));
                
                Log::warning('Wrong app version!', [
                    'file' => __FILE__,
                    'line' => __LINE__,
                    'user_id' => $this->getUserBySocketId($connection->socketId)
                ]);
                
                return false;
            }
            
            // Административные события:
            if(property_exists($playload, 'event')) {
                if($this->isAllowedAdminEvent($playload->event)) {
                    $this->handleAdminEvent($connection, $playload);
                    return false;
                }
            }
            
            // Передаю данные на обработку:
            if($playload->app_version == 1) {

                $response = new \App\Http\Controllers\WebSocket\v1\PlayloadController(
                    $playload, 
                    $connection, 
                    $this->getUserBySocketId($connection->socketId)
                );

                if($response->getUpdates()) {

                    $othersArray = $this->getOtherUserSockets($connection->socketId);
            
                    // Отправляю сообщение остальным фронтендам пользователя:
                    foreach ($this->clients as $client) {
                        if (in_array($client->socketId, $othersArray)) {
                            if($this->isSubsctibedToUpdates($client->socketId)) {
                                $client->send(json_encode($response->getUpdates()));
                                
                            }
                        }
                    }
                }
                
            }
            
            // Подписка и отписка от обновлений:
            if(property_exists($playload, 'event')) {
                if($playload->event == "subscribe_to_updates") {
                    $this->subsctibeSocketToUpdates($connection->socketId);
                } else if($playload->event == "unsubscribe_from_updates") {
                    $this->unSubsctibeSocketToUpdates($connection->socketId);
                    $connection->send(json_encode([
                        "event" => "unsubscribe_result",
                        "status_code" => 200,
                    ]));
                    
                    return false;
                }
            }
            
            return false;
        }      

    }
    
    /**
     *  Обработчик ошибок:
     */
    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        // Отчет об ошибке работает только в режиме разработки:
        if(config('app.debug') == true) {
            
            Log::warning('WebSocket Exception!', [
                'Message:'  => $e->getMessage(), 
                'File:'     => $e->getFile(), 
                'Line:'     => $e->getLine(), 
                'user_id:'  => $this->getUserBySocketId($connection->socketId)
            ]);
            $this->sendError($connection, $e->getMessage()); 
            
        }
    }

    /**
     *  Сообщение об ошибке работает только в режиме разработки:
     */
    public function sendError($connection, $errorMessage)
    {
        $connection->send(json_encode([
            "event"          => "error",
            "status_code"    => 500,
            "status_message" => $errorMessage,
            'data' => [
            ],
        ]));
    }
    
    /**
     *  Логирование активности пользователя. 1 раз в день.
     */
    public function logUserDailyActivity($userId)
    {        
        //Log::info("logUserDailyActivity", [$userId]);
        
        // Беру текущую дату:
        $date = Carbon::now()->format("Y_m_d");
        
        // Узнаю регистрировался ли вход этого пользователя сегодня:
        if(Redis::exists($date . '.' . $userId)) {
            //Log::info("Redis::exists", [true]);
            return false;
        }
        
        //Log::info("Redis::exists", [false]);
        // Если это новый вход за сегодня, кеширую на сутки:
        Redis::set($date . '.' . $userId,  $date, 'EX', 24 * 3600);

        // Сохраняю статистику:
        $record = new Daily;
        $record->date = Carbon::now();
        $record->user_id = $userId;
        $record->save();
        
        //Log::info("record", [$record]);
    }
    
}