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
use App\WikiDataModels\City;
use App\WikiDataModels\Country;

class WikiDataController extends Controller
{

    /**
     *  Items list:
     */
    public function itemsList(Request $request)
    {

        // Поиск по названию и сортировка по населению:
        if($request->has('q')) {

            $items = City::with('country')
            ->orWhere('name','like','%' . $request->q . '%')
            ->orWhere('name_ru','like','%' . $request->q . '%')
            ->orWhere('wiki_entity','like','%' . $request->q . '%');

            if($request->has('population_sort')) {
                if(in_array($request->population_sort, ['asc', 'desc'])) {
                    $items = $items->orderBy('population', $request->population_sort);
                } else {
                    return redirect('cp/wikidata_cities');
                }
            } else {
                $items = $items->orderBy('id','desc');
            }

        } else if(!$request->has('q') and $request->has('population_sort'))  {

            // Сортировка только по населению:
            if(in_array($request->population_sort, ['asc', 'desc'])) {
                $items = City::with('country')->orderBy('population', $request->population_sort);
            } else {
                return redirect('cp/wikidata_cities');
            }

        } else {
            $items = City::with('country')->orderBy('population','desc');
        }

        // Пагинация
        $allItems = $items;
        $itemsCount = $allItems->count();
        $items = $items->paginate(20);

        return view(
            'cp.WikiDataCities.items_list',
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
        return view('cp.WikiDataCities.add_item');
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {
        $item = new City;
        $item->fill($request->all());

        $filesFields = array("");

        foreach($filesFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
            }
        }

        $item->custom_edited = 1;
        $item->need_custom_moderation = 0;
        $item->modification_date = Carbon::now();
        $item->wiki_entity = ucfirst($request->wiki_entity);

        $item->save();

        return redirect('cp/wikidata_cities/edit/'.$item->id)->with('item_created', __('cities_1000.item_created'));
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       $item = City::where('id', $id)->first();

       if($item == null) {
            return redirect('cp/wikidata_cities');
       }


       return view('cp.WikiDataCities.edit_item', compact('id','item'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {
        $filesFields = array("");

        $item = City::where('id', $request->id)->first();

        if($item == null) {
            return redirect('cp/wikidata_cities');
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
                return redirect('cp/wikidata_cities')->with('deleted_item', __('cities_1000.item_deleted'));
            }

            // UPDATE:
            if(isset($request->submit)) {

                $item->fill($request->all());
                $item->custom_edited = 1;
                $item->need_custom_moderation = 0;

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

                $item->wiki_entity = ucfirst($request->wiki_entity);
                $item->modification_date = Carbon::now();
                $item->save();

                return redirect('cp/wikidata_cities/edit/'.$item->id)->with('update_item', __('cities_1000.item_updated'));
            }

        }

        // ERROR:
        return redirect('cp/wikidata_cities')->with('not_found', __('cities_1000.item_not_found'));

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
