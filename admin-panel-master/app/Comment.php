<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "comments";
    public $timestamps = false; 
    
    protected $fillable = ["news_id", "text"];

    protected $casts = [];

    
    # methods
}