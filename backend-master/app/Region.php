<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $connection = 'wikidata';
    protected $table = "regions";    
}