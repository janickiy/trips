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


class SparqlController extends Controller
{
    /**
     *  Здесь собираются все найденные результаты поиска:
     */
    public $wikiEntities = [];

    
    public function __construct()
    {
    }

    public function index()
    {

    }
    
    public function searchByNameEN($cityName, $long, $lat, $lang = 'en')
    {
        
        return 
'SELECT ?item ?name_en ?name_ru ?population ?description_ru ?description_en ?latitude ?longitude ?geoname_id

 WHERE {
  
  ?item ?label "'.$cityName.'"@'.$lang.' ;
        rdfs:label ?city_name .
        FILTER(lang(?city_name) = "'.$lang.'")
        
' . $this->optionalFields() . ' 

  # Подставляю координаты города из нашей базы данных и фильтрую по радиусу ±10 км:
  FILTER (ABS(?longitude - '.$long.') < 0.1)
  FILTER (ABS(?latitude - '.$lat.') < 0.2) 

  SERVICE wikibase:label { 
    bd:serviceParam wikibase:language "en". 
  }
  
}
ORDER BY DESC (?population)
LIMIT 100';
    }
    
    
    public function searchByGeoID($array)
    {
        $string = '';
        if(count($array) > 0) {
            foreach($array as $geonamesID) {
                $string .= '("' . $geonamesID . '") ';
            }
            
return 
'SELECT ?item ?name_en ?name_ru ?population ?description_ru ?description_en ?latitude ?longitude ?geoname_id
WHERE 
{
  ?item wdt:P1566 ?value ;   
  VALUES (?value) {
    ' . $string . '
  }
  
' . $this->optionalFields() . '
  
}
LIMIT 100'; 

        } else {
            return '';
        }
        
        

    }
    
    
    /**
     *  Дополнительные поля:
     */
    public function optionalFields() {
   
    // TODO: взять код страны и IATA
   
    return   
  '
  OPTIONAL {
     ?item rdfs:label ?name_en FILTER (lang(?name_en) = "en").
  }
  
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
  
  OPTIONAL {
     ?item schema:description ?description_en FILTER (lang(?description_en) = "en").
  }
  
  OPTIONAL {
     ?item wdt:P1566 ?geoname_id
  }
  
  OPTIONAL {
     ?item p:P625 ?statement .
     ?statement psv:P625 ?coordinate_node . 
     ?coordinate_node wikibase:geoLatitude ?latitude . 
     ?coordinate_node wikibase:geoLongitude ?longitude .
  }';
    }

}
