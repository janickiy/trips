<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Auth;
use DB;
use File;
use Log;
use Storage;
use URL;
use Validator;
use Carbon\Carbon;


/**
 *  @OA\Info(
 *      title = "Документация API для приложения Trips.",
 *      version = "2.0.0",
 *  )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    public function storeOnStaticServer($request, $path) 
    {
        $fileName = Str::random(16) . '.' . $request->file('file')->getClientOriginalExtension();
        
        // Сохраняю файл:                
        Storage::disk("public")->putFileAs($path, $request->file('file'), $fileName);               

        // Получаю полный URL:
        $imgSrc = storage_path('app/public/' . $path . $fileName);
        
        $client = new \GuzzleHttp\Client();

        // Отправляю файл на статик:
        $response = $client->request('POST', env('STATIC_ENDPOINT') . '/api/files',
            [
                'multipart' => [
                
                    [
                        'name'     => 'path',
                        'contents' => $path
                    ],
                    [
                        'name'     => 'fileName',
                        'contents' => $fileName
                    ],
                    [
                        'name'     => 'file',
                        'contents' => fopen($imgSrc, 'r')
                    ],
                    
                ]
            ]
        );
        
        return [
            'code' => $response->getStatusCode(),
            'file_name' => $fileName,
        ];

    }
    
    public function deleteFromStaticServer($link) 
    {
        $client = new \GuzzleHttp\Client();

        // Отправляю файл на статик:
        $response = $client->request('DELETE', env('STATIC_ENDPOINT') . '/api/files',
            [
                'form_params' => [
                        'link' =>  $link
                ]
            ]
        );


    }
    
    public function deleteFile($link) 
    {
        Storage::disk('public')->delete(str_replace(URL::to('/'), '/', $link));
    }
    
}
