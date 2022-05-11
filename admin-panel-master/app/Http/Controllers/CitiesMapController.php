<?php 
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use App\Cities1000;
use Carbon\Carbon;

class CitiesMapController extends Controller
{

    public function __construct()
    {
        
    }

    public function index()
    {
        $cities = [];
    
 
        $items = Cities1000::select(['id', 'wiki_entity', 'name', 'name_ru', 'latitude', 'longitude', 'iata_code'])
            ->whereNull('deleted_at')
            ->chunk(10000, function($items) use (&$cities) {

                foreach($items as $item){
                    if($item['name_ru'] != null) {
                        $name = $item['name_ru'];
                    } else {
                        $name = $item['name'];
                    }
                    
                    if($item['iata_code'] != null) {
                        $hasIata = 1;
                    } else {
                        $hasIata = 0;
                    }
                    
                    $cities[] = [
                        'id' => $item['id'],
                        'latitude' => $item['latitude'],
                        'longitude' => $item['longitude'],
                        'name_ru' => $name,
                        'has_iata' => $hasIata,
                        'balun' => "",
                        'wiki_entity' => $item['wiki_entity'],
                    ];
                    
                }
            
        });

        return view('cp.CitiesMap.map', compact('cities'));
    }
    
    
    
    
    
}