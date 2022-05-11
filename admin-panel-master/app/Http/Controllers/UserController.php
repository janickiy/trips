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
use Hash;
use App\User;
use App\Role;

class UserController extends Controller
{

    public function __construct()
    {
        $this->storageFolder = "user";
    }

    /**
     *  Items list:
     */
    public function itemsList(Request $request)
    {
        if($request->has('q')) {
            $items = User::where('email','like','%' . $request->q . '%')
            ->orderBy('id','desc')
            ->paginate(30);
        } else {
            $items = User::orderBy('id','desc')
            ->paginate(30);
        }

        return view(
            'cp.User.items_list',
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
        $roles = Role::all();
        return view('cp.User.add_item', compact('roles'));
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {
        if(User::where('email', $request->email)->first() != null) {
            return redirect()->back()->with('email_exists', __('user.email_exists'))->withInput();
        }
        
        $item = new User;
        $item->fill($request->all());
        
        $item->password = bcrypt($request->password);
        $item->role_id = $request->role_id;
        $item->lang = $request->lang;
        
        $filesFields = array("image");
        
        foreach($filesFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
            } 
        }

        $item->save();

        return redirect('cp/user/edit/'.$item->id)->with('item_created', __('user.item_created'))->with('details', $item->name . ' / ' . $item->email);
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       $roles = Role::all();
       
       $item = User::where('id', $id)->first();
       if($item == null) {
           return redirect(URL::to('/') . '/cp/user');
       }
       return view('cp.User.edit_item', compact('id','item', 'roles'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {
                
        $filesFields = array("image");

        $item = User::where('id', $request->id)->first(); 
        
        if($request->email != $item->email) {
            if(User::where('email', $request->email)->first() != null) {
                return redirect()->back()->with('email_exists', __('user.email_exists'))->withInput();
            }
        }
        
        if($item != null) {
            
            // DELETE:
            if(isset($request->delete)) {
                
                // delete files:
                foreach($filesFields as $fileField) {
                    if ($request->has('delete_' . $fileField)) {
                        if(Storage::disk('public')->delete($item->$fileField)) {
                            $item->$fileField = 'img/user_default.png';
                        }
                    } 
                }
                
                $item->delete();
                
                return redirect('cp/user')
                    ->with('deleted_item', __('user.item_deleted'))
                    ->with('details', $item->email);
            }
            
            // UPDATE:
            if(isset($request->submit)) {
             
                $item->fill($request->all());
                
                if($request->has('new_password') and strlen($request->new_password > 0)) {
                    $item->password = bcrypt($request->new_password);
                }
                
                $item->role_id = $request->role_id;
                $item->lang = $request->lang;

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
                
                return redirect('cp/user/edit/'.$item->id)
                    ->with('update_item', __('user.item_updated'))
                    ->with('details', $item->email);
            }
            
        }
        
        // ERROR:
        return redirect('cp/user')->with('not_found', __('user.item_not_found'));
 
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

    public function changePassword(Request $request) 
    {
 
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }

        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);

        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();

        return redirect()->back()->with("success","Password changed successfully !");

    }

  
}