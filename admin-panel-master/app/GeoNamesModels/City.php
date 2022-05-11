<?php

namespace App\GeoNamesModels;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'geonames_cities';
    public $timestamps = false;  
    
    protected $fillable = ["wiki_entity", "geonameid", "name", "name_ru", "asciiname", "alternatenames", "latitude", "longitude", "country_code", "population", "elevation", "timezone", "iata","iata_code", "genitive", "dative", "accusative", "instrumental", "prepositional", "without_diacritics", "region", "description", "description_ru"];

    public function country() 
    {
        return $this->hasOne('App\GeoNamesModels\Country', 'code', 'country_code') ;
    }
    
    
}
