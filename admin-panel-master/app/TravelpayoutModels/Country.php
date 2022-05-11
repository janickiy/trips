<?php

namespace App\TravelpayoutModels;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'travelpayout_countries';
    public $timestamps = false;

    protected $fillable = [];

    public function cities()
    {
        return $this->hasMany('App\TravelpayoutModels\City', 'country_code', 'code');
    }
    
}
