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

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use App\Role;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->storageFolder = "role";
    }

    /**
     *  Items list:
     */
    public function itemsList(Request $request)
    {
        if($request->has('q')) {
            $items = Role::where('name','like','%' . $request->q . '%')
            ->orderBy('id','desc')
            ->paginate(30);
        } else {
            $items = Role::orderBy('id','desc')
            ->paginate(30);
        }

        return view(
            'cp.Role.items_list',
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
        return view('cp.Role.add_item');
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {
        $item = new Role;
        $item->fill($request->all());
        
        $filesFields = array("");
        
        foreach($filesFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
            } 
        }

        $item->save();

        return redirect('cp/role/edit/'.$item->id)->with('item_created', __('role.item_created'));
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       // TODO:
       $item = Role::where('id', $id)->first();
       if($item == null) {
           return redirect(URL::to('/').'/cp/role');
       }
       return view('cp.Role.edit_item', compact('id','item'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {
        $filesFields = array("");
        
        $item = Role::where('id', $request->id)->first(); 
        
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
                return redirect('cp/role')->with('deleted_item', __('role.item_deleted'));
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
                return redirect('cp/role/edit/'.$item->id)->with('update_item', __('role.item_updated'));
            }
            
        }
        
        // ERROR:
        return redirect('cp/role')->with('not_found', __('role.item_not_found'));
 
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
