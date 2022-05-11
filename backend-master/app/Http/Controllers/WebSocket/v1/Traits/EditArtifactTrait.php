<?php 

namespace App\Http\Controllers\WebSocket\v1\Traits;

use App\Http\Controllers\ArtifactMapper;
use Carbon\Carbon;
use DB;
use Log;

/**
 *  Обработчик события edit_artifact
 */
trait EditArtifactTrait
{
    public function editArtifact()
    {
        if($this->noErrors()) {
            if($this->playload->event != 'edit_artifact') {
                return false;
            }
            
            try { 
            
                $artifact = DB::table('artifacts')
                    ->select('artifact_id', 'parent_artifact_id', 'artifact_type', 'created_by_user_id', 'created_at', 'last_modified_by_user_id', 'version', 'order_index')
                    ->where('artifact_id', $this->playload->data->artifact_id)
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
            
            // Если фронтенд прислал старую версию артефакта:
            if($this->playload->data->version <= $artifact->version) {
                // Даю ему новую:
                $this->connection->send(json_encode([
                    'event' => 'edit_artifact_result',
                    'seq_num' => $this->playload->seq_num,
                    'status_code' => 200,
                    'data' => $artifact,
                ])); 
                
                return false;
                    
            }
            
            
            // Обновляю артефакт:
            try {
                DB::table('artifacts')->where('artifact_id', $artifact->artifact_id)
                    ->update([
                        'parent_artifact_id' => $this->playload->data->parent_artifact_id,
                        'last_modified_by_user_id' => $this->userId,
                        'last_modified_at' => Carbon::now()->timestamp,
                        'version' => $this->playload->data->version,
                        'order_index' => $this->playload->data->order_index
                    ]);
                
                $newData = [];
                
                foreach($this->getArtifactAttributes($this->playload->data->artifact_type) as $field) {
                    if(property_exists($this->playload->data->attributes, $field)) {
                        $newData[$field] = $this->playload->data->attributes->$field;
                    }
                    
                }

                if(count($newData)) {
                    try {
                        DB::table($this->getAttributesTable($this->playload->data->artifact_type))
                            ->where('artifact_id', $artifact->artifact_id)
                            ->update($newData);
                            
                    } catch(\Exception $e) {
                        $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                        return false;
                        
                    }
                }
                
            } catch(\Exception $e) { 
                $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                return false;
                
            }
            
            // Читаю обновленный артефакт с БД:
            try { 
            
                $artifact = DB::table('artifacts')
                    ->select('artifact_id', 'parent_artifact_id', 'artifact_type', 'created_by_user_id', 'created_at', 'last_modified_by_user_id', 'version', 'order_index')
                    ->where('artifact_id', $this->playload->data->artifact_id)
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
                "event"          => "edit_artifact_result",
                "seq_num"        => $this->playload->seq_num,
                "status_code"    => 200,
                'data' => ArtifactMapper::cast($artifact),
            ]));
            
            // Добавляю артефакт в очередь на рассылку:
            $this->addUpdates('bulk_edit', ArtifactMapper::cast($artifact));
            $this->queueUpdates();
            
        }
    }
}
    