<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = "news";
    public $timestamps = false; 
    
    protected $fillable = ["title", "text", "comments_count", "comments_closed", "minutes", "publish_status", "radio", "checkbox"];

    protected $casts = ["checkbox" => "array"];

    
    # methods
}