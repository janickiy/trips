<?php 
/*
|--------------------------------------------------------------------------
|   programmer: Vlad Salabun
|   e-mail: vlad@salabun.com
|   telegram: https://t.me/vlad_salabun 
|   site: https://salabun.com
|--------------------------------------------------------------------------
*/

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use App\News;

class NewsController extends Controller
{

    public function __construct()
    {
        # TODO: properties and auth defence 
    }

        /**
     *  Items list:
     */
    public function itemsList()
    {
        $items = News::orderBy('id','desc')->paginate(30);
        return view('cp.News.items_list', compact('items'));
    }

    /**
     *  Add item:
     */
    public function addItem()
    {
        return view('cp.News.add_item');
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {
        $item = new News;
        $item->fill($request->all());
        
        $filesFields = array("thumbnail", "big_photo");
        
        foreach($filesFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
            } 
        }

        $item->save();

        return redirect('cp/news/edit/'.$item->id)->with('item_created', __('news.item_created'));
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       // TODO:
       $item = News::where('id', $id)->first();
       if($item == null) {
           //return redirect(URL::to('/').'/failed_jobs');
       }
       return view('cp.News.edit_item', compact('id','item'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {
        $filesFields = array("thumbnail", "big_photo");
        
        $item = News::where('id', $request->id)->first(); 
        
        if($item != null) {
            
            // DELETE:
            if(isset($request->delete)) {
                
                // delete files:
                foreach($filesFields as $fileField) {
                    if ($request->has('delete_' . $fileField)) {
                        if(Storage::disk('public')->delete($item->$fileField)) {
                            $item->$fileField = null;
                        }
                    } 
                }
                
                $item->delete();
                return redirect('cp/news')->with('deleted_item', __('news.item_deleted'));
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
                return redirect('cp/news/edit/'.$item->id)->with('update_item', __('news.item_updated'));
            }
            
        }
        
        // ERROR:
        return redirect('cp/news')->with('not_found', __('news.item_not_found'));
 
    }



}
