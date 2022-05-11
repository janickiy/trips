<?php

namespace App\TravelpayoutModels;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'travelpayout_cities';
    public $timestamps = false;  
    
    protected $fillable = [];

    public function country() 
    {
        return $this->hasOne('App\TravelpayoutModels\Country', 'code', 'country_code') ;
    }
    
    
}
