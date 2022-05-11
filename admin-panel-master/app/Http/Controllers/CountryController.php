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
use App\Country;
use Carbon\Carbon;

class CountryController extends Controller
{

    public function __construct()
    {
        $this->storageFolder = "country";
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
            ->orderBy('id','desc');
        } else {
            $items = Country::withCount('cities')->orderBy('id','desc');
        }

        $allItems = $items;
        $itemsCount = $allItems->count();
        $items = $items->paginate(20);
        
        return view(
            'cp.Country.items_list',
            array(
                'items' => $items->appends(Input::except('page')),
            ),
            compact('items', 'itemsCount')
        );

    }

    /**
     *  Add item:
     */
    public function addItem()
    {
        return view('cp.Country.add_item');
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

       return view('cp.Country.edit_item', compact('id','item'));
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

            // RESTORE:
            if(isset($request->restore)) {
                $item->updated_at = Carbon::now();
                $item->deleted_at = null;
                $item->save();
                
                return redirect('cp/country/edit/'.$item->id)->with('restore_item', __('country.item_restored'));
            }
        
            // DELETE:
            if(isset($request->delete)) {

                $item->deleted_at = Carbon::now();
                $item->save();
                
                return redirect('cp/country/edit/'.$item->id)->with('deleted_item', __('country.item_deleted'));
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

}
