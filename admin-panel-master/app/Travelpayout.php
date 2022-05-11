<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class Travelpayout extends Model
{
    protected $table = "travelpayout_cities";
    //public $timestamps = false; 
    
    protected $fillable = ["wiki_entity", "geoname_id", "name_en", "name_ru", "country_code", "iata_code", "lon", "lat", "vi", "tv", "ro", "pr", "da"];

    protected $casts = [];

    
    public function city() 
    {
        return $this->hasOne('App\Cities1000',  'id', 'city_id') ;
    }
    
    public function country() 
    {
        return $this->hasOne('App\Country', 'code', 'country_code') ;
    }   
    
}