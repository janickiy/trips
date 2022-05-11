<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;
use Salabun\TelegramBotNotifier;

class SendErrorToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $errorArray = [];
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($array)
    {
        $this->errorArray = $array;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(config('telegram.error_botification') == 0) {
            return false;
        }
        
        $bot = new TelegramBotNotifier(config('telegram.bot_token'));
        $bot->addRecipient(config('telegram.chat_id'));
        
        if(isset($this->errorArray['from'])) {
            $bot->br()->strong($this->errorArray['from']);
        }
        
        $fieldsSet = [
            "status_code", "status_message", "user_id", "event", "seq_num", "info"
        ];
        
        foreach($fieldsSet as $field) {
            if(isset($this->errorArray[$field])) {
                $bot->br()->strong($field . ": ");
                $bot->text($this->errorArray[$field]);
            }
        }

        $bot->send();

    }
}
