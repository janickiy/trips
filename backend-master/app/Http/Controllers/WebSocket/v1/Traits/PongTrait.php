<?php 

namespace App\Http\Controllers\WebSocket\v1\Traits;

use Carbon\Carbon;
use DB;
use Log;

/**
 *  Обработчик события add_artifact
 */
trait PongTrait
{
    public function pong()
    {
        if($this->noErrors()) {
            if($this->playload->event != 'ping') {
                return false;
            }
            
            // Отправляю приветствие:
            $this->connection->send(json_encode([
                'event'         => 'pong',
                "seq_num"       => $this->playload->seq_num,
                'status_code'   => 200,
                /*
                'data' => [
                    'socket_id' => $this->connection->socketId
                ],
                */
            ]));
        }
    }
}
    