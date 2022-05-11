<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;
use File;
use DB;
use URL;
use Log;

use Carbon\Carbon;
use App\User;
use App\City;
use App\Region;
use App\Country;

/**
 *  
 */
class CityController extends Controller
{
    private $lastModifiedAt = 662680861;
    
    public function __construct()
    {
        $this->lastModifiedAt = Carbon::now()->toDateTimeString();
    }
    
    /**
     *  @OA\Post(
     *      path="/api/get_cities_updates",
     *      summary="Метод обновления базы данных городов в приложении.",
     *      description="Требуется авторизация при помощи заголовка bearer. Фронт должен передать метку unix_time последнего обновления базы, а сервер в ответ пришлет все изменения, которые произошли с указанного времени.",
     *      operationId="get_cities_updates",
     *      tags={"City"}, 
     *      security={},
     *      @OA\Parameter(
     *          description="Unix time",
     *          in="query",
     *          name="last_modified_at",
     *          required=true,
     *          example="1630369098",
     *          @OA\Schema(
     *              type="integer",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Найдено.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=200,
     *              ),
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/WikidataSchema"),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Ошибка.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=400,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Wrond unix timestamp."
     *              )
     *          )
     *      ),
     *  )
     */
     //
    public function getCitiesUpdate(Request $request)
    {
        
        if(!$request->has('last_modified_at')) {
            return response()->json([
                'status' => 400,
                'message' => 'Specify last_modified_at field.'
            ]);
        }
        
        if($request->last_modified_at == null) {
            return response()->json([
                'status' => 400,
                'message' => 'Field last_modified_at cannot be null.'
            ]);
        }
        
        
        if(!$this->isTimestamp($request->last_modified_at)) {
            return response()->json([
                'status' => 400,
                'message' => 'Wrond unix timestamp.'
            ]);  
        }

        if(strlen($request->last_modified_at) < 21) {
            $this->lastModifiedAt = Carbon::createFromTimestamp($request->last_modified_at)->toDateTimeString(); 
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Wrond unix timestamp.'
            ]);   
        }
   
        return response()->json([
            'status' => 200,
            //'request' => $this->lastModifiedAt,
            'data' => [
                'cities' => [
                    'bulk_create' => $this->getItems(
                        "App\City", 
                        ['id', 'name', 'name_ru', 'iata_code', 'region_id', 'country_code', 'longitude', 'latitude', 'population', 'genitive', 'dative', 'accusative', 'instrumental', 'prepositional', 'wiki_entity'],
                        "created_at"
                    ),
                    'bulk_edit' => $this->getItems(
                        "App\City", 
                        ['id', 'name', 'name_ru', 'iata_code', 'region_id', 'country_code', 'longitude', 'latitude', 'population', 'genitive', 'dative', 'accusative', 'instrumental', 'prepositional', 'wiki_entity'],
                        "updated_at"
                    ),
                    'bulk_delete' => $this->getItems(
                        "App\City", 
                        ['id', 'name', 'name_ru', 'iata_code', 'region_id', 'country_code', 'longitude', 'latitude', 'population', 'genitive', 'dative', 'accusative', 'instrumental', 'prepositional', 'wiki_entity'],
                        "deleted_at"
                    ),
                ],
                'regions' => [
                    'bulk_create' => $this->getItems(
                        "App\Region", 
                        ['id', 'name_en', 'name_ru', 'country_id'],
                        "created_at"
                    ),
                    'bulk_edit' => $this->getItems(
                        "App\Region", 
                        ['id', 'name_en', 'name_ru', 'country_id'],
                        "updated_at"
                    ),
                    'bulk_delete' => $this->getItems(
                        "App\Region", 
                        ['id', 'name_en', 'name_ru', 'country_id'],
                        "deleted_at"
                    ),
                ],
                'countries' => [
                    'bulk_create' => $this->getItems(
                        "App\Country", 
                        ['id', 'name_en', 'name_ru', 'code'],
                        "created_at"
                    ),
                    'bulk_edit' => $this->getItems(
                        "App\Country", 
                        ['id', 'name_en', 'name_ru', 'code'],
                        "updated_at"
                    ),
                    'bulk_delete' => $this->getItems(
                        "App\Country", 
                        ['id', 'name_en', 'name_ru', 'code'],
                        "deleted_at"
                    ),
                ]
            ]
        ]);
        
    }
    
    public function getItems($model, $fieldsToGet, $fieldToCompare) 
    {
        $items = [];

        // created_at = были созданы после даты, но не были удалены и дата создания == дате модификации
        // updated_at = были модифицированы после даты, но не были удалены или и дата создания != дате модификации
        // deleted_at = были удалены после даты
        
        if($fieldToCompare == "created_at") {

            $model::select($fieldsToGet)
                ->where("created_at", ">", $this->lastModifiedAt)
                ->whereRaw('updated_at = created_at')
                ->whereNull("deleted_at")
                ->chunk(10000, function($records) use (&$items, $model) {
                    
                    foreach($records as $record) {                    
                        $items[] = $this->renameFields($model, $record);
                    }
                    
                });
            
        }
        
        if($fieldToCompare == "updated_at") {

            $model::select($fieldsToGet)
                ->where("updated_at", ">", $this->lastModifiedAt)
                ->whereRaw('updated_at != created_at')
                ->whereNull("deleted_at")
                ->chunk(10000, function($records) use (&$items, $model) {
                    
                    foreach($records as $record) {
                        $items[] = $this->renameFields($model, $record);
                    }
                    
                });
            
        }
        
        if($fieldToCompare == "deleted_at") {

            $model::select($fieldsToGet)
                ->where($fieldToCompare, ">", $this->lastModifiedAt)
                ->chunk(10000, function($records) use (&$items, $model) {
                    
                    foreach($records as $record) {                    
                        $items[] = ['id' =>$record->id ];
                    }
                    
                });
            
        }

        return $items;
    }
    
    public function isTimestamp($x, $lenMax = 11, $compare = 30)
    {
        if (!ctype_digit($x)) {
            return false;
        }
        $x = strlen($x) >= $lenMax ? $x / 1000 : $x;
        
        if ($x < strtotime("-{$compare} years") || $x > strtotime("+{$compare} years")) {   
            return false;
        }
        
        return true;
        
    }
    
    /**
     *  Изменения названий полей для экспорта в приложение:
     */
    public function renameFields($model, $record)
    {
        if($model == "App\City") {
/*
            $record->iata = $record->iata_code; unset($record->iata_code);
            $record->regionID = $record->region_id; unset($record->region_id);
            $record->countryCode = $record->country_code; unset($record->country_code);
            $record->nameEN = $record->name; unset($record->name);
            $record->nameRU = $record->name_ru; unset($record->name_ru); 
            $record->nameRU_rod = $record->genitive; unset($record->genitive); 
            $record->nameRU_dat = $record->dative; unset($record->dative); 
            $record->nameRU_vin = $record->accusative; unset($record->accusative); 
            $record->nameRU_tvo = $record->instrumental; unset($record->instrumental); 
            $record->nameRU_pre = $record->prepositional; unset($record->prepositional); 
            $record->wikiID = substr($record->wiki_entity, 1); unset($record->wiki_entity); 
*/
            $record->wiki_entity = intval(substr($record->wiki_entity, 1));
        }
        
        if($model == "App\Region") {
/*
            $record->countryID = $record->country_id;
            $record->nameEN = $record->name_en;
            $record->nameRU = $record->name_ru;
            
            unset($record->name_en);
            unset($record->name_ru); 
            unset($record->country_id);
*/ 
        }
        
        //
        if($model == "App\Country") {
/*
            $record->countryID = $record->id;
            $record->nameEN = $record->name_en;
            $record->nameRU = $record->name_ru;
            
            unset($record->id);
            unset($record->name_en);
            unset($record->name_ru);
*/  
        }
        
        return $record;
    }
   

    /**
     *  Фикс для начальных дат:
     *  UPDATE `cities_1000` SET `created_at`= '2021-08-31 00:00:00.000000',`updated_at`= '2021-08-31 00:00:00.000000'
     */
     
    /**
     *  @OA\Schema(
     *      schema="WikidataSchema",
     *              @OA\Property(property="cities", type="object", ref="#/components/schemas/CityUpdatesSchema"),
     *              @OA\Property(property="regions", type="object", ref="#/components/schemas/RegionUpdatesSchema"),
     *              @OA\Property(property="countries", type="object", ref="#/components/schemas/CountryUpdatesSchema"),
     *  )
     */
     
    /**
     *  @OA\Schema(
     *      schema="CityUpdatesSchema",
     *              @OA\Property(
     *                  property="bulk_create",
     *                  type="array",
     *                  @OA\Items(
     *                       @OA\Property(property="id", type="int32", example=1),
     *                       @OA\Property(property="longitude", type="float", example="42.00001"),
     *                       @OA\Property(property="latitude", type="float", example="42.00001"),
     *                       @OA\Property(property="population", type="int32", example=2000000),
     *                       @OA\Property(property="iata", type="string", example="MOW"),
     *                       @OA\Property(property="region_id", type="int32", example=1),
     *                       @OA\Property(property="country_code", type="string", example="RU"),
     *                       @OA\Property(property="name_en", type="string", example="Moscow"),
     *                       @OA\Property(property="name_ru", type="string", example="Москва"),
     *                       @OA\Property(property="genitive", type="string", example="Москвой"),
     *                       @OA\Property(property="dative", type="string", example="Москве"),
     *                       @OA\Property(property="accusative", type="string", example="Москву"),
     *                       @OA\Property(property="instrumental", type="string", example="Москвой"),
     *                       @OA\Property(property="prepositional", type="string", example="Москва"),
     *                       @OA\Property(property="wiki_entity", type="int32", example=1),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="bulk_edit",
     *                  type="array",
     *                  @OA\Items(
     *                       @OA\Property(property="id", type="int32", example=1),
     *                       @OA\Property(property="longitude", type="float", example="42.00001"),
     *                       @OA\Property(property="latitude", type="float", example="42.00001"),
     *                       @OA\Property(property="population", type="int32", example=2000000),
     *                       @OA\Property(property="iata", type="string", example="MOW"),
     *                       @OA\Property(property="region_id", type="int32", example=1),
     *                       @OA\Property(property="country_code", type="string", example="RU"),
     *                       @OA\Property(property="name_en", type="string", example="Moscow"),
     *                       @OA\Property(property="name_ru", type="string", example="Москва"),
     *                       @OA\Property(property="genitive", type="string", example="Москвой"),
     *                       @OA\Property(property="dative", type="string", example="Москве"),
     *                       @OA\Property(property="accusative", type="string", example="Москву"),
     *                       @OA\Property(property="instrumental", type="string", example="Москвой"),
     *                       @OA\Property(property="prepositional", type="string", example="Москва"),
     *                       @OA\Property(property="wiki_entity", type="int32", example=1),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="bulk_delete",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property( 
     *                           property="id", type="int32", example=1 
     *                      )
     *                  )
     *              )
     *  )
     */
      
    /**
     *  @OA\Schema(
     *      schema="RegionUpdatesSchema",
     *              @OA\Property(
     *                  property="bulk_create",
     *                  type="array",
     *                  @OA\Items(
     *                       @OA\Property(property="id", type="int32", example=1),
     *                       @OA\Property(property="name_en", type="string", example="Moscow"),
     *                       @OA\Property(property="name_ru", type="string", example="Москва"),
     *                       @OA\Property(property="country_id", type="int32", example=1)
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="bulk_edit",
     *                  type="array",
     *                  @OA\Items(
     *                       @OA\Property(property="id", type="int32", example=1),
     *                       @OA\Property(property="name_en", type="string", example="Moscow"),
     *                       @OA\Property(property="name_ru", type="string", example="Москва"),
     *                       @OA\Property(property="country_id", type="int32", example=1)
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="bulk_delete",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property( 
     *                           property="id", type="int32", example=1 
     *                      )
     *                  )
     *              )
     *  )
     */ 
      
    /**
     *  @OA\Schema(
     *      schema="CountryUpdatesSchema",
     *              @OA\Property(
     *                  property="bulk_create",
     *                  type="array",
     *                  @OA\Items(
     *                       @OA\Property(property="id", type="int32", example=1),
     *                       @OA\Property(property="name_en", type="string", example="Russia"),
     *                       @OA\Property(property="name_ru", type="string", example="Россия"),
     *                       @OA\Property(property="code", type="string", example="RU")
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="bulk_edit",
     *                  type="array",
     *                  @OA\Items(
     *                       @OA\Property(property="id", type="int32", example=1),
     *                       @OA\Property(property="name_en", type="string", example="Russia"),
     *                       @OA\Property(property="name_ru", type="string", example="Россия"),
     *                       @OA\Property(property="code", type="string", example="RU")
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="bulk_delete",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property( 
     *                           property="id", type="int32", example=1 
     *                      )
     *                  )
     *              )
     *  )
     */ 
}