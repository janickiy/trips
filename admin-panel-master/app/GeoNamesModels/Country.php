<?php

namespace App\GeoNamesModels;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'geonames_countries';
    public $timestamps = false;

    protected $fillable = [];

    public function cities()
    {
        return $this->hasMany('App\GeoNamesModels\City', 'country_code', 'code');
    }

}
