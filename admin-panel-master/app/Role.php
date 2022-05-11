<?php 
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = "roles";
    public $timestamps = false; 
    
    protected $fillable = ["access_level", "name"];

    protected $casts = [];

    
    # methods
}