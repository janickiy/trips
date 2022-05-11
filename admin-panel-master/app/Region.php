<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = "regions";
    
    protected $fillable = ["country_id", "wiki_entity", "name_en", "name_ru", "created_at", "updated_at", "deleted_at"];
    
    public function country() 
    {
        return $this->hasOne('App\Country', 'id', 'country_id') ;
    }
    
}