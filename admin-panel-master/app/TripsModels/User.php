<?php 

namespace App\TripsModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $connection = 'trips';
    protected $table = "users";
    public $timestamps = true;
    
    protected $fillable = ["first_name", "last_name", "username", "email", "email_verified_at", "password", "banned", "deleted"];
    
    // protected $hidden = ["password"];

    protected $casts = [];
    
    public function socialAccounts()
    {
        return $this->hasMany('App\TripsModels\UserSocialAccount', 'user_id', 'id');
    }
    
    public function artifacts() 
    {
        return $this->hasMany('App\TripsModels\Artifact', 'created_by_user_id', 'id');
    }
    
}