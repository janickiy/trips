<?php 

namespace App\Http\Controllers\WebSocket\v1\Traits;

use App\Http\Controllers\ArtifactMapper;
use Carbon\Carbon;
use DB;
use Log;

/**
 *  Обработчик события subscribe_to_updates
 */
trait SubscribeToUpdatesTrait
{
    public function validateSubscribeToUpdatesBodyProperties()
    {
        if($this->noErrors()) {
            if($this->playload->event != 'subscribe_to_updates') {
                return false;
            }

            if(!is_object($this->playload->data)) {
                $this->throwError(400, 'Data property must be an object.', __FILE__, __LINE__, []);
                return false;
            }
        
            foreach($this->getRequiresBodyProperties($this->playload->event) as $requiredProperty => $subProperties) {
                if($this->noErrors()) {
                    
                    if(!property_exists($this->playload->data, $requiredProperty)) {
                        $this->throwError(400, $requiredProperty . ' not specified.', __FILE__, __LINE__, []);
                        return false;
                        
                    } else {
                        
                        // Массив объектов:
                        if(!is_array($this->playload->data->$requiredProperty)) {
                            $this->throwError(400, $requiredProperty . ' must be an array.', __FILE__, __LINE__, []);
                            return false;
                            
                        } else {
                            
                            // Проверяю свойства каждого объекта:
                            if(count($this->playload->data->$requiredProperty) > 0) {
                                foreach($this->playload->data->$requiredProperty as $obj) {
                                    if($this->noErrors()) {
                                        
                                        if(!is_object($obj)) {
                                            $this->throwError(400, $requiredProperty . ' must contains an array of objects.', __FILE__, __LINE__, []);
                                            return false;
                                            
                                        } else {
                                            if($requiredProperty == 'bulk_create') {
                                                $this->validateBulkCreateObj($obj);
                                            } else if($requiredProperty == 'bulk_edit') {
                                                $this->validateBulkEditObj($obj);
                                            } else if($requiredProperty == 'bulk_delete') {
                                                $this->validateBulkDeleteObj($obj);
                                            }
                                            
                                        }
                                        
                                    }
                                    
                                }
                                
                            }
                            
                        }
                        
                    }

                }
                
            }
            
        }
        
    }
    

    public function validateBulkCreateObj($obj) 
    {
        // Временный идентификатор должны быть всегда:
        if(!property_exists($obj, 'temp_artifact_id')) {
            $this->throwError(400, 'temp_artifact_id not specified.', __FILE__, __LINE__, []);
            return false;

        } else {
            
            // Если не указан временный родительский артефакт:
            if(!property_exists($obj, 'temp_parent_artifact_id')) {
                
                // То должен быть указан существующий родительский артефакт:
                if(!property_exists($obj, 'parent_artifact_id')) {
                    $this->throwError(400, 'temp_parent_artifact_id (parent_artifact_id) not specified.', __FILE__, __LINE__, []);
                    return false;
                    
                }
            }
            
            // Должен быть указан тип артефакта:
            if(!property_exists($obj, 'artifact_type')) {                
                $this->throwError(400, 'artifact_type not specified.', __FILE__, __LINE__, []);
                return false;
                
            }
            
            if(!property_exists($obj, 'attributes')) {
                $this->throwError(400, 'attributes not specified.', __FILE__, __LINE__, []);
                return false;
                
            } else {
                if(!is_object($obj->attributes)) {
                    $this->throwError(400, 'Attributes property must be an object.', __FILE__, __LINE__, []);
                    return false;
                    
                }
            }
            
            if(!property_exists($obj, 'order_index')) {                
                $this->throwError(400, 'order_index not specified.', __FILE__, __LINE__, []);
                return false;
                
            } else {
                
                if(!$this->validateDataProperty_order_index($obj->order_index)) {
                    $this->throwError(400, 'Wrong order_index.', __FILE__, __LINE__, []);
                    return false; 
                }
            }

        }
    }

    
    /**
     *  Валидация обязательных полей для редактирования артефакта:
     */
    public function validateBulkEditObj($obj) 
    {
        foreach($this->getRequiresBodyProperties('edit_artifact') as $requiredProperty) {
            if($this->noErrors()) {
                
                if($requiredProperty == 'attributes') {
                    if(!is_object($obj->attributes)) {
                        $this->throwError(400, 'Attributes property must be an object.', __FILE__, __LINE__, []);
                        return false;
                        
                    }
                } else {
                    // При редактировании в оффлайне может существовать temp_parent_artifact_id:
                    if('parent_artifact_id' == $requiredProperty) {
                        if(!property_exists($obj, $requiredProperty)) {
                            if(!property_exists($obj, 'temp_parent_artifact_id')) {
                                $this->throwError(400, $requiredProperty . ' not specified.', __FILE__, __LINE__, []);
                                return false;
                            }
                        } else {
                            if(!property_exists($obj, $requiredProperty)) {
                                $this->throwError(400, $requiredProperty . ' not specified.', __FILE__, __LINE__, []);
                                return false;
                                
                            } 
                        }

                    } else {
                        if(!property_exists($obj, $requiredProperty)) {
                            $this->throwError(400, $requiredProperty . ' not specified.', __FILE__, __LINE__, []);
                            return false;
                            
                        }
                    }
                }
                    
                if(!property_exists($obj, 'order_index')) {                
                    $this->throwError(400, 'order_index not specified.', __FILE__, __LINE__, []);
                    return false;
                    
                } else {
                    
                    if(!$this->validateDataProperty_order_index($obj->order_index)) {
                        $this->throwError(400, 'Wrong order_index.', __FILE__, __LINE__, []);
                        return false; 
                    }
                }
                
                
            }
            
        }

    }

    /**
     *  Валидация обязательных полей для удаления артефакта:
     */
    public function validateBulkDeleteObj($obj) 
    {
        if(!property_exists($obj, 'artifact_id')) {
            $this->throwError(400, 'Artifact_id not specified.', __FILE__, __LINE__, []);
            return false;
            
        }

    }
    
    /**
     *  Валидация значений обязательных полей:
     */
    public function validateSubscribeToUpdatesBodyValues() 
    {      
        
        if($this->noErrors()) {
            
            if(count($this->playload->data->bulk_create) > 0) {
                foreach($this->playload->data->bulk_create as $obj) {
                    
                    // Проверяю есть ли родители для всех временных артефактов:
                    if(property_exists($obj, 'temp_parent_artifact_id')) {
                        
                        if(!$this->validateDataProperty_temp_parent_artifact_id($obj->temp_parent_artifact_id)) {
                            $this->throwError(400, 'Wtong temp_parent_artifact_id.', __FILE__, __LINE__, ['artifact' => $obj]);
                            return false;  
                        }

                        // Проверяю есть ли родители для всех временных артефактов:
                        if($this->isTempOrphan($obj->temp_parent_artifact_id) or $obj->temp_parent_artifact_id == null) {                            
                            $this->throwError(400, 'temp_parent_artifact_id not found.', __FILE__, __LINE__, ['artifact' => $obj]);
                            return false;
                            
                        }
                    }

                    if(property_exists($obj, 'temp_artifact_id')) {
                        if(!$this->validateDataProperty_temp_artifact_id($obj->temp_artifact_id)) {
                            $this->throwError(400, 'Wtong temp_artifact_id.', __FILE__, __LINE__, ['artifact' => $obj]);
                            return false;  
                        }
                    }
                
                    

                    if($this->noErrors()) {
                        
                        if(!is_object($obj->attributes)) {
                            $this->throwError(400, 'Attributes property must be an object.', __FILE__, __LINE__, ['artifact' => $obj]);
                            return false;
                            
                        }
                        
                        if(!$this->validateDataProperty_artifact_type($obj->artifact_type)) {
                            $this->throwError(400, 'Wrong artifact_type value.', __FILE__, __LINE__, ['artifact' => $obj]);
                            return false;
                            
                        }
                        
                        // Родительский артефакт может быть null:
                        if(property_exists($obj, 'parent_artifact_id')) {
                            if(!$this->isBigUIntOrNull($obj->parent_artifact_id)) {                                
                                $this->throwError(400, 'Wrong parent_artifact_id value.', __FILE__, __LINE__, ['artifact' => $obj]);
                                return false; 
                                
                            }
                        }
                        
                    }
                }
            }
            
            if(count($this->playload->data->bulk_edit) > 0) {
                foreach($this->playload->data->bulk_edit as $obj) {
                    
                    if($this->noErrors()) {
                        
                        foreach($this->getRequiresBodyProperties('edit_artifact') as $requiredProperty) {
                            
                            if($this->noErrors()) {
                                if($requiredProperty == 'attributes') {
                                    if(!is_object($obj->attributes)) {
                                        $this->throwError(400, 'Attributes property must be an object.', __FILE__, __LINE__, ['artifact' => $obj]);
                                        return false;
                                        
                                    }
                                    
                                } else {
                                    
                                    $method = 'validateDataProperty_' . $requiredProperty;

                                    if(!method_exists($this, $method)) {
                                        Log::warning('Playload validator method '.$method.' not specified.', [
                                            'file' => __FILE__,
                                            'line' => __LINE__,
                                            'user_id' => $this->userId
                                        ]);
                                        
                                    } else {
                                                            
                                        // При редактировании в оффлайне может существовать temp_parent_artifact_id:
                                        if('parent_artifact_id' == $requiredProperty) {
                                            if(!property_exists($obj, $requiredProperty)) {
                                                if(!$this->validateDataProperty_temp_parent_artifact_id($obj->temp_parent_artifact_id)) {
                                                    $this->throwError(400, 'Wrong temp_parent_artifact_id value.', __FILE__, __LINE__, ['artifact' => $obj]);
                                                    return false; 
                                                    
                                                }
                                            } else {
                                                if(!$this->$method($obj->$requiredProperty)) {
                                                    $this->throwError(400, 'Wrong ' . $requiredProperty . ' value.', __FILE__, __LINE__, ['artifact' => $obj]);
                                                    return false; 
                                                    
                                                }
                                            }

                                        } else {
                                            if(!$this->$method($obj->$requiredProperty)) {
                                                $this->throwError(400, 'Wrong ' . $requiredProperty . ' value.', __FILE__, __LINE__, ['artifact' => $obj]);
                                                return false; 
                                                
                                            }
                                        }
                                        
                                        

                                    }
                                }
                            }
                        }  
                    }
                }
            }
            
            if(count($this->playload->data->bulk_delete) > 0) {
                foreach($this->playload->data->bulk_delete as $obj) {
                    if($this->noErrors()) {
                        if(!$this->isBigUInt($obj->artifact_id)) {
                            $this->throwError(400, 'Wrong artifact_id value.', __FILE__, __LINE__, ['artifact' => $obj]);
                            return false; 
                            
                        }
                    }
                }
            }
        }
        
        if($this->noErrors()) {
            // Фикс от вечной рекурсии:
            $this->isTempIdsUnique(); 
        } 
    }
    
    /**
     *  Валидация обязательных атрибутов:
     */
    public function validateSubscribeToUpdatesBodyAttributesProperties()
    {
        if($this->noErrors()) {
            if(count($this->playload->data->bulk_create) > 0) {
                foreach($this->playload->data->bulk_create as $obj) {
                    
                    // Проверяю указаны ли все обязательные атрибуты артефакта:
                    foreach($this->getArtifactRequiredAttributes($obj->artifact_type) as $requiredAttribute) {
                        
                        if($this->noErrors()) {
                            if(!property_exists($obj->attributes, $requiredAttribute)) {
                                $this->throwError(400, 'Attribute '. $requiredAttribute . ' not specified', __FILE__, __LINE__, ['artifact' => $obj]);
                                return false;
                                
                            }
                        }
                    } 
                }
            }
            
            if(count($this->playload->data->bulk_edit) > 0) {
                foreach($this->playload->data->bulk_edit as $obj) {
                    
                    // Проверяю указаны ли все обязательные атрибуты артефакта:
                    foreach($this->getArtifactRequiredAttributes($obj->artifact_type) as $requiredAttribute) {
                        
                        if($this->noErrors()) {
                            if(!property_exists($obj->attributes, $requiredAttribute)) {
                                $this->throwError(400, 'Attribute '. $requiredAttribute . ' not specified', __FILE__, __LINE__, ['artifact' => $obj]);
                                return false;
                                
                            }
                        }
                    }
                }
            }
        }
    }
    
    /**
     *  Валидация значений обязательных атрибутов:
     */
    public function validateSubscribeToUpdatesBodyAttributesValues()
    {
        if($this->noErrors()) {
            if(count($this->playload->data->bulk_create) > 0) {
                foreach($this->playload->data->bulk_create as $obj) {
                    
                    if($this->noErrors()) {
                        
                        // Беру из входящих данных только те атрибуты и их значения, которые разрешены в текущей версии приложения:
                        $attributes = $this->matchAttributes($obj);

                        foreach($attributes as $attribute => $value) {
                            if($this->noErrors()) {
                                $method = 'validateAttribute_' . $attribute;

                                if(!method_exists($this, $method)) {
                                    Log::warning('Playload validator method '.$method.' not specified.', [
                                        'file' => __FILE__,
                                        'line' => __LINE__,
                                        'user_id' => $this->userId
                                    ]);
                                    
                                } else {
                                    
                                    // Валидация значения атрибута:
                                    if(!$this->$method($value)) {
                                        $this->throwError(400, 'Wrong ' . $attribute . ' value.', __FILE__, __LINE__, ['artifact' => $obj]);
                                        return false;
                                        
                                    }
                                }
                            }
                        }
                    } 
                }
            }
            
            if(count($this->playload->data->bulk_edit) > 0) {
                foreach($this->playload->data->bulk_edit as $obj) {
                    
                    if($this->noErrors()) {
                        
                         // Беру из входящих данных только те атрибуты и их значения, которые разрешены в текущей версии приложения:
                        $attributes = $this->matchAttributes($obj);

                        foreach($attributes as $attribute => $value) {
                            if($this->noErrors()) {
                                
                                $method = 'validateAttribute_' . $attribute;

                                if(!method_exists($this, $method)) {
                                    Log::warning('Playload validator method '.$method.' not specified.', [
                                        'file' => __FILE__,
                                        'line' => __LINE__,
                                        'user_id' => $this->userId
                                    ]);
                                    
                                } else {
                                    if(!$this->$method($value)) {
                                        $this->throwError(400, 'Wrong ' . $attribute . ' value.', __FILE__, __LINE__, ['artifact' => $obj]);
                                        return false;
                                        
                                    }
                                }
                            }
                        }
                    }  
                }
            }
        }

    }
    
    /**
     *  Валидация прав доступа к артефактам:
     */
    public function validateSubscribeToUpdatesPermissions()
    {
        $artifactIDs = [];
        
        if($this->noErrors()) {
            
            // Собираю все идентификаторы артефактов, к которым запрашивают доступ:
            if(count($this->playload->data->bulk_create) > 0) {
                foreach($this->playload->data->bulk_create as $obj) {
                    if(property_exists($obj, 'artifact_id')) {
                        if($obj->artifact_id > 0) {
                            if(!in_array($obj->artifact_id, $artifactIDs)) {
                                $artifactIDs[] = $obj->artifact_id;
                            }
                        }
                    }

                    if(property_exists($obj, 'parent_artifact_id')) {
                        
                        // Родительский артефакт может быть null
                        if($obj->parent_artifact_id != null) {
                            if(!in_array($obj->parent_artifact_id, $artifactIDs)) {
                                $artifactIDs[] = $obj->parent_artifact_id;
                            }
                        }
                    }
                    
                }
            }
          
            // Собираю все идентификаторы артефактов, к которым запрашивают доступ:
            if(count($this->playload->data->bulk_edit) > 0) {
                foreach($this->playload->data->bulk_edit as $obj) {
                    
                    if($obj->artifact_id > 0) {
                        if(!in_array($obj->artifact_id, $artifactIDs)) {
                            $artifactIDs[] = $obj->artifact_id;
                        }
                    }
                    
                    if(property_exists($obj, 'parent_artifact_id')) {
                        if($obj->parent_artifact_id != null) {
                            if(!in_array($obj->parent_artifact_id, $artifactIDs)) {
                                $artifactIDs[] = $obj->parent_artifact_id;
                            }
                        }
                    }
                    
                }
            }
            
            $maxID = DB::select("SHOW TABLE STATUS LIKE 'artifacts'")[0]->Auto_increment - 1;
            
            foreach($artifactIDs as $artifactID) {
                if($this->noErrors()) {
                
                    try {
                        $artifact = DB::table('artifacts')
                            ->where('artifact_id', $artifactID)
                            ->first();
                        
                        if($artifact != null) {
                            
                            if($artifact->created_by_user_id != $this->userId) {
                                $this->throwError(403, 'Forbidden', __FILE__, __LINE__, ["artifact" => $artifact]);
                                return false;
                                
                            }
                            
                        } else {
                            
                            if($artifactID > $maxID) {
                                $this->throwError(404, 'Artifact_id not found.', __FILE__, __LINE__, []);                                
                                return false;
                                
                            }
                        }
                        
                    } catch(\Exception $e) { 
                        $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message", $e->getMessage()]);
                        return false;
                        
                    }
                }
            }
            
            
            $this->validateBulkDeletePermissions();


        }
    }
    
    /**
     *  Валидация прав доступа к артефактам:
     */
    public function validateBulkDeletePermissions()
    {
        $artifactIDs = [];
        
        if($this->noErrors()) {
            
            if(count($this->playload->data->bulk_delete) > 0) {
                foreach($this->playload->data->bulk_delete as $obj) {
                    if(!in_array($obj->artifact_id, $artifactIDs)) {
                        $artifactIDs[] = $obj->artifact_id;
                    }
                }
            }
            
            foreach($artifactIDs as $artifactID) {
                if($this->noErrors()) {
                
                    try {
                        $artifact = DB::table('artifacts')
                            ->where('artifact_id', $artifactID)
                            ->first();
                        
                        if($artifact != null) {
                            
                            if($artifact->created_by_user_id != $this->userId) {
                                $this->throwError(403, 'Forbidden', __FILE__, __LINE__, ["artifact" => $artifact]);
                                return false;
                                
                            }
                            
                        }
                        
                    } catch(\Exception $e) { 
                        $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message", $e->getMessage()]);
                        return false;
                        
                    }
                }
            } 
        }
    }

    /**
     *  Выполняю запрос:
     */
    public function subscribeToupdates()
    {
        if($this->noErrors()) {
            if($this->playload->event != 'subscribe_to_updates') {
                return false;
            }
            
            // Беру все артефакты пользователя вместе с атрибутами:
            $this->getAllUserArtifacts();

            
            // Удаляю артефакты:
            if($this->noErrors()) {
                if(count($this->playload->data->bulk_delete) > 0) {
                    $this->queueUpdates();
                    
                    // Собираю идентификаторы, которые нужно удалить:
                    $idsToDelete = [];
                    
                    foreach($this->playload->data->bulk_delete as $obj) {
                        
                        $idsToDelete[] = $obj->artifact_id;

                        $childrenIDs = $this->collectChildrenIds($obj->artifact_id);
                        $idsToDelete = array_merge($idsToDelete, $childrenIDs);
                        
                        // Удаляю все артефакты:
                        if(count($idsToDelete) > 0) {
                            foreach($idsToDelete as $tmpID) {
                                        
                                // Если артефакт еще существует:
                                if(isset($this->userArtifacts[$tmpID])) {
                                    // Удаляю:
                                    unset($this->userArtifacts[$tmpID]);
                                }
                                
                                $delete = $this->destroyArtifactById($tmpID);
                                
                                if($delete instanceof \Exception) {
                                    $this->throwError(500, 'delete_artifact error.', __FILE__, __LINE__, ["status_message", $delete->getMessage()]);
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
                    }
                }
            }
            
            // Создаю новые артефакты, если они еще не созданы:
            if($this->noErrors()) {
                if(count($this->playload->data->bulk_create) > 0) {
                    $this->queueUpdates();
                                        
                    // Создаю:
                    $this->createOfflineArtifacts();
                }
            }
            
            // Обновляю артефакты, если нужно:
            if($this->noErrors()) {
                if(count($this->playload->data->bulk_edit) > 0) {
                    $this->queueUpdates();
                    foreach($this->playload->data->bulk_edit as $obj) {
                        
                        // Если родитель уже существует:
                        if(property_exists($obj, 'parent_artifact_id')) {
                            
                            // Если артефакт еще существует:
                            if(isset($this->userArtifacts[$obj->artifact_id])) {
                                
                                // Если версия входящего артефакта выше:
                                if($obj->version > $this->userArtifacts[$obj->artifact_id]->version) {

                                    try {
                                        DB::table('artifacts')->where('artifact_id', $obj->artifact_id)
                                            ->update([
                                                'parent_artifact_id' => $obj->parent_artifact_id,
                                                'last_modified_by_user_id' => $this->userId,
                                                'last_modified_at' => Carbon::now()->timestamp,
                                                'version' => $obj->version,
                                                'order_index' => $obj->order_index,
                                            ]);
                                            
                                    } catch(\Exception $e) { 
                                        $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message", $e->getMessage()]);
                                    } 
                                    
                                    $newData = [];
                                    
                                    foreach($this->getArtifactAttributes($obj->artifact_type) as $field) {
                                        if(property_exists($obj->attributes, $field)) {
                                            $newData[$field] = $obj->attributes->$field;
                                        }
                                        
                                    }
                                                        
                                    if(count($newData)) {
                                        try {
                                            DB::table($this->getAttributesTable($obj->artifact_type))
                                                ->where('artifact_id', $obj->artifact_id)
                                                ->update($newData);
                                        } catch(\Exception $e) { 
                                            $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message", $e->getMessage()]);
                                        }
                                    }
                                    
                                    try {
                                        
                                        $artifact = DB::table('artifacts')
                                            ->select('artifact_id', 'parent_artifact_id', 'artifact_type', 'created_by_user_id', 'created_at', 'last_modified_by_user_id', 'version')
                                            ->where('artifact_id', $obj->artifact_id)
                                            ->first();
                                        
                                    } catch(\Exception $e) { 
                                        $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message", $e->getMessage()]);
                                    }
                                       
                                    try {
                                        $artifactAttributes = DB::table($this->getAttributesTable($artifact->artifact_type))
                                            ->where('artifact_id', $artifact->artifact_id);
                                            
                                    } catch(\Exception $e) { 
                                        $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message", $e->getMessage()]);
                                    }

                                    foreach($this->getArtifactReadableAttributes($artifact->artifact_type) as $field) {
                                        $artifactAttributes->addSelect($field);
                                        
                                        if($artifact->artifact_type == 3) {
                                           // Log::info('file field', $field);
                                        }
                                    }
                                    
                                    
                                    
                                    $artifact->attributes = $artifactAttributes->first();
                                    
                                    $this->userArtifacts[$obj->artifact_id] = $artifact;
                                    
                                    $this->addUpdates('bulk_edit', ArtifactMapper::cast($artifact));
                                    
                                } else {
                                    
                                }
                                
                                
                            }
                            
                            
                        }

                    }
                    
                }
                
            }

            
            $bulkCreate = [];
            foreach($this->userArtifacts as $id => $artifact) {
                unset($artifact->temp_artifact_id);
                $bulkCreate[] = ArtifactMapper::cast($artifact); 
            }
            
			
			
            if($this->noErrors()) {
                $this->connection->send(json_encode([
                    "event"          => $this->responseEvent($this->playload->event),
                    "seq_num"        => $this->playload->seq_num,
                    "status_code"    => 200,
                    "data" => [
                        "bulk_create" => $bulkCreate,
                        "bulk_edit" => [],
                        "bulk_delete" => []
                    ],
                ]));
            }
            
        }
        
    }
    
    
    /**
     *  Обработка артефактов, созданных в оффлайне:
     */
    public function createOfflineArtifacts() 
    {
        if(count($this->playload->data->bulk_create) > 0) {
            foreach($this->playload->data->bulk_create as $key => $object) {
                
                // Если родитель уже существует:
                if(property_exists($object, 'parent_artifact_id')) {
                    
                    $artifact = $this->findByTempArtifactId($object->temp_artifact_id);
                    
                    // Проверяю не создан ли уже такой артефакт:
                    if(!$artifact) {
                    
                        try {
                            // Создаю артефакт:
                            $artifactId = DB::table('artifacts')->insertGetId([
                                'parent_artifact_id'        => $object->parent_artifact_id,
                                'artifact_type'             => $object->artifact_type,
                                'created_by_user_id'        => $this->userId,
                                'last_modified_by_user_id'  => $this->userId,
                                'created_at'                => Carbon::now()->timestamp,
                                'last_modified_at'          => Carbon::now()->timestamp,
                                'version'                   => 1,
                                'order_index'               => $object->order_index,
                                'temp_artifact_id'          => $object->temp_artifact_id,
                            ]);
                        
                        } catch(\Exception $e) {
                            $this->throwError(500, 'Error while creating artifact.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                            return false;
                            
                        }

                        // Дефолтные атрибуты:
                        $attributes = [
                            'artifact_id'               => $artifactId,
                            'created_by_user_id'        => $this->userId,
                        ];

                        // Уникальные атрибуты:
                        $attributes = array_merge($attributes, $this->matchAttributes($object));
                        
                        try {
                            DB::table($this->getAttributesTable($object->artifact_type))->insert($attributes);
                        } catch(\Exception $e) {
                            $this->throwError(500, 'Error while creating artifact attributes.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                            return false;
                            
                        }
                        
                        // Беру созданный артефакт:
                        $artifact = DB::table('artifacts')
                            //->select('artifact_id', 'parent_artifact_id', 'artifact_type', 'created_by_user_id', 'created_at', 'last_midified_by_user_id', 'version')
                            ->where('artifact_id', $artifactId)
                            ->first();
                            
                        $artifactAttributes = DB::table($this->getAttributesTable($artifact->artifact_type))
                            ->where('artifact_id', $artifact->artifact_id);

                        foreach($this->getArtifactReadableAttributes($artifact->artifact_type) as $field) {
                            $artifactAttributes->addSelect($field);
                        }
                    
    
                        $artifact->attributes = $artifactAttributes->first();
                        
                        $parentTo = $artifact->artifact_id;
                        
                        // В рассылку:
                        $this->userArtifacts->push($artifact);
                        
                        $artifactToOthers = $artifact;
                        unset($artifactToOthers->temp_artifact_id);
                        $this->addUpdates('bulk_create', ArtifactMapper::cast($artifactToOthers));
                        
                    } else {
                        // Если уже был создан, беру ид:
                        $parentTo = $artifact->artifact_id;
                    }
                    
                    // Заменяю дочерним родительский артефакт:
                    $this->setParentParentArtifactId($object->temp_artifact_id, $parentTo);
                    
                    // Удаляю из очереди:
                    unset($this->playload->data->bulk_create[$key]);
                    
                } else if(property_exists($object, 'temp_parent_artifact_id')) {

                }

            }
            
            // Если еще остались артефакты в очереди, повторяю:
            if(count($this->playload->data->bulk_create) > 0) {
                $this->createOfflineArtifacts();
            }
        }
    }
    
    /**
     *  Замена временного ид на постоянный для созданных в оффлайне:
     */
    public function setParentParentArtifactId($parentFrom, $parentTo)
    {
        foreach($this->playload->data->bulk_create as $key => $object) {
            if(property_exists($object, 'temp_parent_artifact_id')) {
                if($object->temp_parent_artifact_id === $parentFrom) {
                    unset($this->playload->data->bulk_create[$key]->temp_parent_artifact_id);
                    $this->playload->data->bulk_create[$key]->parent_artifact_id = $parentTo;
                    
                }
            }
        }
        
        foreach($this->playload->data->bulk_edit as $key => $object) {
            if(property_exists($object, 'temp_parent_artifact_id')) {
                if($object->temp_parent_artifact_id === $parentFrom) {
                    unset($this->playload->data->bulk_edit[$key]->temp_parent_artifact_id);
                    $this->playload->data->bulk_edit[$key]->parent_artifact_id = $parentTo;
                    
                }
            }
        }
    }
    
    /**
     *  Поиск артефакта по сохраеннному в БД временному ид:
     */
    public function findByTempArtifactId($temp_artifact_id) 
    {
        foreach($this->userArtifacts as $artifact) {
            if(property_exists($artifact, 'temp_artifact_id')) {
                if($artifact->temp_artifact_id === $temp_artifact_id) {
                    return $artifact;
                    
                }  
            }            
        }
        
        return false;
    }
    
    /**
     *  Поиск идентификаторов дочерних артефактов среди снэпшота:
     */
    public function collectChildrenIds($parentId) 
    {
        $idsArray = [];
        
        foreach($this->userArtifacts as $artifact) {
            if($artifact->parent_artifact_id == $parentId) {
                
                $childrenIDs = $this->collectChildrenIds($artifact->artifact_id);
                $idsArray = array_merge($idsArray, $childrenIDs);
                
                $idsArray[] = $artifact->artifact_id;
            }

        }
        
        return $idsArray;
    }
    
    /**
     *  Проверяю существует ли родительский артефакт:
     */
    public function isTempOrphan($temp_parent_artifact_id) 
    {
        foreach($this->playload->data->bulk_create as $key => $object) {
            
            if(property_exists($object, 'temp_artifact_id')) {
                if($object->temp_artifact_id === $temp_parent_artifact_id) {
                    return false;
                }
            }

        }
       
        return true;
    }
    
    // Временные идентификаторы артефактов не должны быть идентичны родительским временным идентификаторам:
    public function isTempIdsUnique() 
    {
        if($this->noErrors()) {
            
            $tempIDs = [];
            $tempParentIDs = [];
            
            if(count($this->playload->data->bulk_create) > 0) {
                foreach($this->playload->data->bulk_create as $obj) {
                    
                    if($this->noErrors()) {
                        
                        $tempIDs[] = $obj->temp_artifact_id;
                        
                        if(property_exists($obj, 'temp_parent_artifact_id')) {
                            if($obj->temp_parent_artifact_id === $obj->temp_artifact_id) {
                                $this->throwError(400, 'temp_parent_artifact_id and temp_artifact_id cannot be the same.', __FILE__, __LINE__, ['artifact' => $obj]);
                                return false;
                            
                            }
                        } else {
                            if($obj->parent_artifact_id === $obj->temp_artifact_id) {
                                $this->throwError(400, 'parent_artifact_id and temp_artifact_id cannot be the same.', __FILE__, __LINE__, ['artifact' => $obj]);
                                return false;
                            
                            } 
                        }

                    }
                }
                
                if(count($tempIDs) !== count(array_unique($tempIDs))) {
                    $this->throwError(400, 'All temp_artifact_id must be unique.', __FILE__, __LINE__, ['artifact' => $obj]);

                    return false;
                }
                
            }
        }
        
        // Запрещаю указывать одинаковые идентификаторы:
        if(count($this->playload->data->bulk_edit) > 0) {
            foreach($this->playload->data->bulk_edit as $obj) {
                
                if($this->noErrors()) {
                    
                    $tempIDs[] = $obj->artifact_id;
                    
                    if(property_exists($obj, 'temp_parent_artifact_id')) {
                        if($obj->temp_parent_artifact_id === $obj->artifact_id) {
                            $this->throwError(400, 'temp_parent_artifact_id and artifact_id cannot be the same.', __FILE__, __LINE__, ['artifact' => $obj]);
                            return false;
                        
                        }
                    } else {
                        if($obj->parent_artifact_id === $obj->artifact_id) {
                            $this->throwError(400, 'parent_artifact_id and artifact_id cannot be the same.', __FILE__, __LINE__, ['artifact' => $obj]);
                            return false;
                        
                        } 
                    }
                        
                        
                }
            }
        }
        
               
    }
}
    