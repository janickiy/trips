<?php 

namespace App\Http\Controllers\WebSocket\Traits;

use Log;

/**
 *  Версии фронтенда:
 */
trait AppVersionTrait
{

    private $allowedAppVersions = [1];

    public function getAllowedAppVersions()
    {
        return $this->allowedAppVersions;
    }
    
    public function appVersionIsAllowed($integer)
    {
        if (in_array($integer, $this->allowedAppVersions)) {
           return true;
        }
        
        return false;
    }
       
   
    
}