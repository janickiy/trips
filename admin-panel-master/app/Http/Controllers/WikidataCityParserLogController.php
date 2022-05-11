<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use Carbon\Carbon;
use Asparagus\QueryBuilder;
use App\ParserParam;
use App\Country;
use App\CountryDuplicate;
use App\WikidataCityParserLog;
use App\City1000;

class WikidataCityParserLogController extends Controller
{
    /**
     *  Параметры:
     */
    public function __construct()
    {
        $this->endpoint = 'https://query.wikidata.org'; // https://en.wikibooks.org/wiki/SPARQL
        $this->format = 'json';
        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->endpoint]); // https://github.com/guzzle/guzzle  
         
         
        $this->errors = [
            500 => [
                1 => 'Ошибка в SPARQL запросе или на стороне WikiData.',
            ],
            200 => [
                0 => 'Информаця о городе обновлена!',
                1 => 'Нет results',
                2 => 'Нет bindings',
                3 => 'Ничего не найдено в wikidata.',
                4 => 'В WikiData отсутствует поле description eng',
                5 => 'В WikiData отсутствует поле label ru',
                6 => 'В WikiData отсутствует поле description ru',
                7 => 'Найдено > 1 записи. Сохраняю json для обработки.',
                8 => 'Не указано население. Сохраняю json для обработки.',
                
                101 => 'Информация о городе обновлена!',
                102 => 'Требуется модерация',
            ]
            
        ];
    }

    public function saveLog($array)
    {
        $log = new WikidataCityParserLog;

        $log->city_id = $array['city_id'];
        $log->category_id = $array['category_id'];
        $log->reason_id = $array['reason_id'];
        $log->search_results = $array['search_results'];
        $log->old_population = $array['old_population'];
        $log->query_time = $array['query_time'];
        $log->created_at = Carbon::now();
        $log->updated_at = Carbon::now();
        
        if(isset($array['population_change'])) {
            $log->population_change = $array['population_change'];
        }
        
        // Требуется ручная модерация города:
        if($log->category_id == 200) {
            if(in_array($log->reason_id ,[1, 2, 3, 7, 8, 102])) {
                $city = City1000::where('id', $array['city_id'])->first();
                $city->need_custom_moderation = 1;
                $city->save();
            }
        }
        
        
        $log->save();
    }
    
    public function error200($cityId, $reasonId, $queryTime)
    {
        $this->saveLog([
            'city_id' => $cityId,
            'category_id' => 200,
            'reason_id' => $reasonId,
            'search_results' => null,
            'old_population' => 0,
            'query_time' => $queryTime,
        ]); 
    }
    
    public function sendQuery($sparql)
    {

        
        // Если режим дебага выключен, делаем запрос к викидате:
        $response = $this->client->request(
            'GET', 
            'sparql?format=json&query=' . urlencode($sparql),
             [
                'allow_redirects' => true,
                'timeout' => 2000,
                'http_errors' => false
            ]
        );
        // Обработка ответа сервера:
        if($response->getStatusCode() == 200) {
            
            return [
                'status' => 200,
                'data' => json_decode($response->getBody(), true),
                'message' => $response->getStatusCode()
            ];
            
        } else {
            
            return [
                'status' => 500,
                'data' => json_encode([]),
                'message' => $response->getStatusCode(),
            ];            
        }
        
    }
    
    /**
     *  Запрос данных:
     */
    public function request($query)
    {
        $response = $this->client->request(
            'GET', 
            'sparql?format=' . $this->format . '&query=' . urlencode($query),
             [
                'allow_redirects' => true,
                'timeout' => 2000,
                'http_errors' => false
            ]
        );

        if($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true); 
        } else {
            return $response->getStatusCode();
        }        
        
    }
    
    public function pagination()
    {
        return 'LIMIT ' . $this->limit . ' OFFSET ' . $this->offset;
    }
    
    public function getGeoData($cityName, $long, $lat)
    {
        
        return 
'SELECT ?item ?itemLabel ?itemDescription ?description_ru ?name_ru ?name_ruDescription ?population ?long ?lat ?geonames_id WHERE {
  
  ?item ?label "'.$cityName.'"@en ;
        rdfs:label ?city_name .
        FILTER(lang(?city_name) = "en")
  OPTIONAL {
     ?item rdfs:label ?name_ru FILTER (lang(?name_ru) = "ru").
  }

  OPTIONAL {
     ?item wdt:P1082 ?population .  
  }
  
  OPTIONAL {
     ?item wdt:P1566 ?geonames_id .  
  }
  
  OPTIONAL {
     ?item schema:description ?description_ru FILTER (lang(?description_ru) = "ru").
  }
  
  ?item p:P625 ?statement .
  ?statement psv:P625 ?coordinate_node . 
  ?coordinate_node wikibase:geoLatitude ?lat . 
  ?coordinate_node wikibase:geoLongitude ?long . 

  # Подставляю координаты города из нашей базы данных и фильтрую по радиусу ±10 км:
  FILTER (ABS(?long - '.$long.') < 0.1)
  FILTER (ABS(?lat - '.$lat.') < 0.2) 

  SERVICE wikibase:label { 
    bd:serviceParam wikibase:language "en". 
  }
  
}
ORDER BY DESC (?population)
LIMIT 100';
    }
   
    public function getGeoDataWithGeoID($cityName, $long, $lat, $geoNameID)
    {
        
        return 
'SELECT DISTINCT ?item ?geoNamesID ?itemLabel ?itemDescription ?description_ru ?name_ru ?name_ruDescription ?population ?long ?lat WHERE 
{
  ?item wdt:P1566 ?geoNamesID;
        ?label "'.$cityName.'"@en ;
        rdfs:label ?city_name .
        FILTER(lang(?city_name) = "en")
  OPTIONAL {
     ?item rdfs:label ?name_ru FILTER (lang(?name_ru) = "ru").
  }

  OPTIONAL {
     ?item wdt:P1082 ?population .  
  }
  
  
  OPTIONAL {
     ?item schema:description ?description_ru FILTER (lang(?description_ru) = "ru").
  }
  
  ?item p:P625 ?statement .
  ?statement psv:P625 ?coordinate_node . 
  ?coordinate_node wikibase:geoLatitude ?lat . 
  ?coordinate_node wikibase:geoLongitude ?long . 

  # Подставляю координаты города из нашей базы данных и фильтрую по радиусу ±10 км:
  FILTER (ABS(?long - '.$long.') < 0.1)
  FILTER (ABS(?lat - '.$lat.') < 0.2) 
  FILTER (?geoNamesID >= "'. ($geoNameID - 10). '") 
  FILTER (?geoNamesID <= "'. ($geoNameID + 10). '") 
  
  SERVICE wikibase:label { 
    bd:serviceParam wikibase:language "en". 
  }
  
}
ORDER BY DESC (?population)
LIMIT 100';
    }
   
    public function parseCityFromWikiData(Request $request)
    {
        $city = City1000::where('id', $request->city_id)->first();
        if($city != null) {
            $code = 200;
            $data = $this->findExample($city->id);
        } else {
            $code = 500;
        }
        
        return response()->json([
            'status' => $code,
            'data' => $data,
        ]);
    }
    
    
    
   
    public function findExample($id = 28594)
    {
        $startTime = microtime(true); 

        $city = City1000::where('id', $id)->first();
        
        // TODO: $city->name
        // TODO: $city->without_diacritics
        
        $sparql = $this->getGeoData($city->name, $city->longitude, $city->latitude);
        $result = $this->sendQuery($sparql);
        
        $queryTime = microtime(true) - $startTime;
        
        if($result['status'] == 200) {
            
            if(!isset($result['data']['results'])) {
                // Ошибка:
                $this->error200($city->id, 1, $queryTime) ;
            } else {
                if(!isset($result['data']['results']['bindings'])) {
                    // Ошибка:
                    $this->error200($city->id, 2, $queryTime);  
                } else {

                    // Обработка результатов:
                    if(count($result['data']['results']['bindings']) > 0) {

                        $this->cityHandler($city->id, $queryTime, $result['data']['results']['bindings']);

                    } else {
                                                
                        // Ошибка:
                        $this->error200($city->id, 3, $queryTime);
                    }
                }
            }
            
        } else {

            // Ошибка на беке:
            $this->saveLog([
                'city_id' => $city->id,
                'category_id' => 500,
                'reason_id' => 1,
                'search_results' => null,
                'old_population' => 0,
                'query_time' => $queryTime,
            ]);
        }
        
        return $result;

    }
    
    public function parseCityFromWikiDataByGeoNamesID(Request $request)
    {
        $city = City1000::where('id', $request->city_id)->first();
        if($city != null) {
            $code = 200;
            $this->findByGeoNamesID($city->id);
        } else {
            $code = 500;
        }
        
        return response()->json([
            'status' => $code,
        ]);
    }
    
    public function findByGeoNamesID($id = 28594)
    {
        $startTime = microtime(true); 

        $city = City1000::where('id', $id)->first();
        $sparql = $this->getGeoDataWithGeoID($city->name, $city->longitude, $city->latitude, $city->geonameid);
        $result = $this->sendQuery($sparql);

        $queryTime = microtime(true) - $startTime;
        
        if($result['status'] == 200) {
            
            if(!isset($result['data']['results'])) {
                // Ошибка:
                $this->error200($city->id, 1, $queryTime) ;
            } else {
                if(!isset($result['data']['results']['bindings'])) {
                    // Ошибка:
                    $this->error200($city->id, 2, $queryTime);  
                } else {

                    // Обработка результатов:
                    if(count($result['data']['results']['bindings']) > 0) {

                        $this->cityHandler($city->id, $queryTime, $result['data']['results']['bindings']);

                    } else {
                        // Ошибка:
                        $this->error200($city->id, 3, $queryTime);
                    }
                }
            }
        } else {

            // Ошибка на беке:
            $this->saveLog([
                'city_id' => $city->id,
                'category_id' => 500,
                'reason_id' => 1,
                'search_results' => null,
                'old_population' => 0,
                'query_time' => $queryTime,
            ]);
        }

    }
    
    
    /*
        Обработчик результатов поиска из викидаты:
    */
    public function cityHandler($cityId, $queryTime, $array) 
    {
        $results = [];
        
        // Иду по найденным записям:
        foreach($array as $city) { 
            // Если у найденной записи есть население:
            if(isset($city['population'])) {
                
                $parts = explode('/', $city['item']['value']);
                
                // Добавляю в результаты:
                $results[$parts[count($parts) - 1]] = $city;
                
            } else {

                // Если не указано население?
                $parts = explode('/', $city['item']['value']);
                
                // Добавляю в результаты:
                $results[$parts[count($parts) - 1]] = $city;
            
            }
        }
        

        // Если результат только один:
        if(count($results) == 1) {
            
            foreach($results as $entity => $result) {
                
                // Сохраняю данные:
                $city = City1000::where('id', $cityId)->first();
                
                $old_population = $city->population;

                $parts = explode('/', $result['item']['value']);
                $city->wiki_entity = $parts[count($parts) - 1];

                // Существует ли поле описания:
                if(isset($result['itemDescription'])) {
                    $city->description = $result['itemDescription']['value'];
                } else {
                    $this->error200($city->id, 4, $queryTime);                 
                }
                
                // Существует ли русское название:
                if(isset($result['name_ru'])) {
                    $city->name_ru = $result['name_ru']['value'];
                } else {
                    $this->error200($city->id, 5, $queryTime);                 
                }
                
                // Существует ли русское поле описания:
                if(isset($result['description_ru'])) {
                    $city->description_ru = $result['description_ru']['value'];
                } else {
                    $this->error200($city->id, 6, $queryTime);                 
                }

                // Указано ли население?
                if(isset($result['population']['value'])) {
                    $city->population = $result['population']['value'];
                }
                
                $city->modification_date = Carbon::now();
                
                $city->save();
                
                $population_change = $city->population - $old_population;
                
                // Сохраняю лог:
                $this->saveLog([
                    'city_id' => $cityId,
                    'category_id' => 200,
                    'reason_id' => 0,
                    'search_results' => json_encode($result),
                    'old_population' => $old_population,
                    'population_change' => $population_change,
                    'query_time' => $queryTime,
                ]);
            
            }

        } else if (count($results) == 0) {
           
            // Если ни у одной записи не указано население:
            $this->saveLog([
                'city_id' => $cityId,
                'category_id' => 200,
                'reason_id' => 8,
                'search_results' => json_encode($array),
                'old_population' => 0,
                'query_time' => $queryTime,
            ]);
           
        } else {
            
            $city = City1000::where('id', $cityId)->first();
              

            // TODO: если больше, чем 1 результат
            if($city != null) { 

                foreach($results as $result) {
                  
                    // Существует ли поле geonames_id:
                    if(isset($result['geonames_id'])) {
                        
                        // Если существует:
                        if($result['geonames_id']['value'] == $city->geonameid) {
                            
                            // Сохраняю:
                            $old_population = $city->population;

                            $parts = explode('/', $result['item']['value']);
                            $city->wiki_entity = $parts[count($parts) - 1];

                            // Существует ли поле описания:
                            if(isset($result['itemDescription'])) {
                                $city->description = $result['itemDescription']['value'];
                            } else {
                                $this->error200($city->id, 4, $queryTime);                 
                            }
                            
                            // Существует ли русское название:
                            if(isset($result['name_ru'])) {
                                $city->name_ru = $result['name_ru']['value'];
                            } else {
                                $this->error200($city->id, 5, $queryTime);                 
                            }
                            
                            // Существует ли русское поле описания:
                            if(isset($result['description_ru'])) {
                                $city->description_ru = $result['description_ru']['value'];
                            } else {
                                $this->error200($city->id, 6, $queryTime);                 
                            }

                            // Указано ли население?
                            if(isset($result['population']['value'])) {
                                $city->population = $result['population']['value'];
                            }
                            
                            $city->modification_date = Carbon::now();
                            
                            $city->save();
                            
                            $population_change = $city->population - $old_population;
                            
                            // Сохраняю лог:
                            $this->saveLog([
                                'city_id' => $cityId,
                                'category_id' => 200,
                                'reason_id' => 0,
                                'search_results' => json_encode($result),
                                'old_population' => $old_population,
                                'population_change' => $population_change,
                                'query_time' => $queryTime,
                            ]);

                            break;
                            return true;
                        }
                        
                    }
                  
                }
                
                // Если результат не один:
                $this->saveLog([
                    'city_id' => $cityId,
                    'category_id' => 200,
                    'reason_id' => 7,
                    'search_results' => json_encode($results),
                    'old_population' => 0,
                    'query_time' => $queryTime,
                ]);
                        
                

            } else {
            

            
                // Если результат не один:
                $this->saveLog([
                    'city_id' => $cityId,
                    'category_id' => 200,
                    'reason_id' => 7,
                    'search_results' => json_encode($results),
                    'old_population' => 0,
                    'query_time' => $queryTime,
                ]);
            }
            
        }
  
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     *  Items list:
     */
    public function itemsList(Request $request)
    {
        if($request->has('q') and strlen($request->q) > 0) {
            $items = WikidataCityParserLog::where('city_id', $request->q)
            ->orderBy('id','desc')
            ->paginate(30);
        } else {
            $items = WikidataCityParserLog::orderBy('id','desc')
            ->paginate(30);
        }

        return view(
            'cp.WikidataCityParserLog.items_list',
            array(
                'items' => $items->appends($request->except('page')),
                'errors' => $this->errors,
            ), 
            compact('items')
        );
        
    }

    /**
     *  Add item:
     */
    public function addItem()
    {
        return view('cp.WikidataCityParserLog.add_item');
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {
        $item = new WikidataCityParserLog;
        $item->fill($request->all());
        
        $filesFields = array("");
        
        foreach($filesFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
            } 
        }

        $item->save();

        return redirect('cp/wikidata_city_parser_log/edit/'.$item->id)->with('item_created', __('wikidata_city_parser_log.item_created'));
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       $item = WikidataCityParserLog::where('id', $id)->first();
       
       if($item == null) {
            return redirect('cp/wikidata_city_parser_log');
       }
       
       return view('cp.WikidataCityParserLog.edit_item', compact('id','item'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {
        $filesFields = array("");
        
        $item = WikidataCityParserLog::where('id', $request->id)->first(); 
        
        if($item == null) {
            return redirect('cp/wikidata_city_parser_log');
        }

        if($item != null) {
            
            // DELETE:
            if(isset($request->delete)) {
                
                // delete files:
                foreach($filesFields as $fileField) {
                    if(Storage::disk('public')->delete($item->$fileField)) {
                        $item->$fileField = null;
                    }
                }
                
                $item->delete();
                return redirect('cp/wikidata_city_parser_log')->with('deleted_item', __('wikidata_city_parser_log.item_deleted'));
            }
            
            // UPDATE:
            if(isset($request->submit)) {
             
                $item->fill($request->all());
                
                // upload files:
                foreach($filesFields as $fileField) {
                    if ($request->hasFile($fileField)) {
                        $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
                    } 
                }
                
                // delete files:
                foreach($filesFields as $fileField) {
                    if ($request->has('delete_' . $fileField)) {
                        if(Storage::disk('public')->delete($item->$fileField)) {
                            $item->$fileField = null;
                        }
                    } 
                }

                $item->save();
                return redirect('cp/wikidata_city_parser_log/edit/'.$item->id)->with('update_item', __('wikidata_city_parser_log.item_updated'));
            }
            
        }
        
        // ERROR:
        return redirect('cp/wikidata_city_parser_log')->with('not_found', __('wikidata_city_parser_log.item_not_found'));
 
    }
    
    /**
     *  Edit item:
     */
    public function geodataCitiesWikidata()
    {
       $lastLogId = WikidataCityParserLog::orderBy('id', 'desc')->first();
       $errors = $this->errors;
       
       
       return view('cp.WikidataCityParserLog.geodata_cities_parse_from_wikidata', compact('lastLogId', 'errors'));
    }
    
    public function geodataCitiesWikidataByGeoID()
    {
       $lastLogId = WikidataCityParserLog::orderBy('id', 'desc')->first();
       $errors = $this->errors;
       
       
       return view('cp.WikidataCityParserLog.geodata_cities_parse_from_wikidata_by_geo_id', compact('lastLogId', 'errors'));
    }
    
    
    public function parserCitiesInfo()
    {
        return response()->json([
            'downloaded-count' => City1000::whereNotNull('wiki_entity')->count(),
            'cities-count' => City1000::count(),
            'on-moderation-count' => City1000::where('need_custom_moderation', 1)->where('custom_edited', 0)->count()
        ]);
    }
    
    public function parserGetCityInWork()
    {
        return response()->json([
            'currentcity' => City1000::whereNull('wiki_entity')
                ->where('custom_edited', 0)
                ->where('need_custom_moderation', 0)
                ->orderBy('population', 'desc')
                ->first(),
            'currentstartdate' => Carbon::now()->format('Y/m/d H:i:s'),
        ]);
    }
   
    public function get_wikidata_city_parser_logs(Request $request)
    {
        return response()->json(WikidataCityParserLog::with('city')->where('id', '>', $request->lastLogId)->orderBy('id', 'asc')->get());
    }
    
    
    
    /**
     *  wikidata_city_parser_full
     */
    public function wikidata_city_parser_full()
    {
       $lastLogId = WikidataCityParserLog::orderBy('id', 'desc')->first();
       $errors = $this->errors;

       return view('cp.WikidataCityParserLog.wikidata_city_parser_full', compact('lastLogId', 'errors'));
    }
    
}

/*
SELECT ?item ?itemLabel ?mother ?motherLabel ?ISNI WHERE {
  VALUES ?ISNI { "0000 0001 2281 955X" "0000 0001 2276 4157" }
  ?item wdt:P213 ?ISNI.
  OPTIONAL { ?item wdt:P25 ?mother. }
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
}

*/

