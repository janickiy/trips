<?php

namespace App\WikiDataModels;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'wikidata_regions';
    public $timestamps = false;

    protected $fillable = [];

    public function country() 
    {
        return $this->hasOne('App\WikiDataModels\Country', 'id', 'country_id') ;
    }
    
    public function cities()
    {
        return $this->hasMany('App\WikiDataModels\City', 'region_id', 'id');
    }

}
