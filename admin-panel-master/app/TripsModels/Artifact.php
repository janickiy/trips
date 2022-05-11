<?php 
  
namespace App\TripsModels;

use Illuminate\Database\Eloquent\Model;

class Artifact extends Model
{
    protected $connection = 'trips';
    protected $table = "artifacts"; 
    protected $primaryKey = "artifact_id";    
    
    public function createdByUser() 
    {
        return $this->hasOne('App\TripsModels\User', 'id', 'created_by_user_id'); 
    }
    
}