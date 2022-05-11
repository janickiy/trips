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
use morphos\Russian\GeographicalNamesInflection;

use Wikidata\Wikidata;
use App\Cities1000;


class ParseCityController extends Controller
{
    public $request = null;
    public $sql = null;
    
    public function __construct()
    {
        $this->endpoint = 'https://query.wikidata.org';
        $this->format = 'json';
        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->endpoint]);
        
        $this->cases = [
          "genitive" => "родительный",
          "dative" => "дательный",
          "accusative" => "винительный",
          "instrumental" => "творительный",
          "prepositional" => "предложный",
        ];  
    }
    
    public function getCitiesByQEntity(Request $request)
    {
        $this->request = $request;

        if($this->request->has('q')) {
            
            $array = explode(',', $this->request->q); 
            Log::info("-----");
            Log::info("getCitiesByQEntity query", [$this->request->toArray()]);
            
            $sql = $this->prepareCitiesQuery($array);
            
            if($this->request->has('update')) {
                if($this->request->update == 'yes') {
                    $this->updateCity($sql, 'yes');
                }
            }

        }
        
    }
    
    public function prepareCitiesQuery($array)
    {
        $string = '';
        
        foreach($array as $qEntity) {
            $string .= '( wd:'.$qEntity.' "'.$qEntity.'" ) ';
        }
        
        
            
    $sql =        
'SELECT * WHERE
{
  
  VALUES (?value ?entity) { '. $string .' }
  
  OPTIONAL { ?value  wdt:P1082 ?population. }
  OPTIONAL { ?value  rdfs:label ?name_ru filter (lang(?name_ru) = "ru"). }
  OPTIONAL { ?value  rdfs:label ?name_en filter (lang(?name_en) = "en"). }
  
  OPTIONAL {
     ?value schema:description ?description_ru FILTER (lang(?description_ru) = "ru").
  }
  
  OPTIONAL {
     ?value schema:description ?description_en FILTER (lang(?description_en) = "en").
  }
  
  SERVICE wikibase:label {
      bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". 
  }
  
}';
        
        Log::info("prepareCitiesQuery", [$sql]);
        return $sql;
    }
    
    public function updateCity($sql, $update)
    {
        $response = $this->wikidataRequest($sql); 
        Log::info("wikidata response", [$response]);
        
        if(is_array($response)) {
            
            $items = $response['results']['bindings'];
            
            if($update == 'yes') {
                
                foreach($items as $item) {
                    
                    // dump($item);
                    
                    if(isset($item['entity'])) {
                        
                        if($update == 'yes') {
                            // dump($item['entity']['value']);
                            
                            if($this->request->has('city_id')) {
                                $city = Cities1000::where('id', $this->request->city_id)->first();
                            } else {
                                $city = Cities1000::where('wiki_entity', $item['entity']['value'])->first();
                            }
                            
                            Log::info("old city value", [$city]);
                            
                            if($city != null) {
                                  
                               $city->wiki_entity = $item['entity']['value'];
                                  
                               //
                               if(isset($item['population'])) {
                                   $city->population = $item['population']['value'];
                               }
                               
                               // Название:
                               if(isset($item['name_ru'])) {
                                   $city->name_ru = $item['name_ru']['value'];
                               }
                               
                               
                               if(isset($item['name_en'])) {
                                   $city->name = $item['name_en']['value'];
                               }
                               
                               // Описание:
                               if(isset($item['description_ru'])) {
                                   $city->description_ru = $item['description_ru']['value'];
                               }
                               
                               //
                               if(isset($item['description_en'])) {
                                   $city->description = $item['description_en']['value'];
                               }

                               // TODO: Падежи
                                // Проверяем склоняемо ли имя:
                               if($city->name_ru != null) {
                                    if(GeographicalNamesInflection::isMutable(($city->name_ru))) {
                                   
                                        foreach($this->cases as $case => $caseRU) {
                                            $city->$case = GeographicalNamesInflection::getCase($city->name_ru, $caseRU);
                                        }
                                        
                                    } else {
                                        // Если не склоняемо:
                                        foreach($this->cases as $case => $caseRU) {
                                            $city->$case = $city->name_ru;
                                        }
                                    
                                    }
                               }
                               
                               
                               
                               $city->ru_name_check = 1;
                               $city->save();
                               
                               Log::info("new city value", [$city]);
                            }
                            
                            
                        }
                    }
                }
            
            } else {
                return $item;
            }
        }   
    }
    

    /**
     *  Запрос данных:
     */
    public function wikidataRequest($query)
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
    
}
