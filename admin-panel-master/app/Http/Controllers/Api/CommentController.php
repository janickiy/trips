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
use App\Comment;

class CommentController extends Controller
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
        $items = Comment::orderBy('id','desc')->paginate(30);
        return view('cp.Comment.items_list', compact('items'));
    }

    /**
     *  Add item:
     */
    public function addItem()
    {
        return view('cp.Comment.add_item');
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {
        $item = new Comment;
        $item->fill($request->all());
        
        $filesFields = array("");
        
        foreach($filesFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
            } 
        }

        $item->save();

        return redirect('cp/comment/edit/'.$item->id)->with('item_created', __('comment.item_created'));
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       // TODO:
       $item = Comment::where('id', $id)->first();
       if($item == null) {
           //return redirect(URL::to('/').'/failed_jobs');
       }
       return view('cp.Comment.edit_item', compact('id','item'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {
        $filesFields = array("");
        
        $item = Comment::where('id', $request->id)->first(); 
        
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
                return redirect('cp/comment')->with('deleted_item', __('comment.item_deleted'));
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
                return redirect('cp/comment/edit/'.$item->id)->with('update_item', __('comment.item_updated'));
            }
            
        }
        
        // ERROR:
        return redirect('cp/comment')->with('not_found', __('comment.item_not_found'));
 
    }



}
