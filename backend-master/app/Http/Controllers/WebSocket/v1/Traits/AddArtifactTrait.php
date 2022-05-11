<?php 

namespace App\Http\Controllers\WebSocket\v1\Traits;

use App\Http\Controllers\ArtifactMapper;
use Carbon\Carbon;
use DB;
use Log;

/**
 *  Обработчик события add_artifact
 */
trait AddArtifactTrait
{
    public function addArtifact()
    {
        if($this->noErrors()) {
            if($this->playload->event != 'add_artifact') {
                return false;
            }
            
            try {
            
                // Создаю артефакт:
                $artifactId = DB::table('artifacts')->insertGetId([
                    'parent_artifact_id'        => $this->playload->data->parent_artifact_id,
                    'artifact_type'             => $this->playload->data->artifact_type,
                    'created_by_user_id'        => $this->userId,
                    'last_modified_by_user_id'  => $this->userId,
                    'created_at'                => Carbon::now()->timestamp,
                    'last_modified_at'          => Carbon::now()->timestamp,
                    'version'                   => 1,
                    'order_index'               => $this->playload->data->order_index,
                ]);
                
            } catch(\Exception $e) {
                $this->throwError(500, 'Error while creating artifact.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                return false;
                
            }
            
            // Дефолтные атрибуты:
            $attributes = [
                'artifact_id'        => $artifactId,
                'created_by_user_id' => $this->userId,
            ];

            // Уникальные атрибуты:
            $attributes = array_merge($attributes, $this->matchAttributes($this->playload->data));
            
            if($this->noErrors()) {
                // Сохраняю атрибуты артефакта:
                try {
                    DB::table($this->getAttributesTable($this->playload->data->artifact_type))->insert($attributes);
                    
                } catch(\Exception $e) { 
                    $this->throwError(500, 'Error while save artifact attributes.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                    return false;
                    
                }
            }
            
            if($this->noErrors()) {
                
                try {
                
                    $artifact = DB::table('artifacts')
                        ->select('artifact_id', 'parent_artifact_id', 'artifact_type', 'created_by_user_id', 'created_at', 'last_modified_by_user_id', 'version', 'order_index')
                        ->where('artifact_id', $artifactId)
                        ->first();
                        
                    $artifactAttributes = DB::table($this->getAttributesTable($artifact->artifact_type))
                        ->where('artifact_id', $artifact->artifact_id);

                    foreach($this->getArtifactReadableAttributes($artifact->artifact_type) as $field) {
                        $artifactAttributes->addSelect($field);
                    }
                    
                    try { 
                        $artifact->attributes = $artifactAttributes->first();
                    } catch(\Exception $e) {
                        $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                        return false;
                        
                    }
 
 
                } catch(\Exception $e) { 
                    $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                    return false;
                    
                }
                
                $this->connection->send(json_encode([
                    "event"          => "add_artifact",
                    "seq_num"        => $this->playload->seq_num,
                    "status_code"    => 200,
                    'data'           => ArtifactMapper::cast($artifact),
                ]));
                
                // Добавляю артефакт в очередь на рассылку:
                $this->addUpdates('bulk_create', ArtifactMapper::cast($artifact));
                $this->queueUpdates();
            }
        }
    }
}
    