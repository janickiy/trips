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

class CheckUserLang
{
    public function handle($request, Closure $next)
    {
        App::setLocale(Auth::user()->lang);
        return $next($request);
    }
}