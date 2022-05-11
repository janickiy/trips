<?php 
  
namespace App\Statistics;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Daily extends Model
{
    protected $connection = 'trips_statistics';
    protected $table = "daily_active_users"; 
    public $timestamps = false;
  
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
    
    public function User() 
    {
        return $this->hasOne('App\TripsModels\User', 'id', 'user_id'); 
    }

}