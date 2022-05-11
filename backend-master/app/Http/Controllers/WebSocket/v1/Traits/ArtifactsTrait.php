<?php 

namespace App\Http\Controllers\WebSocket\v1\Traits;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

use DB;
use Log;

/**
 *  Артефакты
 */
trait ArtifactsTrait
{
    
    /**
     *  Типы артефактов:
     */
    private $artifactTypes = [
        1 => "trip",
        2 => "city",
        3 => "file",
        4 => "note",
        5 => "link",
        6 => "transfer",
        7 => "booking",
        8 => "note_photo",
        9 => "flight",
    ];
    
    /**
     *  Таблицы атрибутов артефактов:
     */
    private $attributesTables = [
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
    
    /**
     *  Атрибуты артефактов доступные для записи:
     */
    private $attributes = [
        1 => ["name", "description"],
        2 => ["city_id", "departure_date", "arrival_date"],
        3 => ["title"],
        4 => ["text", "is_trip_description"],
        5 => ["title"], // "upload_is_complete" и "link" недоступны для записи фронтендам
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
        8 => ["title"],
        9 => [
            "title", 
            "departure_city_id", 
            "arrival_city_id", 
            "departure_iata_code", 
            "arrival_iata_code", 
            "flight_number", 
            "carrier", 
        ],
    ];
    
    /**
     *  Атрибуты артефактов доступные для чтения:
     */
    private $readableAttributes = [
        1 => ["name", "description"],
        2 => ["city_id", "departure_date", "arrival_date"],
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
        ],
    ];
    
    /**
     *  Обязательные атрибуты артефактов:
     */
    private $requiredAttributes = [
        1 => [],
        2 => ["city_id"],
        3 => [],
        4 => [],
        5 => [],
        6 => ["category_id"],
        7 => [],
        8 => [],
        9 => [],
    ];
    
    public $userArtifacts;
    
    /**
     *  Узнать имя таблицы атрибутов для указанного типа артефакта:
     */
    public function getAttributesTable($type) 
    {
        return $this->attributesTables[$type];
    }
    
    /**
     *  Узнать имя артифакта по типу:
     */
    public function getArtifactName($type) 
    {
        return $this->artifactTypes[$type];
    }
    
    /**
     *  Узнать все типы артефактов:
     */
    public function getArtifactTypes() 
    {
        return array_keys($this->artifactTypes);
    }
    
    /**
     *  Узнать все атрибуты артефакта доступные для записи:
     */
    public function getArtifactAttributes($type) 
    {
        return $this->attributes[$type];
    }
    
    /**
     *  Узнать все атрибуты артефакта доступные для чтения:
     */
    public function getArtifactReadableAttributes($type) 
    {
        return $this->readableAttributes[$type];
    }
    
    
    /**
     *  Узнать все обязательные атрибуты артефакта:
     */
    public function getArtifactRequiredAttributes($type) 
    {
        return $this->requiredAttributes[$type];
    }
    
    /**
     *  Спарсить атрибуты артефакта из входящих данных:
     */
    public function matchAttributes($dataObj) 
    {
        $attributes = [];
        
        foreach($this->attributes[$dataObj->artifact_type] as $field) {
            if(property_exists($dataObj->attributes, $field)) {
                $attributes[$field] = $dataObj->attributes->$field;
            }
        }
        
        return $attributes;
    }
    
    /**
     *  Получить артефакт по указанному ID:
     */
    public function getArtifactById($artifactId) 
    {
        try { 
        
            $artifact = DB::table('artifacts')
                ->where('artifact_id', $artifactId)
                ->first();
            
            if($artifact == null) {
                return null;
            }
            
            $artifactAttributes = DB::table($this->getAttributesTable($artifact->artifact_type))
                ->where('artifact_id', $artifact->artifact_id);

            foreach($this->attributes[$artifact->artifact_type] as $field) {
                $artifactAttributes->addSelect($field);
            }
            
            try { 
                $artifact->attributes = $artifactAttributes->first();
            } catch(\Exception $e) { 
                Log::error('Произошла ошибка.', [$e->getMessage()]);
                return $e;
                
            }

            return $artifact;
        
        } catch(\Exception $e) { 
            Log::error('Произошла ошибка.', [$e->getMessage()]);
            return $e;
            
        }
    }

    
    /**
     *  Удалить артефакт по указанному ID:
     */
    public function destroyArtifactById($artifactId) 
    {
        try { 
            // Ищу артефакт:
            $artifact = DB::table('artifacts')
                ->select('artifact_id', 'parent_artifact_id', 'artifact_type', 'created_by_user_id', 'created_at', 'last_modified_by_user_id', 'version')
                ->where('artifact_id', $artifactId)
                ->first();

            // Log::info('Удалить файл из облака', [$artifact]);  
                
                
            if($artifact == null) {
                return false;
            } else {
                
                // если это файл
                if($artifact->artifact_type == 3 or $artifact->artifact_type == 8) {

                    // взять атрибуты
                    $fileAttributes = DB::table($this->getAttributesTable($artifact->artifact_type))
                    ->where('artifact_id', $artifact->artifact_id)->first();

                    if($fileAttributes != null) {
                        if($fileAttributes->upload_is_complete == 1) {
                            
                            $currentPath = $fileAttributes->created_by_user_id . '/' . $fileAttributes->artifact_id . '/' . $fileAttributes->link;
                            
                            try {
                                $s3 = S3Client::factory([
                                    'credentials' => [
                                        'key'       => config('filesystems.disks.s3.key'),
                                        'secret'    => config('filesystems.disks.s3.secret')
                                    ],
                                    'version'       => 'latest',
                                    'region'        => config('filesystems.disks.s3.region')
                                ]);

                                $result = $s3->deleteObject(array(
                                  'Bucket' => config('filesystems.disks.s3.bucket'),
                                  'Key'    => $currentPath,
                                ));
                                
                            } catch (Exception $e) {
                                Log::info('Ошибка при удалении файла с облака', [
                                    "message" => $e->getMessage()
                                ]);
                                
                            }
                        }
                    }

                }
            }
            
        } catch(\Exception $e) { 
            Log::error('Произошла ошибка.', [$e->getMessage()]);
            return $e;
            
        }
        
        
        try { 
            // Удаляю атрибуты:
            DB::table($this->getAttributesTable($artifact->artifact_type))
                ->where('artifact_id', $artifact->artifact_id)->delete();
        } catch(\Exception $e) { 
            Log::error('Произошла ошибка.', [$e->getMessage()]);
            return $e;
            
        }
        
        try { 
            // Удаляю артефакт:
            DB::table('artifacts')
                ->where('artifact_id', $artifactId)
                ->delete();
                
        } catch(\Exception $e) { 
            Log::error('Произошла ошибка.', [$e->getMessage()]);
            return $e;
            
        }
        
        
        return true;
    }
    
    /**
     *  Найти все дочерние артефакты в виде дерева:
     */
    public function findChildrenTree($parentArtifactId) 
    {
        /*
        $array = [];
        
        $artifacts = DB::table('artifacts')
            ->where('artifact_parent_id', $parentArtifactId)
            ->get();
            
        foreach($artifacts as $key => $artifact) {
            $artifact->children = $this->findChildrenTree($artifact->artifact_id);
            $array[] = $artifact;
        }
            
        return $array;
        */
    }
    
    /**
     *  Найти все идентификаторы дочерних артефактов:
     */
    public function findChildrenIDs($parentArtifactId) 
    {
        $array = [];
        
        try { 
            $artifacts = DB::table('artifacts')
                ->where('parent_artifact_id', $parentArtifactId)
                ->get();
                
        } catch(\Exception $e) { 
            Log::error('Произошла ошибка.', [
                'Message:'  => $e->getMessage(), 
                'File:'     => $e->getFile(), 
                'Line:'     => $e->getLine(), 
            ]);
            return $e;
            
        }
            
        foreach($artifacts as $key => $artifact) {
            
            $result = $this->findChildrenIDs($artifact->artifact_id);
            
            if($result instanceof \Exception) {
                Log::error('Произошла ошибка.', [
                    'Message:'  => $e->getMessage(), 
                    'File:'     => $e->getFile(), 
                    'Line:'     => $e->getLine(), 
                ]);
                return $result;
                
            }
            
            $array = array_merge($array, $result);
            $array[] = $artifact->artifact_id;
        }
            
        return $array;
    }
    
    /**
     *  Извлечь все артефакты текущего пользователя:
     */
    public function getAllUserArtifacts()
    {
        
        try {
            
            $this->userArtifacts = DB::table('artifacts')
               // ->select('artifact_id', 'parent_artifact_id', 'artifact_type', 'created_by_user_id', 'created_at', 'last_modified_by_user_id', 'version', 'temp_artifact_id')
                ->where('created_by_user_id', $this->userId)
                ->get()->keyBy('artifact_id');
            

            $attributes = [];
            
            foreach($this->artifactTypes as $typeId => $typeName) {
                
                $collection = DB::table($this->attributesTables[$typeId])
                    ->where('created_by_user_id', $this->userId)
                    ->get()->keyBy('artifact_id');
                    
                foreach($collection as $key => $val) {
                    if($typeId == 3) {
                        $collection[$key] = collect($val)->except(['created_by_user_id', 'artifact_id', 'link']);
                    } else {
                        $collection[$key] = collect($val)->except(['created_by_user_id', 'artifact_id']);
                    }
                }

                $attributes[$typeId] = $collection;

            }
            
        } catch(\Exception $e) { 
            $this->throwError(500, 'Database error. Check server logs.', __FILE__, __LINE__, ["status_message" => $e->getMessage()]);
            return false;
            
        }
        
        foreach($this->userArtifacts as $artifactId => $artifact) {
            
            if(isset($attributes[$artifact->artifact_type][$artifactId])) {
                $this->userArtifacts[$artifactId]->attributes = $attributes[$artifact->artifact_type][$artifactId];
            }
            
        }
    }
    
}