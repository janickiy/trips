<?php 
/*
|--------------------------------------------------------------------------
|   programmer: Vlad Salabun
|   e-mail: vlad@salabun.com
|   telegram: https://t.me/vlad_salabun 
|   site: https://salabun.com
|--------------------------------------------------------------------------
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

use App\Cities1000;

class CpController extends Controller
{

    public function __construct()
    {
        # TODO: properties and auth defence 
        $this->gaps = [
            [0, 1000],
            [1001, 10000],
            [10001, 100000],
            [100001, 1000000],
            [1000001, 10000000],
            [10000001, 100000000]
        ];
        
        $this->summaryMethods = [
            'wiki_entity' => 'checkWikiEntity',
            'name_en' => 'checkNameEn',
            'name_ru' => 'checkNameRu',
            'cases' => 'checkCases'
        ];
        
    }

    /**
     *  CP main Page:
     */
    public function index()
    {

        return view('cp.cp_index');
    }

    public function summaryData(Request $request)
    {
        $method = $this->summaryMethods[$request->method];
        
        $data = [
            $request->method => $this->$method(),
        ];
        
        return response()->json($data);  
    }
    
    public function checkWikiEntity()
    {

        $array = [];
        
        foreach($this->gaps as $gap) {
            
            $good = 0;
            $bad = 0;
            $total = 0;
            
            $gapName = implode(',', $gap);
            
            $noWikiEntity = Cities1000::whereNull('wiki_entity')->whereBetween('population', $gap)->get()->count();
            $hasWikiEntity = Cities1000::whereNotNull('wiki_entity')->whereBetween('population', $gap)->get()->count();

            $good += $hasWikiEntity;
            $bad  += $noWikiEntity;
            
            $total += $bad;
            $total += $good;
            
            $array[$gapName] = [
                'good' => $good,
                'bad' => $bad,
                'total' => $total,
            ];
               
        }
        
        return $array;
    }
    
    public function checkNameEn()
    {

        $array = [];
        
        foreach($this->gaps as $gap) {
            
            $good = 0;
            $bad = 0;
            $total = 0;
            
            $gapName = implode(',', $gap);
            
            $noWikiEntity = Cities1000::whereNull('name')->whereBetween('population', $gap)->get()->count();
            $hasWikiEntity = Cities1000::whereNotNull('name')->whereBetween('population', $gap)->get()->count();

            $good += $hasWikiEntity;
            $bad  += $noWikiEntity;
            
            $total += $bad;
            $total += $good;
            
            $array[$gapName] = [
                'good' => $good,
                'bad' => $bad,
                'total' => $total,
            ];
               
        }
        
        return $array;
    }
    
    public function checkNameRu()
    {

        $array = [];
        
        foreach($this->gaps as $gap) {
            
            $good = 0;
            $bad = 0;
            $total = 0;
            
            $gapName = implode(',', $gap);
            
            $noWikiEntity = Cities1000::whereNull('name_ru')->whereBetween('population', $gap)->get()->count();
            $hasWikiEntity = Cities1000::whereNotNull('name_ru')->whereBetween('population', $gap)->get()->count();

            $good += $hasWikiEntity;
            $bad  += $noWikiEntity;
            
            $total += $bad;
            $total += $good;
            
            $array[$gapName] = [
                'good' => $good,
                'bad' => $bad,
                'total' => $total,
            ];
               
        }
        
        return $array;
    }
    
    public function checkCases()
    {

        $array = [];
        
        foreach($this->gaps as $gap) {
            
            $good = 0;
            $bad = 0;
            $total = 0;
            
            $gapName = implode(',', $gap);
            
            $noWikiEntity = Cities1000::whereNull('genitive')->whereBetween('population', $gap)->get()->count();
            $hasWikiEntity = Cities1000::whereNotNull('genitive')->whereBetween('population', $gap)->get()->count();

            $good += $hasWikiEntity;
            $bad  += $noWikiEntity;
            
            $total += $bad;
            $total += $good;
            
            $array[$gapName] = [
                'good' => $good,
                'bad' => $bad,
                'total' => $total,
            ];
               
        }
        
        return $array;
    }
    
    
    /**
     *  Store any file:
     */
    public function store($file, $folder)
    {
        # Truncate long file names:
        $uploadedFilename = str_limit(
            str_before($file->getClientOriginalName(),
            '.' . $file->getClientOriginalExtension()), 
            100, 
            ''
        );
        
        # Add some hash:
        $goodFilename = Str::slug($uploadedFilename . '-' . str_random(5), '-') . '.' . $file->getClientOriginalExtension();
        
        # Put:
        Storage::disk('public')->putFileAs('storage/' . $folder . '/', $file, $goodFilename);
        
        return $goodFilename;
    }
    
}

