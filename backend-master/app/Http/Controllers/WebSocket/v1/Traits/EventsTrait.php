<?php 

namespace App\Http\Controllers\WebSocket\v1\Traits;

use Log;

/**
 *  События
 */
trait EventsTrait
{
    /**
     *  Разрешенные события:
     */
    private $allowedIncomingEvents = [
        'ping'                      => 'pong',
        'subscribe_to_updates'      => 'updates',
        'add_artifact'              => 'add_artifact_result',
        'edit_artifact'             => 'edit_artifact_result',
        'delete_artifact'           => 'delete_artifact_result',
        'unsubscribe_from_updates'  => 'unsubscribe_result',
    ];
    
    /**
     *  Обязательные поля событий:
     */
    private $requiresBodyProperties = [
        'ping' => [
        ],
        'subscribe_to_updates' => [
            'bulk_create' => [
                /*
                "temp_artifact_id",
                "temp_parent_artifact_id",
                "artifact_type",
                "attributes",
                */
            ],
            'bulk_edit' => [
                /*
                "artifact_id",
                "parent_artifact_id",
                "version",
                "artifact_type",
                "attributes",
                */
            ],
            'bulk_delete' => [
                /*
                "artifact_id"
                */
            ],
        ],
        'add_artifact' => [
            'parent_artifact_id',
            'artifact_type',
            'order_index',
            'attributes',
        ],
        'edit_artifact' => [
            "artifact_id",
            "parent_artifact_id",
            "version",
            "artifact_type",
            'order_index',
            "attributes",
        ],
        'delete_artifact' => [
            "artifact_id"
        ],
        'unsubscribe_from_updates' => [
        ],
        
    ];
    
    /**
     *  Массив разрешенных событий:
     */
    public function getAllowedIncomingEvents()
    {
        return array_keys($this->allowedIncomingEvents);
    }
    
    /**
     *  Ответное событие:
     */
    public function responseEvent($event)
    {
        if (array_key_exists($event, $this->allowedIncomingEvents)) {
            return $this->allowedIncomingEvents[$event];
        }
        
        return 'error';
        
    }
    
    
    /**
     *  Массив обязательных свойств события:
     */
    public function getRequiresBodyProperties($event)
    {
        return $this->requiresBodyProperties[$event];
    }
    
}
    