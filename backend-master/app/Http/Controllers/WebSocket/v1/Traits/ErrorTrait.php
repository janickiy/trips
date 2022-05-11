<?php 

namespace App\Http\Controllers\WebSocket\v1\Traits;

use Carbon\Carbon;
use DB;
use Log;
// use App\Jobs\SendErrorToTelegram;
use Illuminate\Support\Facades\Http;

/**
 *  Обработчик ошибок
 */
trait ErrorTrait
{
    public $logErrors = true;
    
    public function throwError($code, $message, $file, $line, $dataArray = null)
    {
        $this->errors[] = $message;

        $this->connection->send(json_encode([
            "event"          => $this->responseEvent($this->playload->event),
            "seq_num"        => $this->playload->seq_num,
            "status_code"    => $code,
            "status_message" => $message,
            'data' => [],
        ]));

        
        if($this->logErrors) {
            Log::warning($message, [
                'file'      => $file,
                'line'      => $line,
                'user_id'   => $this->userId,
                'data'      => $dataArray
            ]);
        }
        
        if(config("telegram.error_botification") == 1) {
            try {

                $response = Http::post(config("app.url") . "/api/dispatch_job", [
                    'token' => "EJSPFJEWNSCKSPEJFYESKSUKL",
                    'data' => [
                        "from" => "🤖 WebSocket exception",
                        "status_code"    => $code,
                        "status_message" => $message,
                        'user_id'        => $this->userId,
                        "event"          => $this->responseEvent($this->playload->event),
                        "seq_num"        => $this->playload->seq_num,

                    ],
                ]);
            
            } catch(\Exception $e) {
                Log::warning($e->getMessage());
            }
        }

    }
    
    
}
    