<?php 
/*
|--------------------------------------------------------------------------
|   programmer: Vlad Salabun
|   e-mail: vlad@salabun.com
|   telegram: https://t.me/vlad_salabun 
|   site: https://salabun.com
|--------------------------------------------------------------------------
*/

namespace App\Http\Middleware;

use Auth;
use Closure;
use URL;
use App;

class CheckAdminPrivileges
{
    public function handle($request, Closure $next)
    {
        if(Auth::user()->role_id != 10) {
            return redirect(URL::to('/').'/logout');
        }
        return $next($request);
    }
}