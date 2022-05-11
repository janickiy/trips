<?php 

namespace App\Http\Controllers\WikiData;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use App\WikiDataModels\Region;
use App\WikiDataModels\City;
use App\WikiDataModels\Country;

class RegionController extends Controller
{

    public function __construct()
    {
        $this->storageFolder = "wikidata_region";
    }

    /**
     *  Items list:
     */
    public function itemsList(Request $request)
    {
        if($request->has('q')) {
            $items = Region::orWhere('name_en','like','%' . $request->q . '%')
            ->orWhere('name_ru','like','%' . $request->q . '%')
            ->orWhere('wiki_entity','like','%' . $request->q . '%')
            ->orderBy('id','desc')
            ->paginate(30);
        } else {
            $items = Region::orderBy('id','desc')
            ->paginate(30);
        }

        return view(
            'cp.WikidataRegion.items_list',
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
        return view('cp.WikidataRegion.add_item');
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {
        $item = new Region;
        $item->fill($request->all());
        
        $filesFields = array("");
        
        foreach($filesFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
            } 
        }

        $item->save();

        return redirect('cp/wikidata_region/edit/'.$item->id)->with('item_created', __('wikidata_region.item_created'));
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       $item = Region::where('id', $id)->first();
       
       if($item == null) {
            return redirect('cp/wikidata_region');
       }
       
       return view('cp.WikidataRegion.edit_item', compact('id','item'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {
        $filesFields = array("");
        
        $item = Region::where('id', $request->id)->first(); 
        
        if($item == null) {
            return redirect('cp/wikidata_region');
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
                return redirect('cp/wikidata_region')->with('deleted_item', __('wikidata_region.item_deleted'));
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
                return redirect('cp/wikidata_region/edit/'.$item->id)->with('update_item', __('wikidata_region.item_updated'));
            }
            
        }
        
        // ERROR:
        return redirect('cp/wikidata_region')->with('not_found', __('wikidata_region.item_not_found'));
 
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
