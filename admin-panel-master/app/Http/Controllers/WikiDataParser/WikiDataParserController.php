<?php

namespace App\Http\Controllers\WikiDataParser;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use App\City;
use Carbon\Carbon;

use App\Http\Controllers\MorphosController;
use App\Http\Controllers\WikidataCityParserLogController;
use morphos\Russian\GeographicalNamesInflection;

/*
    Метод:
    api/download_city_from_wiki_data?delta_longitude=0.1&delta_latitude=0.2&city_id=16360&show_results=true
    api/download_city_from_wiki_data?delta_longitude=0.1&delta_latitude=0.2&city_id=83068
    
    Визуализация:
    http://wikidata.com.yy/cp/wikidata_city_parser_full
*/


class WikiDataParserController extends Controller
{
    /**
     *  Здесь собираются все найденные результаты поиска:
     */
    public $wikiEntities = [];
    
    /**
     *  Вероятности:
     */
    public $wikiEntitiesProbabilities = [];
    
    /**
     *  Запросы
     */
    public $quries = [
        'by_name_en' => '',
        'by_name_ru' => '',
        'by_geoname_id' => '',
    ];
    
    /**
     *  ID города из нашей базы данных, который обрабатывает парсер:
     */
    public $cityID = 16360; // Santiago
    public $city = null; // объект
    public $nameEN = null; // название на английском
    public $nameRU = null; // название на русском
    public $alterNamesEN = []; // другие возможные названия на английском
    public $alterNamesRU = []; // другие возможные названия на русском
    public $cases = []; // сгенерированные падежи
    
    public $longitude = null;
    public $latitude = null;
    public $population = 0;
    public $iata_code = null;
    public $country_code = null;
    public $geonameid = null;
    
    public $deltaLongitude = 0.1;
    public $deltaLatitude = 0.2;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->sparql = new SparqlController;
        
        // Устанавливаю максимальное отклонение координат:
        $this->deltaLongitude();
        $this->deltaLatitude();
        $this->getCityData();
        
        $this->client = new \GuzzleHttp\Client(['base_uri' => 'https://query.wikidata.org']);
    }

    /**
     *  Программа парсинга:
     */
    public function index()
    {
        $startTime = microtime(true);
        
        // Ищу в WikiData:
        $this->findByNameEN(); // Ищу по английскому названию
        $this->findByAlterNamesEN(); // Ищу по альтернативным английским названиям:
        $this->findByNameRU(); // Ищу по русскому названию
        $this->findByGeoNamesId(); // Ищу по GeoNames ID 
        $this->findByCoordinates(); // Ищу по координатам
      
        // Разбор результатов:
        $this->matchResults(); 
       
        $this->createCases();
      
        // echo $this->quries['by_name_en'];
        // echo $this->quries['by_geoname_id'];
        
        $this->queryTime = microtime(true) - $startTime;

        $result = $this->updateCityData();
  
        if($this->request->has('show_results')) {
            $this->showResults();
        } else {
            return response()->json($result); 
        }

    }
    
    public function getCityData()
    {
        if($this->request->has('city_id') and $this->request->city_id != null) {
            $this->cityID = $this->request->city_id;
        }
        
        $this->city = City::where('id', $this->cityID)->first();
        
        if($this->city != null) {
            
            $this->nameEN = $this->city->name;
            $this->nameRU = $this->city->name_ru;
            $this->longitude = $this->city->longitude;
            $this->latitude = $this->city->latitude;
            $this->population = $this->city->population;
            $this->iata_code = $this->city->iata_code;
            $this->country_code = $this->city->country_code;
            $this->geonameid = $this->city->geonameid;
            $this->alterNamesEN = explode(',', $this->city->alternatenames);
            
            // Формирую запросы:
            $this->quries = [
                'by_name_en' => $this->sparql->searchByNameEN($this->nameEN, $this->longitude, $this->latitude, 'en'),
                'by_name_ru' => $this->sparql->searchByNameEN($this->nameRU, $this->longitude, $this->latitude, 'ru'),
                'by_geoname_id' => $this->sparql->searchByGeoID([$this->geonameid]),
            ];
  
        }
    }
    
    /**
     *  Запрос к серверу WikiData:
     */
    public function request($query)
    {
        $response = $this->client->request(
            'GET', 
            'sparql?format=json&query=' . urlencode($query),
             [
                'allow_redirects' => true,
                'timeout' => 2000,
                'http_errors' => false
            ]
        );

        if($response->getStatusCode() == 200) {
            $response = json_decode($response->getBody(), true); 
            
            // Если есть результаты:
            if(isset($response['results'])) {
                
                if(isset($response['results']['bindings'])) {
                    
                    // И их больше, чем 0:
                    if(count($response['results']['bindings']) > 0) {
                        
                        $array = $response['results']['bindings'];
                        
                        // Добавляю их к текущему городу:
                        foreach($array as $item) {
                            
                            // Объект из поисковой выдачи:
                            if(isset($item['item'])) {
                                
                                $parts = explode('/', $item['item']['value']);
                                $entity = $parts[count($parts) - 1];
                                
                                // Если его еще нет, добавляю к результатам:
                                if(!isset($this->wikiEntities[$entity])) { 
                                    $this->wikiEntities[$entity] = $this->getObjectData($item);
                                }                                
                                
                            }
                            
                        } // объекты из поисковой выдачи
                        
                    } // <- Если результатов > 0
                    
                } // <- Если есть bindings
                
            } // <- Если есть результаты
            
            return true;
            
        } else {
            
            return false;
            
        }          
    }
    
    /**
     *  Обработка объекта из выдачи:
     */
    public function getObjectData($array)
    {
        $fields = [
            'name_en',
            'name_ru',
            'description_en',
            'description_ru',
            'latitude',
            'longitude',
            'population',
            'geoname_id'

        ];
        
        $result = [];
        
        foreach($fields as $field) {
            if(isset($array[$field])) {
                $result[$field] = $array[$field]['value'];
            } else {
                $result[$field] = false;
            }
        }
        
        return $result;

    }
    
    /**
     *  Создаем падежи, если их нет:
     */
    public function createCases()
    {
        if($this->nameRU != null) {
            $morphos = new MorphosController;
            
            foreach($morphos->cases as $case => $caseRU) {
                $this->cases[$case] = GeographicalNamesInflection::getCase($this->nameRU, $caseRU);
            }
        }
    }
    
    /**
     *  Устанавливаю максимальное отклонение по долготе:
     */
    public function deltaLongitude()
    {
        if($this->request->has('delta_longitude') and $this->request->delta_longitude != null) {
            $this->deltaLongitude = $this->request->delta_longitude;
        } 
    }
    
    /**
     *  Устанавливаю максимальное отклонение по широте:
     */
    public function deltaLatitude()
    {
        if($this->request->has('delta_latitude') and $this->request->delta_latitude != null) {
            $this->deltaLatitude = $this->request->delta_latitude;
        } 
    }
    
    /**
     *  Поиск в WikiData по GeoNames ID:
     */
    public function findByGeoNamesId()
    {
        $response = $this->request($this->quries['by_geoname_id']);
    }
    
    /**
     *  Поиск в WikiData по английскому названию:
     */
    public function findByNameEN()
    {
        $response = $this->request($this->quries['by_name_en']);
    }
    
    /**
     *  Поиск в WikiData по альтернативным английским названиям:
     */
    public function findByAlterNamesEN()
    {
        // TODO: $this->wikiEntities = [];
    }
    
    /**
     *  Поиск в WikiData по русскому названию:
     */
    public function findByNameRU()
    {
        $response = $this->request($this->quries['by_name_ru']);
    }
    
    /**
     *  Поиск в WikiData по координатах:
     */
    public function findByCoordinates()
    {
        // TODO: $this->wikiEntities = [];
    }
    
    /**
     *  Разбор результатов:
     */
    public function matchResults()
    {
        foreach($this->wikiEntities as $entity => $params) {
            $this->matchGeoNameID();
            $this->matchNameEN();
            $this->matchNameRU();
            $this->matchPopulation();
            $this->matchCoordinates();
        }
        
        $this->matchProbability();
    }
    
    public function matchGeoNameID()
    {
        foreach($this->wikiEntities as $entity => $params) {
            if($this->geonameid > 0 and $this->geonameid == $this->wikiEntities[$entity]['geoname_id']) {
                $this->wikiEntitiesProbabilities[$entity]['geoname_id'] = true;
            } else {
                $this->wikiEntitiesProbabilities[$entity]['geoname_id'] = false;
            }
        }
    }
    
    public function matchNameEN()
    {
        foreach($this->wikiEntities as $entity => $params) {
            if($this->nameEN != null and $this->nameEN == $this->wikiEntities[$entity]['name_en']) {
                $this->wikiEntitiesProbabilities[$entity]['name_en'] = true;
            } else {
                $this->wikiEntitiesProbabilities[$entity]['name_en'] = false;
            }
        }
    }
    
    public function matchNameRU()
    {
        foreach($this->wikiEntities as $entity => $params) {
            if($this->nameRU != null and $this->nameRU == $this->wikiEntities[$entity]['name_ru']) {
                $this->wikiEntitiesProbabilities[$entity]['name_ru'] = true;
            } else {
                $this->wikiEntitiesProbabilities[$entity]['name_ru'] = false;
            }
        }
    }
    
    public function matchPopulation()
    {
        foreach($this->wikiEntities as $entity => $params) {
            if($this->population != false and $this->population >= 0) {
                if($this->wikiEntities[$entity]['population'] != false) {
                    // TODO: отклонение по населению
                    $this->wikiEntitiesProbabilities[$entity]['population'] = true;
                } else {
                    $this->wikiEntitiesProbabilities[$entity]['population'] = false;
                }
            } else {
                $this->wikiEntitiesProbabilities[$entity]['population'] = false;
            }
        }
    }
    
    public function matchCoordinates()
    {
        foreach($this->wikiEntities as $entity => $params) {
        
            // Если координаты города есть:
            if($this->longitude != false and $this->latitude != false) {
                
                // Если координаты объекта из выдачи есть:
                if($this->wikiEntities[$entity]['longitude'] != false and $this->wikiEntities[$entity]['latitude'] != false) {
                    
                    $deltaLongitude = abs(abs($this->longitude) - abs($this->wikiEntities[$entity]['longitude']));
                    $deltaLatitude = abs(abs($this->latitude) - abs($this->wikiEntities[$entity]['latitude']));
                    
                    if($deltaLongitude <= $this->deltaLongitude and $deltaLatitude <= $this->deltaLatitude) {
                        $this->wikiEntitiesProbabilities[$entity]['coordinates'] = true;
                    } else {
                        $this->wikiEntitiesProbabilities[$entity]['coordinates'] = false;
                    }
                    
                } else {
                    $this->wikiEntitiesProbabilities[$entity]['coordinates'] = false;
                }
            } else {
                $this->wikiEntitiesProbabilities[$entity]['coordinates'] = false;
            }
        }
    }
    
    public function showResults()
    {
        
        
        echo '<style>table { border-collapse: collapse; }table, th, td { border: 1px solid black; }
        table { width: 100%; } th, td { padding: 15px; text-align: center; }</style>';
        
        echo '<h2>';
        echo '<h2>' . $this->nameEN . ' / ' .  $this->nameRU;
        echo '</h2>';
        echo '<table>';
        echo '<tr>';
            
            echo '<th>Q-entity</th>';
        
        if(count($this->wikiEntitiesProbabilities) > 0) {
            foreach($this->wikiEntitiesProbabilities[array_keys($this->wikiEntitiesProbabilities)[0]] as $property => $probability) {
                echo '<th>'.$property.'</th>';
            }
        }
                        
        echo '</tr>';
        
        if(count($this->wikiEntitiesProbabilities) > 0) {
            foreach($this->wikiEntitiesProbabilities as $entity => $probabilities) {
                echo '<tr>';
                echo '<td><a href="https://www.wikidata.org/wiki/'.$entity.'" target="_blank">'.$entity.'</a></td>';
                
                foreach($probabilities as $property => $probability) {
                    if($probability == true) {
                        if(is_bool($probability)) {
                            echo '<td>true</td>';
                        } else {
                            echo '<td>'.$probability.'%</td>';
                        }
                    } else {
                        echo '<td>'.$probability.'</td>';
                    }
                }
                

                
                echo '</tr>';
            }
        }
        echo '</table>';
    }
    
    /**
     *  Подсчет вероятности совпадения:
     */
    public function matchProbability()
    {
        if(count($this->wikiEntitiesProbabilities) > 0) {
            $fieldsCount = count($this->wikiEntitiesProbabilities[array_keys($this->wikiEntitiesProbabilities)[0]]);
            
            foreach($this->wikiEntitiesProbabilities as $entity => $probabilities) {
                
                $trues = 0;
               
                foreach($probabilities as $property => $probability) {
                    if($probability == true) {
                        $trues++;
                    }
                }

                if($trues > 0) {
                    $this->wikiEntitiesProbabilities[$entity]['probability'] = ceil($trues / $fieldsCount * 100);
                } else {
                    $this->wikiEntitiesProbabilities[$entity]['probability'] = 0;
                }
               
            }
        }
    }
    
    /**
     *  Обновление данных:
     */
    public function updateCityData()
    {
        $entityToUpdate = $this->findEntityToUpdate();
        $updated_fields = [];
        
        $log = new WikidataCityParserLogController;
        
        // Если найдена 1 запись, то обновляю:
        if($entityToUpdate != false) {
            
            $updated_fields = $this->saveEmptyFields($entityToUpdate);
            
            $log->saveLog([
                'city_id' => $this->city->id,
                'category_id' => 200,
                'reason_id' => 101,
                'search_results' => '',
                'old_population' => 0,
                'query_time' => $this->queryTime,
            ]);
            
        } else {
            // Требуется модерация:
            $this->city->need_custom_moderation = 1;
            $this->city->save();
            
            $log->saveLog([
                'city_id' => $this->city->id,
                'category_id' => 200,
                'reason_id' => 102,
                'search_results' => '',
                'old_population' => 0,
                'query_time' => $this->queryTime,
            ]);
        }
        
        if($this->request->has('show_results')) {
            if($this->city->locked == 0) {
                //echo '<p>Данные в базе обновлены. ('.count($this->wikiEntitiesProbabilities).')</p>';
            } else {
                //echo '<p>Данные в базе НЕ обновлены. LOCKED == 1</p>';
            }
        } else {
            if($this->city->locked == 0) {
                return [
                    'status' => 200,
                    'probabilities' => $this->wikiEntitiesProbabilities,
                    'update_message' => 'Данные в базе обновлены',
                    'updated_fields' => $updated_fields,
                    'city' => $this->city,
                ];
            } else {
                return [
                    'status' => 200,
                    'probabilities' => $this->wikiEntitiesProbabilities,
                    'update_message' => 'Данные в базе не обновлены.',
                    'updated_fields' => $updated_fields,
                    'city' => $this->city,
                ];
            }
        }
    }
    
    /**
     *  Определяю какая запись больше всего соответствует:
     */
    public function findEntityToUpdate()
    {
        $count = count($this->wikiEntitiesProbabilities);
        
        // Если найден только 1 результат:
        if($count == 0) {
            return false;
        } else if($count == 1) {
            
            $entity = array_keys($this->wikiEntitiesProbabilities)[0];
            
            // Если вероятность не нулевая:
            if($this->wikiEntitiesProbabilities[$entity]['probability'] > 0) {
                if($this->wikiEntitiesProbabilities[$entity]['geoname_id'] == true) {
                    return $entity;
                }
            }
            
            return false;
            
        } else {
            // Если больше, чем 1 результат
            // TODO: взять тот результат, где вероятность больше
            $entitiesToSave = [];
            $maxProbability = 0;
            
            // Определяю максимальную вероятность:
            foreach($this->wikiEntitiesProbabilities as $entity => $probabilities) {
                if($probabilities['probability'] > 0) {
                    
                    if($probabilities['probability'] > $maxProbability) {
                        $maxProbability = $probabilities['probability'];
                    }
                    
                }
            }
            
            // Ищу записи с максимальной вероятностью:
            foreach($this->wikiEntitiesProbabilities as $entity => $probabilities) {
                if($probabilities['probability'] > 0) {
                    
                    if($probabilities['probability'] >= $maxProbability) {
                        $entitiesToSave[] = $entity;
                    }
                    
                }
            }
            
            // Если нет ни одной вероятности:
            if(count($entitiesToSave) == 0) {
                return false;
            } else if(count($entitiesToSave) == 1) {
                // Если осталась одна вероятность:
                return $entitiesToSave[0];
            } else {
                // Если несколько с одинаково максимальной вероятностью:
                $copyEntitiesToSave = $entitiesToSave;
                
                foreach($entitiesToSave as $tmpID => $entityTmp) {
                    // Проверяю по GeoNames ID:
                    if($this->wikiEntitiesProbabilities[$entityTmp]['geoname_id'] == false) {
                        unset($entitiesToSave[$tmpID]);
                    }
                }
                // Если осталась одна:
                if(count($entitiesToSave) == 1) {
                    $entitiesToSave = array_values($entitiesToSave);
                    return $entitiesToSave[0];
                } else {
                    // Иначе беру первую попавшуюся запись:
                    $entitiesToSave = array_values($entitiesToSave);
                    return $copyEntitiesToSave[0];
                }
                
            }

            return false;
        }

    }
    
    /**
     *  Сохраняю недостающие данные о городе:
     */
    public function saveEmptyFields($entityToUpdate)
    {
        $data = $this->wikiEntities[$entityToUpdate];
        $updated_fields = [];
        
        if($this->city->locked == 0) {
        
            if($this->city->wiki_entity == null) {
                $this->city->wiki_entity = $entityToUpdate;
                $updated_fields['wiky_entity'] = $entityToUpdate;
            }
            
            if($this->city->name == null and $data['name_en'] != false) {
                $this->city->name = $data['name_en'];
                $updated_fields['name_en'] = $data['name_en'];
            }
            
            if($this->city->name_ru == null and $data['name_ru'] != false) {
                $this->city->name_ru = $data['name_ru'];
                $updated_fields['name_ru'] = $data['name_ru'];
            }
            
            if($this->city->description == null and $data['description_en'] != false) {
                $this->city->description = $data['description_en'];
                $updated_fields['description_en'] = $data['description_en'];
            }
            
            if($this->city->description_ru == null and $data['description_ru'] != false) {
                $this->city->description_ru = $data['description_ru'];
                $updated_fields['description_ru'] = $data['description_ru'];
            }
            
            if($this->city->population == null and $data['population'] != false) {
                $this->city->population = $data['population'];
                $updated_fields['population'] = $data['population'];
            }
            
            foreach($this->cases as $caseName => $caseValue) {
                if($this->city->$caseName == null) {
                    $this->city->$caseName = $caseValue;
                    $updated_fields['cases'][$caseName] = $caseValue;
                }
            }
            
            $this->city->modification_date = Carbon::now();
            $this->city->save();
        
        } else {
            $updated_fields['locked'] = 1;
        }
        
        return $updated_fields;
        
    }
    
            
        
    
    
}
