<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MyEvent;
use Illuminate\Console\Scheduling\Event;
use Artisan;
use Redirect;
use File;
use Illuminate\Support\Facades\Response;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function clearAppCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('route:cache');
        Artisan::call('view:clear');
        Artisan::call('config:cache');
        Artisan::call('config:clear');
        // Artisan::call('optimize');
        return response()->json(['OK']);
    }
    
    public function welcome()
    {
        return view('welcome');
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function index()
    {
        return view('home');
    }
    
    
    
    public function test_chat()
    {
        event(new MyEvent('hello world'));
        return view('chat');
    }
    public function run_chat()
    {
        dd(event(new MyEvent('hello world')));
    }
    
    public function storageFolderFilename($folder, $filename)
    {       

        $path = storage_path('app/public/storage/'.$folder.'/'.$filename);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }
    
    
}