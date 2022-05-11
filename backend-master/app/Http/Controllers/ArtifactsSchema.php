<?php 

namespace App\Http\Controllers;

use Log;

/*
  "property", "ref", "schema", "title", "description", "maxProperties", "minProperties", "required", "properties", "type", "format", "items", "collectionFormat", "default", "maximum", "exclusiveMaximum", "minimum", "exclusiveMinimum", "maxLength", "minLength", "pattern", "maxItems", "minItems", "uniqueItems", "enum", "multipleOf", "discriminator", "readOnly", "writeOnly", "xml", "externalDocs", "example", "nullable", "deprecated", "allOf", "anyOf", "oneOf", "not", "additionalProperties", "additionalItems", "contains", "patternProperties", "dependencies", "propertyNames", "const", "x"
*/
class ArtifactsSchema extends Controller
{
     
    /**
     * @OA\Schema(
     *   schema="Типы артефактов",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="trip", type="integer", default=1, description="Поездка."),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="city", type="integer", default=2, description="Город."),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="file", type="integer", default=3, description="Файл."),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="note", type="integer", default=4, description="Заметка."),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="link", type="integer", default=5, description="Ссылка."),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="transfer", type="integer", default=6, description="Перемещение."),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="booking", type="integer", default=7, description="Бронирование."),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="note_photo", type="integer", default=8, description="Изображение в заметке."),
     *     ),
     *     @OA\Schema(
     *       @OA\Property(property="flight", type="integer", default=9, description="Перелет."),
     *     ),
     *   }
     * )
     */
    private static $types = [
        'trip' => 1,
        'city' => 2,
        'file' => 3,
        'note' => 4,
        'link' => 5,
        'transfer' => 6,
        'booking' => 7,
        'note_photo' => 8, // фотографии в заметка
        'flight' => 9, // перелет
    ];    

    /**
     * @OA\Schema(
     *   schema="Атрибуты артефакта trip",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="name", type="string", nullable=true, maxLength=190, description="Название поездки."),
     *       @OA\Property(property="description", type="string", nullable=true,  description="Описание поездки."),
     *     ),
     *   }
     * )
     */
    private static $tripAttributes = [
        'name'
    ];
     
     
    /**
     * @OA\Schema(
     *   schema="Атрибуты артефакта city",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="city_id", type="integer", description="Идентификатор города из базы данных городов."),
     *       @OA\Property(property="departure_date", type="integer", nullable=true, description="Unix метка времени отправления."),
     *       @OA\Property(property="arrival_date", type="integer", nullable=true, description="Unix метка времени прибытия."),
     *     ),
     *   }
     * )
     */
    private static $cityAttributes = [
        'city_id'
    ];
     
     
    /**
     * @OA\Schema(
     *   schema="Атрибуты артефакта file",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="title", type="string", nullable=true, maxLength=190, description="Название файла."),
     *       @OA\Property(property="upload_is_complete", type="integer", enum={"0", "1"}, description="Этот параметр устанавливает сервер. По-умолчанию равен 0 и означает, что клиент инициировал загрузку, но файл еще не загружен в облако. 1 означает, что файл загружен в облако и его можно скачивать."),
     *     ),
     *   }
     * )
     */
     
     
    private static $fileAttributes = [
        'title',
        'link',
    ];
     
    /**
     * @OA\Schema(
     *   schema="Атрибуты артефакта note",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="text", type="string", nullable=true, description="Текст заметки."),
     *       @OA\Property(property="is_trip_description", type="boolean", nullable=false, default=false),
     *     ),
     *   }
     * )
     */
    private static $noteAttributes = [
        'text',
		'is_trip_description'
    ];
     
    /**
     * @OA\Schema(
     *   schema="Атрибуты артефакта link",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="title", type="string", nullable=true, maxLength=190, description="Название ссылки."),
     *       @OA\Property(property="link", type="string", nullable=true, maxLength=190, description="Адрес ссылки."),
     *     ),
     *   }
     * )
     */
    private static $linkAttributes = [
        'title',
        'link',
    ];
    
    /**
     * @OA\Schema(
     *   schema="Атрибуты артефакта transfer",
     *   allOf={
     *     @OA\Schema(
     *          @OA\Property(property="category_id", type="integer", nullable=true, description="Категория билета: null - неизвестная категория, 1 - авиа,  2 - жд,  3 - авто, 4 - паром.", enum={"null", "1", "2", "3", "4"}),
     *          @OA\Property(property="departure_at", type="integer", nullable=true, description="Unix метка времени отправления."),
     *          @OA\Property(property="arrival_at", type="integer", nullable=true, description="Unix метка времени прибытия."),
     *     ),
     *   }
     * )
     */   
    private static $transferAttributes = [
        'category_id',
        'departure_at',
        'arrival_at',

    ];
    
    /**
     * @OA\Schema(
     *   schema="Атрибуты артефакта booking",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="name", type="string", nullable=true, maxLength=190, description="Название бронирования."),
     *       @OA\Property(property="address", type="string", nullable=true, maxLength=190, description="Адрес бронирования."),
     *       @OA\Property(property="latitude", type="number", nullable=true, description="Широта."),
     *       @OA\Property(property="longitude", type="number", nullable=true, description="Долгота."),
     *     ),
     *   }
     * )
     */
    private static $bookingAttributes = [
        'name',
        'address',
        'latitude',
        'longitude',
    ];
    
    
    /**
     * @OA\Schema(
     *   schema="Атрибуты артефакта note_photo",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="title", type="string", nullable=true, maxLength=190, description="Название файла."),
     *       @OA\Property(property="upload_is_complete", type="integer", enum={"0", "1"}, description="Этот параметр устанавливает сервер. По-умолчанию равен 0 и означает, что клиент инициировал загрузку, но файл еще не загружен в облако. 1 означает, что файл загружен в облако и его можно скачивать."),
     *     ),
     *   }
     * )
     */
    private static $note_photoAttributes = [
        'title',
        'link',
    ];
    
    /**
     * @OA\Schema(
     *   schema="Атрибуты артефакта flight",
     *   allOf={
     *     @OA\Schema(
     *          @OA\Property(property="title", type="string", nullable=true, maxLength=190, description="Название билета."),
     *          @OA\Property(property="departure_city_id", type="integer", nullable=true, description="Идентификатор города отправления из базы данных городов."),
     *          @OA\Property(property="arrival_city_id", type="integer", nullable=true, description="Идентификатор города прибытия из базы данных городов."),
     *          @OA\Property(property="departure_iata_code", type="string", nullable=true, maxLength=190, description="IATA города отправления."),
     *          @OA\Property(property="arrival_iata_code", type="string", nullable=true, maxLength=190, description="IATA города прибытия."),
     *          @OA\Property(property="flight_number", type="string", nullable=true, description="Номер рейса."),
     *          @OA\Property(property="carrier", type="string", nullable=true, maxLength=190, description="Перевозчик.")
     *     )
     *   }
     * )
     */
    private static $flightAttributes = [
        'title',
        'departure_city_id',
        'arrival_city_id',
        'departure_iata_code',
        'arrival_iata_code',
        'flight_number',
        'carrier',
    ];
    
    /**
     * Получить тип артефакта:
     */
    public static function getType($name) 
    {
        if(isset(self::$types[$name])) {
            return self::$types[$name];
        } else {
            Log::warning('Для указанного артефакта не задан type в ArtifactsSchema!', [$name]);
            return false;
        }
    }
     
    /**
     * Получить атрибуты артефакта:
     */
    public static function getAllowedAttributes($name) 
    {
        if(ArtifactsSchema::getType($name)) {
            $property = $name . 'Attributes';
            
            if(isset(self::$$property)) {
                return self::$$property;
            }
            
            Log::warning('Для указанного артефакта не заданы атрибуты в ArtifactsSchema!', [$name]);
            
        }
        
        return false;
    }

    public function index() 
    {
        $array = [];
        
        foreach(ArtifactsSchema::$types as $artifactName => $artifactType) {
            $array[$artifactName] = [
                'atrifact_type' => ArtifactsSchema::getType($artifactName),
                'atrifact_attributes' => ArtifactsSchema::getAllowedAttributes($artifactName),
            ];
        }
        
        return response()->json($array);
    }
    
    
}


