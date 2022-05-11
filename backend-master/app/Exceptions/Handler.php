<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use App\Jobs\SendErrorToTelegram;
use Illuminate\Support\Facades\Log;
use Request;

class Handler extends ExceptionHandler
{
    protected $notificationEnabled = false;
    protected $notificationLog = true;
    protected $dontReport = [];
    protected $dontFlash = ['password', 'password_confirmation'];

    public function report(Throwable $exception)
    {
        if(config("telegram.error_botification") == 1) {
             $this->notificationEnabled = true;
        }
        
        // Kill reporting if this is an "access denied" (code 9) OAuthServerException.
        if ($exception instanceof \League\OAuth2\Server\Exception\OAuthServerException && $exception->getCode() == 9) {
            return;
        }
		
		//Log::info("error", [ $exception, $exception->getStatusCode(), $exception->getMessage()]);

        // Unauthenticated:
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
			if($exception->getStatusCode() == 401) {
				// Log::info('auth error');
				return;
			}
		}
			

		
		
		//HttpException::getStatusCode()

        $this->sendNotification($exception);

        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {

        if ($this->isHttpException($exception)) {

            // 404:
            if ($exception->getStatusCode() == 404) {              
            }
            
        }

        return parent::render($request, $exception);
    }

    public function sendNotification($exception)
    {
        $array = [
                'from' => "🤖 Laravel ExceptionHandler",
                'status_code' => $exception->getCode(),
                'status_message' => $exception->getMessage(),
        ];
        
        
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $this->notificationEnabled = false;
            $this->notificationLog = false;
            $array['status_message'] = 'Someone tried to access a non-existent route: ' . Request::url();
        }
        
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            $this->notificationEnabled = false;
            $this->notificationLog = false;
            $array['status_message'] .= ' Route: ' . Request::url();
        }
        
            
        if($this->notificationLog) {
            Log::info('exception', [$exception]);
        }

        if($this->notificationEnabled) {
            SendErrorToTelegram::dispatch($array); 
        }
    }        
    
}
