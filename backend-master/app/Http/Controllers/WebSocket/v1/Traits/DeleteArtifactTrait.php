<?php 

namespace App\Http\Controllers\WebSocket\v1\Traits;

use Carbon\Carbon;
use DB;
use Log;

/**
 *  Обработчик события delete_artifact
 */
trait DeleteArtifactTrait
{
    public function deleteArtifact()
    {
        if($this->noErrors()) {
            if($this->playload->event != 'delete_artifact') {
                return false;
            }
            
            // Собираю идентификаторы всех артефактов, которые нужно удалить:
            $idsToDelete = [$this->playload->data->artifact_id];
            $childrenIDs = $this->findChildrenIDs($this->playload->data->artifact_id);
            
            if($childrenIDs instanceof \Exception) {
                $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $childrenIDs->getMessage()]);
                return false;
            
            }
            
            $idsToDelete = array_merge($idsToDelete, $childrenIDs);
            
            // Удаляю все артефакты:
            if(count($idsToDelete) > 0) {
                foreach($idsToDelete as $tmpID) {
                    
                    $delete = $this->destroyArtifactById($tmpID);
                    
                    if($delete instanceof \Exception) {
                        $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $delete->getMessage()]);
                        return false;
                        
                    } else if($delete) {
                        // Сохраняю результат удаления, чтобы передать остальным фронтендам:
                        $object = new \stdClass();
                        $object->artifact_id = $tmpID;

                        // Добавляю артефакт в очередь на рассылку:
                        $this->addUpdates('bulk_delete', $object);
                        
                    }
                }
            }
            
            $this->connection->send(json_encode([
                'event' => 'delete_artifact_result',
                'seq_num' => $this->playload->seq_num,
                'status_code' => 200,
                'data' => [
                    'artifact_id' => $this->playload->data->artifact_id,
                ],
            ]));
            
            // Добавляю артефакт в очередь на рассылку:
            $this->queueUpdates();
                
        }
    }
}
    