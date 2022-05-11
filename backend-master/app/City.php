<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $connection = 'wikidata';
    protected $table = "cities_1000";    
}