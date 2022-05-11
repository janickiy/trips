<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = "countries";
    
    protected $fillable = [
        "created_at", "updated_at", "deleted_at",
        "wiki_id", "wiki_link", "wiki_region_class_id", 
        "name_ru", "name_en", "code", "currency", 
        "vi", "tv", "ro", "pr", "da", 
        "moderated"
    ];
    
    # methods
    public function cities()
    {
        return $this->hasMany('App\Cities1000', 'country_code', 'code');
    }
    
}