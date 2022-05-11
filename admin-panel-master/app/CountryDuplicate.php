<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class CountryDuplicate extends Model
{
    protected $table = "country_duplicates";
    protected $fillable = ["country_id", "created_at", "updated_at", "wiki_id", "wiki_link", "name", "iso_code"];
    
    # methods
}