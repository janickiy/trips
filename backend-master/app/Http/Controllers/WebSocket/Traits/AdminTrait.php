<?php 

namespace App\Http\Controllers\WebSocket\Traits;

use DB;
use Log;

/**
 *  Административные команды для веб-сокета:
 */
trait AdminTrait
{
    
    /**
     *  Методы:
     */
    private $adminEvents = [
        'admin_test' => 'AdminTest',
        'publish_artifact_update' => 'PublishArtifactUpdate',
        'delete_all_user_artifacts' => 'DeleteAllUserArtifacts',
        'user_was_deleted' => 'UserWasDeleted',
        'get_online_users' => 'GetOnlineUsers',
    ];

    
    /**
     *  Проверить событие:
     */
    public function isAllowedAdminEvent($event)
    {
        if (array_key_exists($event, $this->adminEvents)) {
            return true;
        }

        return false;
    }
    
    /**
     *  Запуск обработчика административных событий:
     */
    public function handleAdminEvent($connection, $playload)
    {
        // Права доступа:
        if($this->isAdminSocketId($connection->socketId)) {
        
            // Обработчик:
            if(!method_exists($this, "adminEvent" . $this->adminEvents[$playload->event])) {
                $connection->send(json_encode([
                    "event"          => "error",
                    "status_code"    => 500,
                    "status_message" => 'Mehtod not exists.',
                    'data' => [$playload->event, "adminEvent" . $this->adminEvents[$playload->event]],
                ]));
                
                return false;
            }
            
            $method = "adminEvent" . $this->adminEvents[$playload->event];
          
            $this->$method($connection, $playload);
        
            return false;
        
        } else {
            $connection->close();
        }
    }
    
    /**
     *  Тест:
     */
    public function adminEventAdminTest($connection, $playload)
    {
        $connection->send(json_encode([
            "event"          => "admin_test",
            "status_code"    => 200,
            'data' => [$playload],
        ]));
    }
    
    /**
     *  Уведомление об обновлении артефакта:
     */
    public function adminEventPublishArtifactUpdate($connection, $playload)
    {
        $attributesTables = [
            1 => "trips_attributes",
            2 => "cities_attributes",
            3 => "files_attributes",
            4 => "notes_attributes",
            5 => "links_attributes",
            6 => "transfers_attributes",
            7 => "bookings_attributes",
            8 => "note_photos_attributes",
            9 => "flights_attributes",
        ];
        
        $readableAttributes = [
                1 => ["name", "description"],
                2 => ["city_id"],
                3 => ["title", "upload_is_complete"],
                4 => ["text", "is_trip_description"],
                5 => ["title", "link"],
                6 => [
                    "category_id", 
                    "departure_at", 
                    "arrival_at", 
                ],
                7 => [
                    "name", 
                    "address", 
                    "latitude", 
                    "longitude"
                ],
                8 => ["title", "upload_is_complete"],
                9 => [
                    "title", 
                    "departure_city_id", 
                    "arrival_city_id", 
                    "departure_iata_code",
                    "arrival_iata_code", 
                    "flight_number", 
                    "carrier", 
                ]
            ];
        
        // Ищу активные подключения пользователя:
        $socketsArray = $this->getSocketsByUserId($playload->data->user_id);
        
        // Log::info("socketsArray", [$socketsArray, $playload->data->user_id]);
        
        // Отправляю сообщение всем фронтендам пользователя:
        if(count($socketsArray) > 0) {
            
            // Ищу артефакт
            try {
            
                $artifact = DB::table('artifacts')
                    ->select('artifact_id', 'parent_artifact_id', 'artifact_type', 'created_by_user_id', 'created_at', 'last_modified_by_user_id', 'version')
                    ->where('artifact_id', $playload->data->artifact_id)
                    ->first();
                    
                $artifactAttributes = DB::table($attributesTables[$artifact->artifact_type])
                    ->where('artifact_id', $artifact->artifact_id);

                foreach($readableAttributes[$artifact->artifact_type] as $field) {
                    $artifactAttributes->addSelect($field);
                }
                
                try { 
                    $artifact->attributes = $artifactAttributes->first();
                } catch(\Exception $e) {

                    Log::error('Error addArtifact.', [
                        'Message:'  => $e->getMessage(), 
                        'File:'     => $e->getFile(), 
                        'Line:'     => $e->getLine(), 
                    ]);

                    $connection->send(json_encode([
                        "event"          => "publish_artifact_update",
                        "seq_num"        => "",
                        "status_code"    => 500,
                        "status_message" => '',
                        'data' => [],
                    ]));
                    
                    return false;
                    
                }

            } catch(\Exception $e) { 

                Log::error('Error addArtifact.', [
                    'Message:'  => $e->getMessage(), 
                    'File:'     => $e->getFile(), 
                    'Line:'     => $e->getLine(), 
                ]);

                $connection->send(json_encode([
                    "event"          => "publish_artifact_update",
                    "seq_num"        => "",
                    "status_code"    => 500,
                    "status_message" => '',
                    'data' => [],
                ]));
        
                return false;
                
            }
            
            foreach ($this->clients as $client) {
                if (in_array($client->socketId, $socketsArray)) {
                    if($this->isSubsctibedToUpdates($client->socketId)) {
                        $client->send(json_encode([
                            'event' => 'updates',
                            'data' => [
                                'bulk_create'   => [],
                                'bulk_edit'     => [$artifact],
                                'bulk_delete'   => [],
                            ],
                        ]));
                        
                    }
                }
            }
            
        }
        
        $connection->send(json_encode([
            "event"          => "publish_artifact_update",
            "status_code"    => 200,
            'data' => [
                "sockets" => $socketsArray,
            ],
        ]));
        
    }

    /**
     *  Удалить все артефакты пользователя:
     */
    public function adminEventDeleteAllUserArtifacts($connection, $playload)
    {
        
        if(!property_exists($playload, 'data')) {
            $connection->send(json_encode([
                "event"          => "delete_all_user_artifacts",
                "status_code"    => 400,
                "status_message" => 'Specify data.',
            ]));
            return false;
        }
        
        if(!is_object($playload->data)) {
            $connection->send(json_encode([
                "event"          => "delete_all_user_artifacts",
                "status_code"    => 400,
                "status_message" => 'Data must be an object.',
            ]));
            return false;
        }
        
        if(!property_exists($playload->data, 'user_id')) {
            $connection->send(json_encode([
                "event"          => "delete_all_user_artifacts",
                "status_code"    => 400,
                "status_message" => 'Specify user_id.',
            ]));
            return false;
        }
        
        $user = DB::table('users')->where('id', $playload->data->user_id)->first();
        
        if($user == null) {
            $connection->send(json_encode([
                "event"          => "delete_all_user_artifacts",
                "status_code"    => 404,
                "status_message" => 'User not found.',
            ]));
            return false;
        }
        
        DB::table('artifacts')->where('created_by_user_id', $user->id)->delete();

        $attributesTables = [
            1 => "trips_attributes",
            2 => "cities_attributes",
            3 => "files_attributes",
            4 => "notes_attributes",
            5 => "links_attributes",
            6 => "tickets_attributes",
            7 => "bookings_attributes",
            8 => "note_photos_attributes",
            9 => "flights_attributes",
        ]; 

        foreach($attributesTables as $id => $table) {
            DB::table($table)->where('created_by_user_id', $user->id)->delete();
        }
        
        // TODO: стереть файлы пользователя
        
        $connection->send(json_encode([
            "event"          => "delete_all_user_artifacts_result",
            "status_code"    => 200,
            'data' => [
                "user_id" => $user->id,
            ],
        ]));
    }
    
    /**
     *  Уведомление о том, что аккаунт был удален:
     */
    public function adminEventUserWasDeleted($connection, $playload)
    {
        $othersArray = $this->getSocketsByUserId($playload->data->user_id);
        
        foreach ($this->clients as $client) {
            if (in_array($client->socketId, $othersArray)) {
                $client->send(json_encode([
                    "event" => "delete_account",
                    'data' => [
                        "user_id" => $playload->data->user_id,
                    ],
                ]));
                
                $client->close();
            }
        }
    }
    
    /**
     *  Уведомление о пользователях онлайн:
     */
    public function adminEventGetOnlineUsers($connection, $playload)
    {
        $connection->send(json_encode([
            "event"          => "get_online_users",
            "status_code"    => 200,
            'data' => $this->getOnlineStatistics(),
        ]));  
    }
    
    
    
    
    
}