<?php 
/*
|-------------------------------------------------------------
| Author: Vlad Salabun 
| Site: https://salabun.com 
| Contacts: https://t.me/vlad_salabun | vlad@salabun.com 
|-------------------------------------------------------------
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;

use App\Country;
use App\Cities1000;

use Carbon\Carbon;

use morphos\Russian\GeographicalNamesInflection;


class MorphosController extends Controller
{
	public function __construct() 
	{
        $this->cases = [
          "genitive" => "родительный",
          "dative" => "дательный",
          "accusative" => "винительный",
          "instrumental" => "творительный",
          "prepositional" => "предложный",
        ];  

        $this->exception = [
            'Ий', 
            'Эгюий', 
            'И-сюр-Тий', 
            'Арк-сюр-Тий', 
            'Юли-Ий', 
            'Марсийи-сюр-Тий'
         ];
        
	}


	public function getCases() 
	{
        $cities = Cities1000::whereNotNull('name_ru')
            ->whereNull('genitive')
            ->orderBy('population', 'desc')
            ->get();
  
  
        foreach($cities as $city) {
        
            if(!in_array($city->name_ru, $this->exception)) {
        
                // Проверяем склоняемо ли имя:
                if(GeographicalNamesInflection::isMutable(($city->name_ru))) {
                   
                    foreach($this->cases as $case => $caseRU) {
                        $casesArray[] = GeographicalNamesInflection::getCase($city->name_ru, $caseRU);
                        $city->$case = GeographicalNamesInflection::getCase($city->name_ru, $caseRU);
                    }
                } else {
                    // Если не склоняемо:
                    foreach($this->cases as $case => $caseRU) {
                        $city->$case = $city->name_ru;
                    }
                    
                }
                
                $city->save();
            } else {
                foreach($this->cases as $case => $caseRU) {
                    $city->$case = $city->name_ru;
                }
                $city->save();
            }            
            
        }
        
        dd(count($cities));

    }
    
}