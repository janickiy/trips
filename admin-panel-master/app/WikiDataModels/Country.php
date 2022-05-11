<?php

namespace App\WikiDataModels;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'wikidata_countries';
    public $timestamps = false;

    protected $fillable = [];

    public function cities()
    {
        return $this->hasMany('App\WikiDataModels\City', 'country_code', 'code');
    }

}
