<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\User;

class DeleteAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user = [];
    
    private $tables = [
        'artifacts', 'bookings_attributes', 'cities_attributes', 'links_attributes', 'notes_attributes', 'tickets_attributes', 'trips_attributes', 'files_attributes'
    ];
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($array)
    {
        $this->user = $array;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('DELETE ACCOUNT REQUEST', $this->user);
        
        $this->deleteUserArtifacts();
        $this->deleteLocalFolder();
        $this->deleteS3Folder();
        $this->updateUserData();
        
        // TODO:
        $this->sendWSSNotification();
        
        Log::info('DELETED ACCOUNT SUCCESS', $this->user);
    }
    
    public function deleteUserArtifacts()
    {
        foreach($this->tables as $table) {
            DB::table($table)->where('created_by_user_id', $this->user['id'])->delete();
        }
        
        DB::table('user_social_accounts')->where('user_id', $this->user['id'])->delete();
        
    }
    
    public function deleteLocalFolder()
    {
        $currentPath = 'public/uploads/users/' . $this->user['id'] . '/';

        if (Storage::disk('local')->exists($currentPath)) {
            
            // first delete contents of the directory, but preserve the directory itself
            try {
                Storage::deleteDirectory($currentPath, true);
            } catch (Throwable $e) {
                // sleep 1 second because of race condition with HD
                sleep(1);
                try {
                     // actually delete the folder itself
                    Storage::deleteDirectory($currentPath);
                } catch (Throwable $e) {
                    
                }
            }
            
            
        } 
    }
    
    public function deleteS3Folder()
    {
        if(Storage::disk('s3')->exists($this->user['id'])) {
            Storage::disk('s3')->deleteDirectory($this->user['id']);
        }
    }
    
    public function updateUserData()
    {
        $user = User::where('id', $this->user['id'])->first();
        
        $user->first_name = "DELETED";
        $user->last_name = "DELETED";
        $user->username = $user->id;
        $user->email = $user->id . "@deleted.user";
        $user->deleted = 1;
        
        $user->save();
    }
    
    public function sendWSSNotification()
    {
        $client = new \WebSocket\Client(config('websockets.endpoint') . "?admin=" . config('websockets.admin_token'), [
            'timeout' => 5,
        ]);
             
        try {
            $client->send(json_encode([
                "event" => "user_was_deleted",
                "app_version" => 1,
                "data" => [
                    "user_id" => $this->user['id'],
                ]
            ])); 
        
        } catch(\WebSocket\ConnectionException $e) {
            
            Log::warning('sendDeleteRequestTest WebSocket\ConnectionException', [  
                "status_code" => 500,  
                "status_message" => $e->getMessage(),  
                "data" => [
                    "user_id" => $this->user['id'],
                ]                                                               
            ]); 
            
        }        
    }
    
}
