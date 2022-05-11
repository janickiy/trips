<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Travelpayouts extends Model
{
    protected $table = "travelpayout_cities";
    public $timestamps = false;  
    
    protected $fillable = [];
    
    public function city() 
    {
        return $this->hasOne('App\Cities1000',  'id', 'city_id') ;
    }
    
}