<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'email_verified_at', 'created_at', 
        //'updated_at'
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function socialAccounts()
    {
        return $this->hasMany('App\UserSocialAccount', 'user_id', 'id');
    }
    
    /**
     *  Даты должны быть в формате unix:
     */
    public function getUpdatedAtAttribute($value) 
    {
        return Carbon::parse($value)->timestamp;
    }
    
    /**
     *  Даты должны быть в формате unix:
     */
    public function getPasswordExpiredAtAttribute($value) 
    {
        return Carbon::parse($value)->timestamp;
    }
    
}
