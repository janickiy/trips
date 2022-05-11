<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class Geoname extends Model
{
    protected $table = "geonames";
    public $timestamps = false;  
    
    protected $fillable = ["wiki_entity", "geonameid", "name", "name_ru", "asciiname", "alternatenames", "latitude", "longitude", "country_code", "population", "elevation", "timezone", "iata", "genitive", "dative", "accusative", "instrumental", "prepositional", "without_diacritics", "region"];

    protected $casts = [];

    
    public function country() 
    {
        return $this->hasOne('App\Country', 'code', 'country_code') ;
    }

    
    # methods
}