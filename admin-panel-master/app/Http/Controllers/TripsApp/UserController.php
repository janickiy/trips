<?php 

namespace App\Http\Controllers\TripsApp;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Hash;
use App\TripsModels\User;
use App\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Rules\TikTokUsername;
use Carbon\Carbon;

class UserController extends Controller
{
    private $fieldsForExport = ["id", "email", "username", "first_name", "last_name", "created_at", "deleted"];
    
    
    public function __construct()
    {
        $this->storageFolder = "trips_users";
    }

    /**
     *  Items list:
     */
    public function itemsList(Request $request)
    {
        if($request->has('q')) {
            $items = User::
              orWhere('email','like','%' . $request->q . '%')
            ->orWhere('first_name','like','%' . $request->q . '%')
            ->orWhere('last_name','like','%' . $request->q . '%')
            ->orWhere('username','like','%' . $request->q . '%')
            ->orderBy('id','desc')
            ->paginate(10);
        } else {
            $items = User::orderBy('id','desc')
            ->paginate(10);
        }

        return view(
            'cp.TripsUser.items_list',
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
        return view('cp.TripsUser.add_item', compact('roles'));
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {

        $item = User::where('email', trim($request->email))->first();
        
        if($item != null) {
            return redirect()->back()->with('email_exists', "Указанный e-mail: " . $request->email . " уже зарегистрирован.")->withInput();
        }

        $item = User::where('username', trim($request->username))->first();
        
        if($item != null) {
            return redirect()->back()->with('email_exists', "Указанный username: " . $request->username . " уже зарегистрирован.")->withInput();
        }
        
        
        $validateUserName = Validator::make($request->all(), [
            'username' => ['max:191', new TikTokUsername],
        ]);

        if ($validateUserName->fails()) {
            return redirect()->back()->with('email_exists', "Ошибка, неправильный username.")->withInput();
        }
        
        
        $item = new User;
        $item->fill($request->all());
        
        $item->password = bcrypt(Str::upper(Str::random(6)));
        
        $item->save();
        
        
        /*
        $client = new \GuzzleHttp\Client();
        
        // Registraion request:
        $res = $client->request('POST', 'http://trips.com.yy/api/send_auth_email', [
            'form_params' => [
                'email' => trim($request->email)
            ],
        ]);*/
        
        return redirect('cp/trips_user/edit/'.$item->id)->with('item_created', __('user.item_created'))->with('details', $item->email);
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       $roles = Role::all();
       
       $item = User::where('id', $id)->first();
       if($item == null) {
           return redirect(URL::to('/') . '/cp/trips_user');
       }
       return view('cp.TripsUser.edit_item', compact('id','item', 'roles'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {

        $filesFields = array("");
        
        // Текущий пользователь:
        $item = User::where('id', $request->id)->first();    
        
        // Если емайл изменился:
        if($request->email != $item->email) {
            
            // Если он занят:
            $item2 = User::where('email', trim($request->email))->first();
            
            if($item2 != null) {
                return redirect()->back()->with('email_exists', "Указанный e-mail: " . $request->email . " уже зарегистрирован.")->withInput();
            }            

        }
        
        if($item == null) {
            return redirect('cp/trips_user')->with('not_found', __('user.item_not_found'));    
        }
        
        // Проверяю никнейм:
        $item3 = User::where('username', trim($request->username))->where("id", "!=", $item->id)->first();
        
        if($item3 != null) {
            return redirect()->back()->with('email_exists', "Указанный username: " . $request->username . " уже зарегистрирован.")->withInput();
        }
        
        
        $validateUserName = Validator::make($request->all(), [
            'username' => ['max:191', new TikTokUsername],
        ]);

        if ($validateUserName->fails()) {
            return redirect()->back()->with('email_exists', "Ошибка, неправильный username.")->withInput();
        }   
 
        // UPDATE:
        if(isset($request->submit)) {
         
            $item->fill($request->all());
            $item->save();
            
            return redirect('cp/trips_user/edit/'.$item->id)
                ->with('update_item', __('user.item_updated'))
                ->with('details', $item->email);
        }

 
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

    public function exportUsersCSV(Request $request) 
    {
        $filename = 'users_dump_'.str_replace(' ', '_', str_replace(':', '_', Carbon::now())).'.csv';
    
        $headers = array(
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Expires' => '0',
            'Pragma' => 'public',
        );
        
        if(!$request->has('ids')) {
            return response()->json([
                "status" => 400,
                "message" => "Specify ids",
            ]);
        }
        
        // Експорт всей бази:
        if($request->ids == null) {
            $data = User::all($this->fieldsForExport);
        } else {
            // Експорт указанных записей:
            $idsArray = explode(PHP_EOL, $request->ids);
            $data = [];
            
            foreach($idsArray as $id) {
                
                $record = User::where("id", $id)->first($this->fieldsForExport);;
                if($record) {
                    $data[] = User::where("id", $id)->first($this->fieldsForExport);
                }
            }
        }

        $handle = fopen($filename, 'w');
            
        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM
        fputcsv($handle, $this->fieldsForExport, ";");

        foreach($data as $row) {
            fputcsv($handle, [
                $row->id,
                $row->email,
                $row->username,
                $row->first_name,
                $row->last_name,
                $row->created_at,
                $row->deleted,
            ], ';');
        };

        fclose($handle);
 
        return response()->download(public_path() . '/' . $filename, $filename, $headers)->deleteFileAfterSend(true);
        
    }
    
    
    
  
}