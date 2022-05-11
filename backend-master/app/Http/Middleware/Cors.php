<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Cors
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
        
        $handle = $next($request);

        if(method_exists($handle, 'header'))
        {
            // Standard HTTP request.

            $handle->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, HEAD, PATCH')
            ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Content-Length, X-Token-Auth, Authorization, x-xsrf-token, x-socket-id, X-Request-ID, Upload-Offset, Upload-Length, Upload-Metadata, Tus-Version, Tus-Resumable, Tus-Extension, Tus-Max-Size, X-HTTP-Method-Override, Upload-Defer-Length, Upload-Checksum, Tus-Checksum-Algorithm, Upload-Concat, X-Content-Type-Options');

            return $handle;

        }

        // Download Request?

        $handle->headers->set('Access-Control-Allow-Origin' , '*');
        $handle->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE, HEAD, PATCH');
        $handle->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Range, Authorization, X-Requested-With, Application, X-Request-ID, Upload-Offset, Upload-Length, Upload-Metadata, Tus-Version, Tus-Resumable, Tus-Extension, Tus-Max-Size, X-HTTP-Method-Override, Upload-Defer-Length, Upload-Checksum, Tus-Checksum-Algorithm, Upload-Concat, X-Content-Type-Options');
        $handle->headers->set('Accept-Ranges', 'bytes');

        return $handle;
         
            
    }
}