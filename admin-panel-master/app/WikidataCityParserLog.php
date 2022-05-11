<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class WikidataCityParserLog extends Model
{
    protected $table = "wikidata_city_parser_logs";

    public function city()
    {
        return $this->hasOne('App\City1000', 'id', 'city_id');
    }
    
    # methods
}