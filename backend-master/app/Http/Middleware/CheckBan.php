<?php 
namespace App\Http\Middleware;

use Auth;
use Closure;

class CheckBan
{
    public function handle($request, Closure $next)
    {
        if(Auth::user()->banned == 1) {
            return response()->json([], 403);
        }
        
        return $next($request);
        
    }
}