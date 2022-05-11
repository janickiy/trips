<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
use Redirect;
use Log;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendErrorToTelegram;
use App\Http\Controllers\Api\AppleController;

class HomeController extends Controller
{

    protected $jwt = 'eyJraWQiOiJlWGF1bm1MIiwiYWxnIjoiUlMyNTYifQ.eyJpc3MiOiJodHRwczovL2FwcGxlaWQuYXBwbGUuY29tIiwiYXVkIjoiY29tLnRyaXBzLnRyaXBzLnNpZCIsImV4cCI6MTYyNTE1NjYwNCwiaWF0IjoxNjI1MDcwMjA0LCJzdWIiOiIwMDE2NTYuMmFjZTM2ZWRlNzdlNDQxYWJiY2Q4MDM2YjFiNDllZDYuMTgyMSIsImNfaGFzaCI6IkdxS1NTel9SZ3VQbWlsY3I0TDVLa3ciLCJlbWFpbCI6InZsYWRzYWxhYnVuQGdtYWlsLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjoidHJ1ZSIsImF1dGhfdGltZSI6MTYyNTA3MDIwNCwibm9uY2Vfc3VwcG9ydGVkIjp0cnVlfQ.aTthsHV5fFmE-S8tVwCa_tO1s7NgNMBZ2CNvzgv6wFH8d4Dlic3Xd_cLN4FZAtnGSRlU0niRKqZjnGS6Z7T01tvKkmbBMDv5k5nYsmPyDbvX7_8WQt2su4Gvvr3crRVasABxli3XaY9-mV21wr1FCuBlWvQWHBUPYosEb5g5w917e-eXR_SJWQwQA67IRhjJtvCdQy24MBFRLyKWcTX2GtXzC8C99SIkvHzvO7HOsfauKreKcHDOioywhG92oSoiVYLj0NEiUZS2zLIfB8CBWkreG6JIeGJF35l6OYMAdncDSj8YqwWJE4BWWTNP-SSQGV_L-NdxSDQzOvst5v6WIA';
    protected $code = 'cc83e1d5128e84dba87e4955094b4b4bf.0.rrwvw._6V_Eo38bPqd1BrFtGx7wg';

    public function __construct()
    {

    }

    public function clearAppCache()
    {
        Artisan::call('cache:clear'); // php artisan config:clear for .env
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        
        Artisan::call('config:cache');
        Artisan::call('route:cache'); 

        return response()->json(['OK']);
    }
    
    public function welcome()
    {

        return response()->json([
            'code'              => 200,
            'message'           => 'OK',
            'server_timezone'   => config('app.timezone'),
            'server_date'       => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
    
    public function index()
    {
        return view('home');
    }
    
    public function dispatchNewJob(Request $request)
    {

        if(!$request->has('token') or $request->token !== 'EJSPFJEWNSCKSPEJFYESKSUKL') {
             Log::info('rejected', $request->toArray());
        }
        
        SendErrorToTelegram::dispatch($request->data); 
    }
    
    public function authCodeTemplate(Request $request)
    {
        $name = 'Artemiy';
        $code = 'WYISWG';
        
        if($request->has('lang')) {
            return view('emails.mail_' . $request->lang, compact('name', 'code'));
        }
        
        return view('emails.mail_en', compact('name', 'code'));
    }
    
    public function sendDeleteRequestTest()
    {
        $userId = 5;
        
        $client = new \WebSocket\Client(config('websockets.endpoint') . "?admin=" . config('websockets.admin_token'), [
            'timeout' => 5,
        ]);
             
        try {
            $client->send(json_encode([
                "event" => "user_was_deleted",
                "app_version" => 1,
                "data" => [
                    "user_id" => $userId,
                ]
            ])); 
        
        } catch(\WebSocket\ConnectionException $e) {
            
            Log::warning('sendDeleteRequestTest WebSocket\ConnectionException', [  
                "status_code" => 500,  
                "status_message" => $e->getMessage(),  
                "data" => [
                    "user_id" => $userId,
                ]                                                               
            ]); 
            
        }
    }
    
    public function testJWT()
    {
        AppleController::verify($this->jwt);
 
    } 
}