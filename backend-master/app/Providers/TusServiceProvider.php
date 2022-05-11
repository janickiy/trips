<?php

namespace App\Providers;

use TusPhp\Tus\Server as TusServer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use DB;

class TusServiceProvider extends ServiceProvider
{   
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->singleton('tus-server', function ($app) {

            $server = new TusServer('redis');
            
            // Время жизни загрузки:
            $server->getCache()->setTtl(315360000);
            
            
            $user = Auth::user();
            
            // Метаданные файла можно извлечь только при создании загрузки.
            // Узнаю метод:
            $requestMethod = $server->getRequest()->method();
            
            // Если это запрос на создание файла:
            if("POST" == $requestMethod) {
                
                // Извлекаю мета данные:
                $meta = $server->getRequest()->extractAllMeta();
                
                
            
                if(!isset($meta["artifact_id"])) {
                    return [
                        'status_code' => 400,
                        'status_message' => "Specify artifact_id."
                    ];
                }
                
                // Ищу артефакт:
                $artifact = null;
                $artifact = DB::table('artifacts')->where('artifact_id', $meta["artifact_id"])->whereIn('artifact_type', [3, 8])->first();
                
                if($artifact == null) {
                    return [
                        'status_code' => 404,
                        'status_message' => "Artifact not found."
                    ];
                }
                
                // Проверяю права доступа к артефакту:
                if($artifact->created_by_user_id != $user->id) {
                    return [
                        'status_code' => 403,
                        'status_message' => "Forbidden."
                    ];  
                }
                
                
                // Создаю папку для хранения артефакта:
                $path = storage_path('app/public/uploads/users/' . $user->id . '/' . $artifact->artifact_id);
            
                if(!File::isDirectory($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                // Устанавливаю папку для дозагрузки:
                $server
                    ->setApiPath('/api/file_upload') // tus server endpoint.
                    ->setUploadDir($path); // uploads dir.
            }
            
            // Если это запрос на дозагрузку файла:
            if("PATCH" == $requestMethod) {
                $meta = $server->getRequest()->extractAllMeta();
                
                // Log::info('patch request', [$meta]);
            }
            
            // Если это запрос на информацию о файле:
            if("HEAD" == $requestMethod) {
                $meta = $server->getRequest();
                
                // Log::info('head request', [$meta]);
            }
            
            
            
            $server->event()->addListener('tus-server.upload.created', function (\TusPhp\Events\TusEvent $event) {
                $request  = $event->getRequest();
                $fileMeta = $event->getFile()->details();
                $allMeta = $event->getRequest()->extractAllMeta();
                
                
                
                // Log::info('tus-server.upload.created allMeta', [$allMeta]);
                // Log::info('tus-server.upload.created request', [$request]);
                // Log::info('tus-server.upload.created fileMeta', [$fileMeta['metadata']['artifact_id']]);
            });

            
            // По завершению загрузки - обновить запись в БД и уведомить по WSS
            $server->event()->addListener('tus-server.upload.complete', function (\TusPhp\Events\TusEvent $event) use($user) {
                
                
                
                $fileMeta = $event->getFile()->details();
                $request  = $event->getRequest();
                $response = $event->getResponse();
                
                // Log::info('fileMeta', [$fileMeta]);
                // Log::info('request', [$request]);
                // Log::info('response', [$response]);

                $artifact = DB::table("artifacts")->where('artifact_id', $fileMeta['metadata']['artifact_id'])->first();
                // Log::info("update file after upload to S3", [$artifact]);
                
                if($artifact == null) {
                    return false;
                }
                
                // Локальный путь:
                $currentPath = 'public/uploads/users/' . $user->id . '/' . $fileMeta['metadata']['artifact_id'] . '/' . $fileMeta['metadata']['filename'];

                // Если файл существует:
                if (Storage::disk('local')->exists($currentPath)) {
                    
                    // Читаю:
                    $contents = Storage::get($currentPath);
                    
                    // Загружаю в облако:
                    $uploadResult = Storage::disk('s3')->put(
                        $user->id . '/' . $fileMeta['metadata']['artifact_id'] . '/' . $fileMeta['metadata']['filename'],
                        $contents
                    );
                    
                    // Удалять файл локально только после успешной загрузки в облако
                    if($uploadResult == true) {
                        Storage::disk('local')->delete($currentPath);
                  
                        $fileTable = [
                            3 => 'files_attributes',
                            8 => 'note_photos_attributes',
                        ];

                        try {
                            // Сохраняю ссылку на файл:
                            DB::table($fileTable[$artifact->artifact_type])
                                ->where('artifact_id', $fileMeta['metadata']['artifact_id'])
                                ->update([
                                    'link' => $fileMeta['metadata']['filename'],
                                    'upload_is_complete' => 1
                                ]);
                            
                            // Увеличиваю версию:                
                            DB::table("artifacts")
                                ->where('artifact_id', $fileMeta['metadata']['artifact_id'])
                                ->update([
                                    'version' => $artifact->version + 1,
                                ]); 
                                
                        } catch(Exception $e) {
                            Log::warning('TUS Error save file to DB.', [
                                "message" => $e->getMessage(),                             
                                "user_id" => $user->id,
                                "artifact_id" => $fileMeta['metadata']['artifact_id'],
                            ]);
                        }
                        
                        

                        $client = new \WebSocket\Client(config('websockets.endpoint') . "?admin=" . config('websockets.admin_token'), [
                            'timeout' => 5,
                        ]);
                             
                        try {
                            $client->send(json_encode([
                                "event" => "publish_artifact_update",
                                "app_version" => 1,
                                "data" => [
                                    "user_id" => $user->id,
                                    "artifact_id" => $fileMeta['metadata']['artifact_id'],
                                ]
                            ])); 
                        
                        } catch(\WebSocket\ConnectionException $e) {
                            
                            Log::warning('TUS WebSocket\ConnectionException', [  
                                "status_code" => 500,  
                                "status_message" => $e->getMessage(),  
                                "data" => [
                                    "user_id" => $user->id,
                                    "artifact_id" => $fileMeta['metadata']['artifact_id'], 
                                ]                                                               
                            ]); 
                        }
                    
                    } else {
                        Log::warning('Failed upload file to Amazon S3', [  
                            "status_code" => 500,   
                            "data" => [
                                "user_id" => $user->id,
                                "artifact_id" => $fileMeta['metadata']['artifact_id'], 
                            ]                                                               
                        ]); 
                    }
                            
                            

                    
                } // <-- если файл существует

            });
                
            return $server;
        });
    }

}