<?php 

namespace App\Http\Controllers\SQLite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use Carbon\Carbon;
use App\Country;
use App\Region;
use App\City;

class ExportController extends Controller
{
    public $sql = '';

    public function __construct()
    {
$this->sql = 'CREATE TABLE "Cities" (
    "id"    INTEGER NOT NULL,
    "wikiID"    INTEGER NOT NULL,
    "iata"    TEXT,
    "regionID"    INTEGER,
    "countryCode"    TEXT NOT NULL,
    "longitude"    REAL NOT NULL,
    "latitude"    REAL NOT NULL,
    "population"    INTEGER NOT NULL,
    "nameEN"    TEXT,
    "nameRU"    TEXT,
    "nameRU_rod"    TEXT,
    "nameRU_dat"    TEXT,
    "nameRU_vin"    TEXT,
    "nameRU_tvo"    TEXT,
    "nameRU_pre"    TEXT,
    PRIMARY KEY("id")
) WITHOUT ROWID;

CREATE TABLE "CitiesTemp" (
    "id"    INTEGER NOT NULL,
    "wikiID"    INTEGER NOT NULL,
    "iata"    TEXT,
    "regionID"    INTEGER,
    "countryCode"    TEXT NOT NULL,
    "longitude"    REAL NOT NULL,
    "latitude"    REAL NOT NULL,
    "population"    INTEGER NOT NULL,
    "nameEN"    TEXT,
    "nameRU"    TEXT,
    "nameRU_rod"    TEXT,
    "nameRU_dat"    TEXT,
    "nameRU_vin"    TEXT,
    "nameRU_tvo"    TEXT,
    "nameRU_pre"    TEXT,
    PRIMARY KEY("id")
) WITHOUT ROWID;

CREATE TABLE "Countries" (
    "code"    TEXT NOT NULL,
    "countryID"    INTEGER,
    "nameEN"    TEXT,
    "nameRU"    TEXT,
    PRIMARY KEY("code")
);

CREATE TABLE "Regions" (
    "id"    INTEGER NOT NULL,
    "nameEN"    TEXT,
    "nameRU"    TEXT,
    PRIMARY KEY("id")
) WITHOUT ROWID;';
   
    }
    
    public function run() 
    {
        $this->putCountries();
        $this->putRegions();
        $this->putCities();
        
        
        Storage::disk('local')->put('sqlite_db_' . date('Y-m-d_H_i_s'). '.sql', $this->sql);
    }
    
    public function download() 
    {
        $normalTimeLimit = ini_get('max_execution_time');

        ini_set('max_execution_time', 300); 
        
        $this->putCountries();
        $this->putRegions();
        $this->putCities();
        
        $filename = 'sqlite_db_' . date('Y-m-d_H_i_s'). '.sql';
        
        Storage::disk('local')->put($filename, $this->sql);
        $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        
        ini_set('max_execution_time', $normalTimeLimit); 
       // $this->showMemoryUsage();
        return response()->download($storagePath . '/' . $filename)->deleteFileAfterSend();
    }
    
    public function concat($row) 
    {
        $this->sql .= $row;
    }
    
    public function line($array) 
    {
        $values = [];
        
        $line = '(';
        
        foreach($array as $value) {
            if(is_int($value)) {
                $values[] = $value;
            } else {
                $values[] = '"'.$value.'"';
            }
        }
        
        $line .= implode(',', $values);
        
        $line .= ')';
        
        return $line;
    }
    
    
    public function putCountries()
    {
        $this->concat(PHP_EOL);
        $this->concat(PHP_EOL);
        
        $this->concat('INSERT INTO Countries VALUES');
        $this->concat(PHP_EOL);
        
        $countries = Country::all();
        
        $count = count($countries);
        $i = 1;
        
        foreach($countries as $country) {
            
            if($i == $count) {
                $endOfLine = ';';
            } else {
                $endOfLine = ',';
            }

                $this->concat($this->line([
                    $country->code,
                    $country->id,
                    $country->name_en,
                    $country->name_ru
                ]) . $endOfLine . PHP_EOL);
            
            $i++;
        }

    }
    
    public function putRegions()
    {
        $this->concat(PHP_EOL);
        $this->concat(PHP_EOL);
        
        $this->concat('INSERT INTO Regions VALUES');
        $this->concat(PHP_EOL);
        
        
        $regions = Region::all();
        
        $count = count($regions);
        $i = 1;
        
        foreach($regions as $region) {
            
            if($i == $count) {
                $endOfLine = ';';
            } else {
                $endOfLine = ',';
            }

                $this->concat($this->line([
                    $region->id,
                    $region->name_en,
                    $region->name_ru
                ]) . $endOfLine . PHP_EOL);
            
            $i++;
        }

    }

    public function putCities()
    {
        $limit = 10000;
        
        $this->concat(PHP_EOL);
        $this->concat(PHP_EOL);
        
        $this->concat('INSERT INTO Cities VALUES');
        $this->concat(PHP_EOL);

        $count = City::count();
        $steps = ceil($count / $limit);
        
        
        $i = 1;
        
        for($step = 0; $step <= $steps; $step++) {
            
            $cities = City::skip($step * $limit)->take($limit)->get(['id', 'wiki_entity', 'iata_code', 'region_id', 'country_code', 'longitude', 'latitude', 'population', 'name', 'name_ru', 'genitive', 'dative', 'accusative', 'instrumental', 'prepositional']);

            if(count($cities) > 0) {
                foreach($cities as $city) {
            
                    if($i == $count) {
                        $endOfLine = ';';
                    } else {
                        $endOfLine = ',';
                    }

                    $this->concat($this->line([
                        $city->id,
                        (int) preg_replace('/[^0-9]/', '', $city->wiki_entity),
                        $city->iata_code,
                        $city->region_id,
                        $city->country_code,
                        $city->longitude,
                        $city->latitude,
                        $city->population,
                        $city->name,
                        $city->name_ru,
                        $city->genitive,
                        $city->dative,
                        $city->accusative,
                        $city->instrumental,
                        $city->prepositional,            
                    ]) . $endOfLine . PHP_EOL);
            
                    $i++;
                }
        
            }
        }
        
        $cities = [];

    }
        
    public function showMemoryUsage()
    {
       /* Currently used memory */
       $mem_usage = memory_get_usage();
       
       /* Peak memory usage */
       $mem_peak = memory_get_peak_usage();

       echo 'The script is now using: <strong>' . round($mem_usage / 1024) . 'KB</strong> of memory.<br>';
       echo 'Peak usage: <strong>' . round($mem_peak / 1024) . 'KB</strong> of memory.<br><br>';
    }
    
}
