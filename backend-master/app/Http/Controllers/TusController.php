<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

use TusPhp\Tus\Server as TusServer;

class TusController extends Controller
{

    public function __construct()
    {
        //
    }
     
    public function index(Request $request)
    {        
        if($request->getMethod() == 'OPTIONS') {
            return true;
        }

        if (Auth::check()) {

            $server = app('tus-server');

            if (is_a($server, 'TusPhp\Tus\Server')) {
                return $server->serve();
            } else {
                return response()->json([
                    'status_code' => $server['status_code'],
                    'status_message' => $server['status_message']
                ], $server['status_code']);
            }

        }

    }
    
}
