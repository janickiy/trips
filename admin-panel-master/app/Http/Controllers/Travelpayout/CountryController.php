<?php
/*
|-------------------------------------------------------------
| Author: Vlad Salabun
| Site: https://salabun.com
| Contacts: https://t.me/vlad_salabun | vlad@salabun.com
|-------------------------------------------------------------
*/

namespace App\Http\Controllers\Travelpayout;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use App\TravelpayoutModels\City;
use App\TravelpayoutModels\Country;
use Carbon\Carbon;

class CountryController extends Controller
{
 public function __construct()
    {
        $this->storageFolder = "country";
        
		$this->endPoint = 'https://api.travelpayouts.com/data';
        $this->lang = 'ru';
		$this->citiesJson = 'cities.json';
		$this->countriesJson = 'countries.json';
        
        $this->citiesLogs = [];
        
        $this->cases = [
            "vi" => "", // куда? - в Осло
            "tv" => "instrumental", // кем, чем? - Москвой
            "ro" => "genitive", // кого, чего? - Москвы
            "pr" => "", // о ком, о чем? - Москве
            "da" => "dative", // кому, чему? - Москве
        ];
    }

    /**
     *  Items list:
     */
    public function itemsList(Request $request)
    {
        if($request->has('q')) {
            $items = Country::withCount('cities')
            ->orWhere('name_ru','like','%' . $request->q . '%')
            ->orWhere('name_en','like','%' . $request->q . '%')
            //->orWhere('code','like','%' . $request->q . '%')
            ->orderBy('id','desc')
            ->paginate(20);
        } else {
            $items = Country::withCount('cities')->orderBy('id','desc')
            ->paginate(20);
        }

        return view(
            'cp.TravelpayoutCountries.items_list',
            array(
                'items' => $items->appends(Input::except('page')),
            ),
            compact('items')
        );

    }

    /**
     *  Add item:
     */
    public function addItem()
    {
        return view('cp.TravelpayoutCountries.add_item');
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {
        $item = new Country;
        $item->fill($request->all());

        $filesFields = array("");

        foreach($filesFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
            }
        }

        $item->save();

        return redirect('cp/country/edit/'.$item->id)->with('item_created', __('country.item_created'));
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       $item = Country::where('id', $id)->first();

       if($item == null) {
            return redirect('cp/country');
       }

       return view('cp.TravelpayoutCountries.edit_item', compact('id','item'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {
        $filesFields = array("");

        $item = Country::where('id', $request->id)->first();

        if($item == null) {
            return redirect('cp/country');
        }

        if($item != null) {

            // DELETE:
            if(isset($request->delete)) {

                // delete files:
                foreach($filesFields as $fileField) {
                    if(Storage::disk('public')->delete($item->$fileField)) {
                        $item->$fileField = null;
                    }
                }

                $item->delete();
                return redirect('cp/country')->with('deleted_item', __('country.item_deleted'));
            }

            // UPDATE:
            if(isset($request->submit)) {

                $item->fill($request->all());

                // upload files:
                foreach($filesFields as $fileField) {
                    if ($request->hasFile($fileField)) {
                        $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
                    }
                }

                // delete files:
                foreach($filesFields as $fileField) {
                    if ($request->has('delete_' . $fileField)) {
                        if(Storage::disk('public')->delete($item->$fileField)) {
                            $item->$fileField = null;
                        }
                    }
                }

                $item->save();
                return redirect('cp/country/edit/'.$item->id)->with('update_item', __('country.item_updated'));
            }

        }

        // ERROR:
        return redirect('cp/country')->with('not_found', __('country.item_not_found'));

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
    
    
    
    
    
	public function countriesParser(Request $request) 
	{
		$client = new \GuzzleHttp\Client(['base_uri' => implode([ $this->endPoint, 'ru', $this->countriesJson ], '/')]);

        $response = $client->request(
            'GET', 
            '',
             [
                'allow_redirects' => true,
                'timeout' => 2000,
                'http_errors' => false
            ]
        );
        
        if($response->getStatusCode() == 200) {
            
            // Читаю JSON:
            $countriesArray = json_decode($response->getBody(), true);
            
            // Иду по странам:
            foreach($countriesArray as $countryItem) {
                
                // Есть ли в базе уже такая страна:
                $country = Country::where('code', $countryItem['code'])->get();
                
                // Если нет:
                if(count($country) == 0) {
                    
                    $country = new Country;
                   
                    $country->currency = $countryItem['currency'];
                    $country->code = $countryItem['code'];
                    
                    $country->vi = $countryItem['cases']['vi'];
                    $country->tv = $countryItem['cases']['tv'];
                    $country->ro = $countryItem['cases']['ro'];
                    $country->pr = $countryItem['cases']['pr'];
                    $country->da = $countryItem['cases']['da'];
                    
                    $country->name_en = $countryItem['name_translations']['en'];
                    $country->name_ru = $countryItem['name'];
                    
                    $country->created_at = Carbon::now();
                    $country->updated_at = Carbon::now();
                    
                    $country->save();   

                } else if(count($country) == 1) {
                    
                    // echo '<p>-- обновляю страну: '.$countryItem['name'].'</p>';
                    
                    $country[0]->currency = $countryItem['currency'];
                    $country[0]->code = $countryItem['code'];
                    
                    $country[0]->vi = $countryItem['cases']['vi'];
                    $country[0]->tv = $countryItem['cases']['tv'];
                    $country[0]->ro = $countryItem['cases']['ro'];
                    $country[0]->pr = $countryItem['cases']['pr'];
                    $country[0]->da = $countryItem['cases']['da'];
                    
                    $country[0]->name_en = $countryItem['name_translations']['en'];
                    $country->name_ru = $countryItem['name'];

                    $country[0]->updated_at = Carbon::now();
                    
                    $country[0]->save();
                    
                    
                } else {
                    echo '<p>Больше, чем 1 страна с таким названием: '.$countryItem['name'].'</p>';
                }
                
            }
            
        }

	}
    
    
    
    
}
