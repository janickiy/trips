<?php 
/*
|--------------------------------------------------------------------------
|   programmer: Vlad Salabun
|   e-mail: vlad@salabun.com
|   telegram: https://t.me/vlad_salabun 
|   site: https://salabun.com
|--------------------------------------------------------------------------
*/
  
namespace App\TripsModels;

use Illuminate\Database\Eloquent\Model;

class UserSocialAccount extends Model
{
    protected $connection = 'trips';
    protected $table = "user_social_accounts";   
    protected $fillable = ["created_at", "updated_at"];
    
    public function user() 
    {
        return $this->hasOne('App\TripsModels\User', 'id', 'user_id'); 
    }
    
}