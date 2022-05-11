<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class Cities1000 extends Model
{
    protected $table = "cities_1000";
    public $timestamps = false;  
    
    protected $fillable = ["wiki_entity", "geonameid", "name", "name_ru", "asciiname", "alternatenames", "latitude", "longitude", "country_code", "population", "elevation", "timezone", "iata","iata_code", "genitive", "dative", "accusative", "instrumental", "prepositional", "without_diacritics", "region", "description", "description_ru"];

    protected $casts = [];

    
    public function country() 
    {
        return $this->hasOne('App\Country', 'code', 'country_code') ;
    }
    
    public function countryRegion() 
    {
        return $this->hasOne('App\Region', 'id', 'region_id') ;
    }
    
}