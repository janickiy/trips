<?php 

  
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = "users";
    public $timestamps = true;
    
    protected $fillable = ["name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "ip"];

    protected $casts = [];

    
    # methods
    
    public function role() 
    {
        return $this->hasOne('App\Role', 'access_level', 'role_id'); 
    }
    
}