<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use URL;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // Если это не админ: 
        if(Auth::user()->role_id != 1) {
            return redirect(URL::to('/').'/logout');
        }

        return $next($request);
    }
}
