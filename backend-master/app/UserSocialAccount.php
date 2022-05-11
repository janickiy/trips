<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSocialAccount extends Model
{
    protected $table = "user_social_accounts";   
    protected $fillable = ["created_at", "updated_at"];
    
    public function user() 
    {
        return $this->hasOne('App\User', 'id', 'user_id'); 
    }
    
}