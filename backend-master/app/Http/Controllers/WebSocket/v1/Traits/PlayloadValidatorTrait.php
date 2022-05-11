<?php 

namespace App\Http\Controllers\WebSocket\v1\Traits;

use DB;
use Log;

/**
 *  Артефакты
 */
trait PlayloadValidatorTrait
{
    /**
     *  Ошибки:
     */
    private $errors = [];
    
    public function noErrors() 
    {
        if(count($this->errors) == 0) {
            return true;
        }
        
        return false;
    }
    
    public function validateHeaders() 
    {
        if($this->noErrors()) {
            if(!property_exists($this->playload, 'event')) {
                $this->playload->event = "error";
                $this->throwError(400, 'Event not specified', __FILE__, __LINE__, []);
                return false;
                
            }
        }
        
        if($this->noErrors()) {
            // Указано ли разрешенное имя события:
            if (!in_array($this->playload->event, $this->getAllowedIncomingEvents())) {
                $this->throwError(400, 'Wrong event name. 2', __FILE__, __LINE__, []);
                return false;
                
            }
        }
        
        if($this->noErrors()) {
            if(!property_exists($this->playload, 'seq_num')) {
                $this->throwError(400, 'seq_num not specified.', __FILE__, __LINE__, []);
                return false;
                
            }
        }
        
        if($this->noErrors()) {
            
            if($this->playload->event == 'ping') {
                
            } else if($this->playload->event == 'unsubscribe_from_updates') {
                
            } else {
                
                if(!property_exists($this->playload, 'data')) {
                    $this->throwError(400, 'data not specified.', __FILE__, __LINE__, []);
                    return false;
                    
                } else {
                    if($this->playload->event == 'edit_artifact') {
                        if(!property_exists($this->playload->data, 'version')) {                            
                            $this->throwError(400, 'version not specified.', __FILE__, __LINE__, []);
                            return false;
                            
                        } else {
                            if(!$this->validateDataProperty_version($this->playload->data->version)) {
                                $this->throwError(400, 'Wrong version.', __FILE__, __LINE__, []);
                                return false;
                                
                            } else {
                                if($this->playload->data->version < 1) {
                                    $this->throwError(400, 'Wrong version.', __FILE__, __LINE__, []);
                                    return false; 
                                    
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function validateBodyProperties() 
    {
        if($this->noErrors()) {
            
            if($this->playload->event == 'subscribe_to_updates') {
                $this->validateSubscribeToUpdatesBodyProperties();

            } else {
                foreach($this->getRequiresBodyProperties($this->playload->event) as $requiredProperty) {
                    if($this->noErrors()) {
                        if(!property_exists($this->playload->data, $requiredProperty)) {
                            $this->throwError(400, $requiredProperty . ' not specified.', __FILE__, __LINE__, []);
                            return false;
                            
                        }
                    } 
                }
            }  
        }
    }
    
    public function validateBodyValues() 
    {
        if($this->noErrors()) {
            
            if($this->playload->event == 'subscribe_to_updates') {
                $this->validateSubscribeToUpdatesBodyValues();

            } else {
                
                foreach($this->getRequiresBodyProperties($this->playload->event) as $requiredProperty) {
                    if($this->noErrors()) {
                        
                        if($requiredProperty == 'attributes') {
                            
                            if(!is_object($this->playload->data->attributes)) {
                                $this->throwError(400, 'Attributes property must be an object.', __FILE__, __LINE__, []);
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
                                if(!$this->$method($this->playload->data->$requiredProperty)) {
                                    $this->throwError(400, 'Wrong ' . $requiredProperty . ' value.', __FILE__, __LINE__, []);
                                    return false; 
                                    
                                }
                            }
                        }
                    }
                }
            }
        } 
    }
        
    
    public function validateAttributesProperties() 
    {
        if($this->noErrors()) {
            if($this->playload->event == "add_artifact" or $this->playload->event == "edit_artifact") {
                foreach($this->getArtifactRequiredAttributes($this->playload->data->artifact_type) as $requiredAttribute) {
                    
                    if($this->noErrors()) {
                        if(!property_exists($this->playload->data->attributes, $requiredAttribute)) {
                            $this->throwError(400, 'Attribute '. $requiredAttribute . ' not specified.', __FILE__, __LINE__, []);
                            return false;
                            
                        }
                    }
                }
            } else if($this->playload->event == "subscribe_to_updates") {
                $this->validateSubscribeToUpdatesBodyAttributesProperties();
                
            }
        
        }
    }
    
    public function validateAttributesValues()
    {
        $attributes = [];
        
        if($this->noErrors()) {
            
            if($this->playload->event == "subscribe_to_updates") {
                $this->validateSubscribeToUpdatesBodyAttributesValues();
                
            } else if($this->playload->event == "add_artifact" or $this->playload->event == "edit_artifact") {
                $attributes = $this->matchAttributes($this->playload->data);

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

                                $this->throwError(400, 'Wrong ' . $attribute . ' value.', __FILE__, __LINE__, []);
                                
                                return false; 
                            }
                        }
                    }
                }
            }            
        }
    }

    public function validatePermissions()
    {
        if($this->noErrors()) {
            
            if($this->playload->event == 'add_artifact') {
                
                if($this->playload->data->parent_artifact_id > 0) {
                    try {
                    
                        $artifact = DB::table('artifacts')
                            ->where('artifact_id', $this->playload->data->parent_artifact_id)
                            ->first();
                        
                        if($artifact == null) {
                            $this->throwError(404, 'Parent_artifact_id not found.', __FILE__, __LINE__, []);
                            return false;
                            
                        } else {
                            
                            if($artifact->created_by_user_id != $this->userId) {
                                $this->throwError(403, 'Forbidden.', __FILE__, __LINE__, ["artifact" => $artifact]);
                                return false;
                                
                            }
                        }
                    
                    } catch(\Exception $e) { 
                        $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                        return false;
                        
                    }
                
                }

            } else if($this->playload->event == 'edit_artifact') {
                
                try {
                
                    $artifact = DB::table('artifacts')
                        ->where('artifact_id', $this->playload->data->artifact_id)
                        ->first();
                    
                    if($artifact == null) {
                        $this->throwError(404, 'Artifact_id not found.', __FILE__, __LINE__, []);
                        return false;
                        
                    } else {
                        
                        if($artifact->created_by_user_id != $this->userId) {
                            $this->throwError(403, 'Forbidden.', __FILE__, __LINE__, ["artifact" => $artifact]);
                            return false;
                            
                        }
                    }
                
                } catch(\Exception $e) { 
                    $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
                    return false;
                    
                }
               
                if($this->noErrors()) {
                    
                    if($this->playload->data->parent_artifact_id > 0) {
                        try { 
                        
                            $artifact = DB::table('artifacts')
                                ->where('artifact_id', $this->playload->data->parent_artifact_id)
                                ->first();
                            
                            if($artifact == null) {
                                $this->throwError(404, 'Parent_artifact_id not found.', __FILE__, __LINE__, []);
                                return false;
                                
                            } else {
                                
                                if($artifact->created_by_user_id != $this->userId) {
                                    $this->throwError(403, 'Forbidden.', __FILE__, __LINE__, ["artifact" => $artifact]);
                                    return false;
                                    
                                }
                            }
                        
                        } catch(\Exception $e) { 
                            $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]); 
                            return false;
                            
                        }
                    
                    }
                        
                }

            } else if($this->playload->event == 'delete_artifact') {
                try {
                
                    $artifact = DB::table('artifacts')
                        ->where('artifact_id', $this->playload->data->artifact_id)
                        ->first();
                    
                    if($artifact == null) {                        
                        $this->throwError(404, 'Artifact_id not found', __FILE__, __LINE__, []);
                        return false;
                        
                    } else {
                        
                        if($artifact->created_by_user_id != $this->userId) {
                            $this->throwError(403, 'Forbidden.', __FILE__, __LINE__, ["artifact" => $artifact]);
                            return false;
                            
                        }
                    }
                
                } catch(\Exception $e) { 
                    $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]); 
                    return false;
                    
                }
            } else if($this->playload->event == 'subscribe_to_updates') {
                $this->validateSubscribeToUpdatesPermissions();
            }
            
        }
    }
    
    public function validateAttribute_name($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }

        return false;
    }
    
    public function validateAttribute_description($value) 
    {         
        return true;
    }
    
    public function validateAttribute_city_id($value) 
    {
        if($this->isUInt($value)) {
            return true;
        }

        return false;
    }
    
    
    public function validateAttribute_title($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }

        return false;
    }
    
    public function validateAttribute_link($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }

        return false;
    }
    
    public function validateAttribute_text($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_string($value)) {
            return true;
        }

        return false;
    }
    
    public function validateAttribute_category_id($value) 
    {        
        if ($value == null){
            return true;
        }
        
        if(!is_int($value)) {
            return false;
        }
        
        if (in_array($value, [1,2,3,4,5])){
            return true;
        }

        return false;
    }
    
    public function validateAttribute_departure_city_id($value) 
    {
        if($value == null) {
            return true;
        }
        
        if($this->isUInt($value)) {
            return true;
        }

        return false;
    }
    
    public function validateAttribute_departure_date($value) 
    {
        if($value == null) {
            return true;
        }
        
        if($this->isUInt($value)) {
            return true;
        }

        return false;
    }
    
    public function validateAttribute_arrival_date($value) 
    {
        if($value == null) {
            return true;
        }
        
        if($this->isUInt($value)) {
            return true;
        }

        return false;
    }
    
    public function validateAttribute_arrival_city_id($value) 
    {
        if($value == null) {
            return true;
        }
        
        if($this->isUInt($value)) {
            return true;
        }

        return false;
    }
    
    public function validateAttribute_departure_at($value) 
    {
        if($value == null) {
            return true;
        }
        
        if($this->isUInt($value)) {
            return true;
        }

        return false;
    }
    
    public function validateAttribute_arrival_at($value) 
    {
        if($value == null) {
            return true;
        }
        
        if($this->isUInt($value)) {
            return true;
        }

        return false;
    }
    
    public function validateAttribute_flight_number($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }

        return false;
    }
    
    public function validateAttribute_fly_min($value) 
    {
        if($value == null) {
            return true;
        }
        
        if($this->isUInt($value)) {
            return true;
        }

        return false;
    }
    
    public function validateAttribute_departure_iata_code($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }

        return false;
    }
    
    public function validateAttribute_arrival_iata_code($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }

        return false;
    }
    
    public function validateAttribute_carrier($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }

        return false;
    }
    
    public function validateAttribute_address($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }

        return false;
    }
    
    public function validateAttribute_latitude($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_float($value)) {
            return true;
        }
        
        return false;
    }
    
    public function validateAttribute_longitude($value) 
    {
        if($value == null) {
            return true;
        }
        
        if(is_float($value)) {
            return true;
        }
        
        return false;
    }
    
    public function validateAttribute_is_trip_description($value) 
    {
		// Log::info("validateAttribute is_trip_description", [$value]);
		
		if(is_bool($value)) {
			return true;
		}
		
		if ($value === true or $value === false) {
			return true;
		}
		
        return false;
    }
	
	
    public function validateDataProperty_artifact_id($value) 
    {
        if($this->isBigUInt($value)) {
            return true;
            
        }
        
        return false;        
    }
    
    public function validateDataProperty_parent_artifact_id($value) 
    {
        if($value == null) {
            return true;
        }
        
        if($this->isBigUInt($value)) {
            return true;
            
        }
        
        return false;
    }

    public function validateDataProperty_temp_artifact_id($value) 
    {        
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }
        
        return false;
    }
    
    public function validateDataProperty_temp_parent_artifact_id($value) 
    {
        if(is_string($value)) {
            if(strlen($value) < 190) {
                return true;
            }
        }
        
        return false;
    }
    
    public function validateDataProperty_version($value) 
    {
        if($this->isUInt($value)) {
            return true;
        }
        
        return false;
    }
    
    public function validateDataProperty_order_index($value) 
    {
        if($this->isUInt($value)) {
            return true;
        }
        
        return false;
    }
		
    
    
    public function validateDataProperty_artifact_type($value) 
    {
        if (in_array($value, $this->getArtifactTypes())) {
            return true;
           
        }
        
        return false;
    }
    
    public function isSmallUint($value) 
    {        
        if (is_int($value)) {
            if (is_numeric($value)) {
                if($value >= 0 and $value <= 65535) {
                    return true;
                }   
            }
        }

        return false;
    }

    public function isInt($value) 
    {
        if (is_int($value)) {
            if (is_numeric($value)) {
                if($value >= -2147483648 and $value <= 2147483647) {
                    return true;
                }   
            }
        }

        return false;
    }
    

    public function isUInt($value) 
    {
        if (is_int($value)) {
            if (is_numeric($value)) {
                if($value >= 0 and $value <= 2147483647) {
                    return true;
                }   
            }
        }

        return false;
    }
    
    public function isBigUInt($value)
    { 
        if ($value === 0) {
            return true;
        }
        
        if ($value == null) {
            return false;
        }
        
        if (is_int($value)) {
            if (is_numeric($value)) {
                if($value >= 0 and $value <= PHP_INT_MAX) {
                    return true;
                } 
                
            }
           
        } else if($value >= 0 and $value < PHP_INT_MAX) {
            
            if($this->numbersOnly($value) == 1) {
                return true;
            } 
        }

        return false;
    }
    
    public function isBigUIntOrNull($value)
    { 
        if ($value == null) {
            return true;
        }
        
        if ($value === 0) {
            return false;
        }
        
        if (is_int($value)) {
            if (is_numeric($value)) {
                if($value >= 0 and $value <= PHP_INT_MAX) {
                    return true;
                } 
                
            }
           
        } else if($value >= 0 and $value < PHP_INT_MAX) {
            
            if($this->numbersOnly($value) == 1) {
                return true;
            } 
        }

        return false;
    }
    
    public function numbersOnly($value)
    {
        preg_match_all('/^([0-9]*)$/', $value, $matches);
        return count($matches[0]);
    } 
    
}