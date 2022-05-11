<?php 
/*
|-------------------------------------------------------------
| Author: Vlad Salabun 
| Site: https://salabun.com 
| Contacts: https://t.me/vlad_salabun | vlad@salabun.com 
|-------------------------------------------------------------
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;

use App\Country;
use App\Cities1000;
use App\Travelpayouts;

use Carbon\Carbon;


class TravelPayoutsController extends Controller
{
	public function __construct() 
	{
		$this->endPoint = 'https://api.travelpayouts.com/data';
        $this->lang = 'ru';
		$this->citiesJson = 'cities.json';
		$this->countriesJson = 'countries.json';
        
        $this->citiesLogs = [];
        
        $this->cases = [
            "vi" => "", // куда? - в Осло
            "tv" => "instrumental", // кем, чем? - Москвой
            "ro" => "genitive", // кого, чего? - Москвы
            "pr" => "", // о ком, о чем? - Москве
            "da" => "dative", // кому, чему? - Москве
        ];
        /*
        $this->cases_ru = [
            "genitive" => "", // 
            "dative" => "",
            "accusative" => "",
            "instrumental" => "",
            "prepositional" => "",
        ];*/
	}

	public function countriesParser(Request $request) 
	{
		$client = new \GuzzleHttp\Client(['base_uri' => implode([ $this->endPoint, 'ru', $this->countriesJson ], '/')]);

        $response = $client->request(
            'GET', 
            '',
             [
                'allow_redirects' => true,
                'timeout' => 2000,
                'http_errors' => false
            ]
        );
        
        if($response->getStatusCode() == 200) {
            
            // Читаю JSON:
            $countriesArray = json_decode($response->getBody(), true);
            
            // Иду по странам:
            foreach($countriesArray as $countryItem) {
                
                // Есть ли в базе уже такая страна:
                $country = Country::where('code', $countryItem['code'])->get();
                
                // Если нет:
                if(count($country) == 0) {
                    
                    $country = new Country;
                   
                    $country->currency = $countryItem['currency'];
                    $country->code = $countryItem['code'];
                    
                    $country->vi = $countryItem['cases']['vi'];
                    $country->tv = $countryItem['cases']['tv'];
                    $country->ro = $countryItem['cases']['ro'];
                    $country->pr = $countryItem['cases']['pr'];
                    $country->da = $countryItem['cases']['da'];
                    
                    $country->name_en = $countryItem['name_translations']['en'];
                    $country->updated_at = Carbon::now();
                    
                    $country->save();   

                } else if(count($country) == 1) {
                    
                    // echo '<p>-- обновляю страну: '.$countryItem['name'].'</p>';
                    
                    $country[0]->currency = $countryItem['currency'];
                    $country[0]->code = $countryItem['code'];
                    
                    $country[0]->vi = $countryItem['cases']['vi'];
                    $country[0]->tv = $countryItem['cases']['tv'];
                    $country[0]->ro = $countryItem['cases']['ro'];
                    $country[0]->pr = $countryItem['cases']['pr'];
                    $country[0]->da = $countryItem['cases']['da'];
                    
                    $country[0]->name_en = $countryItem['name_translations']['en'];
                    $country[0]->updated_at = Carbon::now();
                    
                    $country[0]->save();
                    
                    
                } else {
                    echo '<p>Больше, чем 1 страна с таким названием: '.$countryItem['name'].'</p>';
                }
                
            }
            
        }

	}
    
	public function citiesParser(Request $request) 
	{
		$client = new \GuzzleHttp\Client(['base_uri' => implode([ $this->endPoint, 'ru', $this->citiesJson ], '/')]);

        $response = $client->request(
            'GET', 
            '',
             [
                'allow_redirects' => true,
                'timeout' => 2000,
                'http_errors' => false
            ]
        );
        
        if($response->getStatusCode() == 200) {
            
            // Читаю JSON:
            $citiesArray = json_decode($response->getBody(), true);
            
            // Иду по городам:
            foreach($citiesArray as $cityItem) {
                
                $Travelpayouts = Travelpayouts::where('iata_code', $cityItem['code'])->first();
                
                if($Travelpayouts == null) {
                    
                    $Travelpayouts = new Travelpayouts;
                    $Travelpayouts->name_en = $cityItem['name_translations']['en'];
                    $Travelpayouts->name_ru = $cityItem['name'];
                    $Travelpayouts->country_code = $cityItem['country_code'];
                    $Travelpayouts->iata_code = $cityItem['code'];
                    $Travelpayouts->lon = $cityItem['coordinates']['lon'];
                    $Travelpayouts->lat = $cityItem['coordinates']['lat'];
                    $Travelpayouts->vi = strlen($cityItem['cases']['vi']) > 0 ? $cityItem['cases']['vi'] : null;
                    $Travelpayouts->tv = strlen($cityItem['cases']['tv']) > 0 ? $cityItem['cases']['tv'] : null;
                    $Travelpayouts->ro = strlen($cityItem['cases']['ro']) > 0 ? $cityItem['cases']['ro'] : null;
                    $Travelpayouts->pr = strlen($cityItem['cases']['pr']) > 0 ? $cityItem['cases']['pr'] : null;
                    $Travelpayouts->da = strlen($cityItem['cases']['da']) > 0 ? $cityItem['cases']['da'] : null;
                    $Travelpayouts->time_zone = $cityItem['time_zone'];
                    $Travelpayouts->save();
                    
                } else {
                    $this->citiesLogs['Travelpayouts'][] = 
                    'Уже есть в базе код '.$Travelpayouts->iata_code.': ' . $cityItem['name_translations']['en'];
                }
                
                // $this->saveCity($cityItem);
            }
            
            dd($this->citiesLogs);
            
        }
        
    }
    
	public function saveCity($cityItem) 
	{
        $geodataCities = Cities1000::where('name', $cityItem['name_translations']['en'])
            ->where('country_code', $cityItem['country_code'])
            ->get();


        if(count($geodataCities) == 0) {
            $this->citiesLogs['0'][] = 'Не найдено городов с названием: ' . $cityItem['name_translations']['en'];
        } else if(count($geodataCities) == 1) {
            foreach($geodataCities as $geodataCity) {
                
                if(abs($geodataCity->longitude - $cityItem['coordinates']['lon']) < 0.5) {
                    if(abs($geodataCity->latitude - $cityItem['coordinates']['lat']) < 0.5) {
                        // TODO:
                    } else {
                        $this->citiesLogs['long'][] = 'Слишком большое отклонение по latitude: ' . $cityItem['name_translations']['en'];
                    }
                } else {
                    $this->citiesLogs['long'][] = 'Слишком большое отклонение по longitude: ' . $cityItem['name_translations']['en'];
                }
                
                
            }
        } else if(count($geodataCities) > 1) {
            
            $this->citiesLogs['1+'][] = 'Больше 1 совпадения для города: ' . $cityItem['name_translations']['en'];
            
            foreach($geodataCities as $geodataCity) {
                
                if(abs($geodataCity->longitude - $cityItem['coordinates']['lon']) < 0.5) {
                    if(abs($geodataCity->latitude - $cityItem['coordinates']['lat']) < 0.5) {
                        // TODO:
                       // $geodataCity->
                    } else {
                        $this->citiesLogs['long'][] = 'Слишком большое отклонение по latitude: ' . $cityItem['name_translations']['en'];
                    }
                } else {
                    $this->citiesLogs['long'][] = 'Слишком большое отклонение по longitude: ' . $cityItem['name_translations']['en'];
                }  
                

            }
        }
        
        dd($this->citiesLogs);

    }
    
	public function checkIataDup() 
	{
        $items = Travelpayouts::all();
        
        $array = [];
        
        foreach($items as $item) {
            if(isset($array[$item->iata_code])) {
                $array[$item->iata_code] += 1;
            } else {
                $array[$item->iata_code] = 0;
            }
        }
        
        foreach($array as $iata => $count) {
            if($count > 0) {
                echo '>1 : '. $iata.'<br>';
            }
        }
        
        if(count($items) != count($array)) {
            echo 'Есть дубли!';
        } else {
            echo 'Дублей нет!';
        }
        
        // dd($array);
    }

	public function matchTravelP() 
	{
     //   $items = Travelpayouts::where('geoname_id', 0)->get(); // dd(count($items));
        $items = Travelpayouts::where('geoname_id', 0)->whereNotNull('name_ru')->get(); // dd(count($items));
        
        $array = [
            'unknown' => [],
            'good' => [],
            'more than one' => [],
            'more than one - good' => [],
        ];
        
     //  dd(count($items));
        
        foreach($items as $item) {
            
            /*
            $cities = Cities1000::where('name', $item->name_en)
                //->where('name_ru', $item->name_ru)
                ->where('country_code', $item->country_code)
                ->get();
            */
            
            /*
            $cities = Cities1000::where('name', $item->name_en)
                ->where(function ($query) use ($item) {
                    $query
                        ->where('longitude', '<', $item->lon + 2)
                        ->where('latitude', '<', $item->lat + 2);
                })
                ->where(function ($query) use ($item) {
                    $query
                        ->where('longitude', '>', $item->lon - 2)
                        ->where('latitude', '>', $item->lat - 2);
                })
                ->where('country_code', $item->country_code)
                ->where('feature_code', 'PPL')
             ->get();
             */
             
            $cities = Cities1000::where('name_ru', $item->name_ru)
                ->where('country_code', $item->country_code)
                ->get(); 
                
            $count = count($cities);
            
            if($count == 0) {
                
                $array['unknown'][] = $item;
                
            } else if($count == 1) {
                
                $array['good'][] = $item;
                echo $item->name_ru .' - '. $cities[0]->name_ru.' ['.$item->country_code.'] ('.$item->lon.', '.$item->lat.' / '.$item->iata_code.')<br>';

                // Сохраняю код: 
               $this->saveMatching($item, $cities[0]);
                
                
            } else if($count > 1) {
                
                $array['more than one'][] = $cities;
/*
                //$array['more than one'][] = $cities;
                
                $diffs = [];
                
                $max = 0.2;
                
                $travelItems = [];
                
                // Рассчитываю координаты:
                foreach($cities as $city) {
                    $diffLon = abs($city->longitude - $item->lon);
                    $diffLat = abs($city->latitude - $item->lat);
                    //$diffs[] = [$diffLon, $diffLat];
                    
                    if($diffLon < $max and $diffLat < $max) {
                        $travelItems[] = [
                            'travel' => $item,
                            'geo' => $city
                        ];
                    }
                }
                
                if(count($travelItems) == 0) {
                } else if(count($travelItems) == 1) { 
                    $array['more than one - good'][] = $item;
                    echo $travelItems[0]['travel']->name_en .' - '. $travelItems[0]['geo']->name.' ['.$travelItems[0]['travel']->country_code.']<br>';
                    $this->saveMatching($travelItems[0]['travel'], $travelItems[0]['geo']);
                }else {
                    $array['more than one - bad'][] = $travelItems;
                }
                
                //$array['more than one'][] = $diffs;
*/
            }
        }
       // return response()->json($array['good']);
       // dump(count($array['unknown']));
        // dump(count($array['good']));
        // dump($array['good']);
        // dump(count($array['more than one']));

       
        
      //  dump($array['more than one - bad']);
     //   dd(1);
        //return response()->json($array['unknown']);
       // return response()->json($array['good']);

    }
    
	public function saveMatching($travelCity, $geoNamesCity)
	{
        $geoNamesCity->iata_code = $travelCity->iata_code;
        $geoNamesCity->save();
        
        $travelCity->city_id = $geoNamesCity->id;
        $travelCity->geoname_id = $geoNamesCity->geonameid;
        $travelCity->save();
    }
    
	public function emptyCoordinates() 
	{
        $items = Travelpayouts::where('geoname_id', 0)->where(function ($query) {
            $query
                ->whereNull('lon')
                ->orWhereNull('lat');
        })->get();

        return response()->json(count($items));
    }
    
	public function emptyNameRu() 
	{
        $items = Travelpayouts::where('geoname_id', 0)->whereNull('name_ru')->get();

        return response()->json(count($items));
    }
    
}