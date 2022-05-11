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
use Wikidata\Wikidata;
use App\Cities1000;

class WikiDataController extends Controller
{

    public function __construct()
    {
        $this->endpoint = 'https://query.wikidata.org';
        $this->format = 'json';
        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->endpoint]);
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
    
    public function test(Request $request)
    {
        $wikidata = new Wikidata();
        /*
        $results = $wikidata->search('Berlin', 'en', 1000);
        dd($results);
        */
        /*
        $entity = $wikidata->get('Q510357');
        $properties = $entity->properties->toArray();
        dd($properties);
        */
        
        //$results = $wikidata->searchBy('P642', 'Q1490', 'ru', 100);
        //$results = $wikidata->searchBy('Q1490', 'ru', 100);
        
        //$results = $wikidata->get('Q1490', 'ru');
        $results = $wikidata->get('Q100002', 'ru');
        
        
        
        dd($results->label, $results);
        
    }
    
    
    public function getCitiesByQEntity(Request $request)
    {
        
        if($request->has('q')) {
            
            $array = explode(',', $request->q); Log::info("getCitiesByQEntity query", [$request->toArray()]);
            $sql = $this->prepareCitiesQuery($array);
            
            if($request->update == 'yes') {
                $this->updateCity($sql, 'yes');
            }

        }
        
    }
    
    public function updateCity($sql, $update)
    {
        $response = $this->request($sql);
        
        if(is_array($response)) {
            
            $items = $response['results']['bindings'];
            
            if($update == 'yes') {
                
                foreach($items as $item) {
                    
                    // dump($item);
                    
                    if(isset($item['entity'])) {
                        
                        if($update == 'yes') {
                            // dump($item['entity']['value']);
                            //Log::info("wikidata entity", [$item]);
                            $city = Cities1000::where('wiki_entity', $item['entity']['value'])->first();
                            
                            Log::info("old city value", [$city]);
                            
                            if($city != null) {
                                                        
                               //
                               if(isset($item['population'])) {
                                   $city->population = $item['population']['value'];
                               }
                               
                               //
                               if(isset($item['name_ru'])) {
                                   $city->name_ru = $item['name_ru']['value'];
                               }
                               
                               //
                               if(isset($item['name_en'])) {
                                   $city->name = $item['name_en']['value'];
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
  
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
  
}';
        
        Log::info("prepareCitiesQuery", [$sql]);
        return $sql;
    }
    
    
    public function getNameRuNull(Request $request)
    {
        $cities = Cities1000::whereNotNull('wiki_entity')->whereNull('name_ru')->where('ru_name_check', 0)->orderBy('population', 'desc')->paginate(100);
        
        $array = [];
        
        foreach($cities as $city) {
            $array[] = $city->wiki_entity;
            echo $city->wiki_entity . '<br>';
        }

        $sql = $this->prepareCitiesQuery($array);
        $this->updateCity($sql, 'yes');
        
        return count($cities);

    }
    
}

