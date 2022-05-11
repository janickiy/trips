<?php 

namespace App\Http\Controllers\WebSocket\v1;

use App\Http\Controllers\WebSocket\v1\Traits\PlayloadValidatorTrait;
use App\Http\Controllers\WebSocket\v1\Traits\ErrorTrait;
use App\Http\Controllers\WebSocket\v1\Traits\EventsTrait;
use App\Http\Controllers\WebSocket\v1\Traits\ArtifactsTrait;
use App\Http\Controllers\WebSocket\v1\Traits\AddArtifactTrait;
use App\Http\Controllers\WebSocket\v1\Traits\EditArtifactTrait;
use App\Http\Controllers\WebSocket\v1\Traits\DeleteArtifactTrait;
use App\Http\Controllers\WebSocket\v1\Traits\SubscribeToUpdatesTrait;
use App\Http\Controllers\WebSocket\v1\Traits\PongTrait;

use Carbon\Carbon;
use DB;
use Log;

/**
 *  Обработчик входящих данных фронтендов 
 *  Версия 1. 
 */
class PlayloadController
{
    use PlayloadValidatorTrait, ErrorTrait, EventsTrait, ArtifactsTrait, AddArtifactTrait, EditArtifactTrait, DeleteArtifactTrait, SubscribeToUpdatesTrait, PongTrait;
    
    /**
     *  Текущее соединение:
     */
    private $connection;
    private $userId;
    
    /**
     *  Входящие данные:
     */
    private $playload;
    
    /**
     *  Канал updates, для остальных фронтендов пользователя:
     */
    private $hasUpdates = false;
    private $updates = [
        'event' => 'updates',
        'data' => [
            'bulk_create'   => [],
            'bulk_edit'     => [],
            'bulk_delete'   => [],
        ],
    ];

    /**
     *  Програма:
     */ 
    public function __construct($playload, $connection, $userId)
    {
        // Устанавливаю данные для работы:
        $this->connection = $connection;
        $this->playload = $playload;
        $this->userId = $userId;
        
        // Проверяю входящие данные:
        $this->validateHeaders();
        $this->validateBodyProperties();
        $this->validateBodyValues();
        $this->validateAttributesProperties();
        $this->validateAttributesValues();
        $this->validatePermissions();

        // Если нет ошибок, беру в обработку:
        if($this->noErrors()) {
            $this->pong();
            $this->addArtifact();
            $this->editArtifact();
            $this->deleteArtifact();
            $this->subscribeToupdates();
            
        }

    }
    
    /**
     *  Добавляет данные в рассылку остальным фронтендам пользователя:
     */
    public function addUpdates($key, $obj) 
    {
        $this->updates['data'][$key][] = $obj;
    }
    
    public function getUpdates() 
    {
        $hasUpdates = false;
        
        foreach($this->updates['data'] as $method => $array) {
            if(count($this->updates['data'][$method]) > 0) {
                $hasUpdates = true;
            }
        }
        
        
        if($hasUpdates) {
            return $this->updates;
        } else {
            return false;
        }
    }
    
    public function queueUpdates() 
    {
        $this->hasUpdates = true;
    }
    
}