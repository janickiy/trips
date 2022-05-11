<?php

namespace App\Http\Controllers\TripsApp;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Storage;
use URL;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     *  Store any file:
     */
    public function store($file, $folder)
    {        
        $path = "storage/" . $folder . "/";
        
        # Add some hash:
        $goodFilename = str_random(10) . "." . $file->getClientOriginalExtension();
        
        # Put:
        Storage::disk("public")->putFileAs($path, $file, $goodFilename);
        
        return URL::to("/") . "/" . $path . $goodFilename;
    }
    
}
