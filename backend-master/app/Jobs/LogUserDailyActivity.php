<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Statistics\Daily;

class LogUserDailyActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId, $logDate;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $logDate)
    {
        $this->userId = $userId;
        $this->logDate = $logDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // SendErrorToTelegram::dispatch($array);
        /*
- Таск виконується
    - При підключенні до сокета подивитися у редіс!
    - дивимость у редіс чи є запис про цього юзера за сьогоднішню дату, якщо запис є, нічого не робимо
    - якщо нема, то створюємо запис у редісі (ttl = 48h), а також дописуємо daily_active_users
*/
        $record = new Daily;
        $record->date = $this->logDate;
        $record->user_id = $this->userId;
        $record->save();
        
        
    }
}
