<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

use Illuminate\Support\Collection;
use ImageOptimizer;
use Intervention\Image\ImageManagerStatic as ImageManagerStatic;

use App\City;

class StaticController extends Controller
{    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function pics()
    {

        if ($this->request->hasFile('file')) {
        
            // Кладу фото города в папку:
            $fileName = $this->storePhoto($this->request->file('file'), 'city_photos', $this->request->city_id);
            
            // Получаю полный URL:
            $imgSrc = storage_path('app/public/storage/city_photos/' . $fileName);
            
            $client = new \GuzzleHttp\Client();

            // Отправляю файл на статик:
            $response = $client->request('POST', env('STATIC_ENDPOINT') . '/api/pics',
                [
                    'multipart' => [
                        [
                            'name'     => 'city_id',
                            'contents' => $this->request->city_id
                        ],
                        
                        [
                            'name'     => 'file',
                            'contents' => fopen($imgSrc, 'r')
                        ],
                        
                    ]
                ]
            );
            
            // Удаляю файл:
            Storage::disk('public')->delete('storage/city_photos/' . $fileName);
            
            if($response->getStatusCode() == 200) {

                 $item = City::where('id', $this->request->city_id)->first();
                 
                 if($item != null) {
                     $item->has_photo = 1;
                     $item->save();
                 }
            
                return response()->json(['Success'], 200);
                
            }
            
        }
        
        return response()->json(['Bad Request'], 400);

    }

    /**
     *  Store city photo:
     */
    public function storePhoto($file, $folder, $cityID)
    {
       // jpg, JPG, jpeg, JPEG, png, PNG
        
        // Если это jpeg:
        if(
            $file->getClientOriginalExtension() == 'jpeg' or
            $file->getClientOriginalExtension() == 'JPG' or
            $file->getClientOriginalExtension() == 'JPEG'
        ) {
            // Устанавливаю расширение:
            $ext = 'jpg';
        } else {
            $ext = $file->getClientOriginalExtension();
        }
        
        // Сохраняю исходный файл:
        Storage::disk('public')->putFileAs('storage/' . $folder . '/', $file, $cityID . '.' . $ext);
        
        // Если это не jpg:
        if($ext != 'jpg') {
            
            // Конвертирую в jpg:
            $imgSrc = storage_path('app/public/storage/city_photos/' . $cityID . '.' . $ext);
            ImageManagerStatic::make($imgSrc)->encode('jpg', 75)->save(storage_path('app/public/storage/city_photos/' . $cityID . '.jpg'));
            
            // Удаляю исходный файл:
            Storage::disk('public')->delete('storage/city_photos/' . $cityID . '.' . $ext);
            
        }

        return $cityID . '.jpg';
    }
    
    public function deletePic()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('DELETE', env('STATIC_ENDPOINT') . '/api/pics',
            [
                'form_params' => [
                    'id' => $this->request->id,
                ]
            ]
        );
        
        if($response->getStatusCode() == 200) { 

            $item = City::where('id', $this->request->id)->first();
        
            return response()->json([
                'status' => 200,
                'data' => $item,
                'message' => 'Файл удален.'
            ]);
            
        }
        
    }
    
    
    public function files()
    {
        if ($this->request->hasFile('file')) { 
            try {              
                Storage::disk("public")->putFileAs($this->request->path, $this->request->file('file'), $this->request->fileName); 
                return response()->json(['Success'], 200);
            } catch (\Throwable $e) {
                return response()->json([$e->getMessage()], 500);
            }           
        }
        
        return response()->json(['Bad Request'], 400);
    }
    
    
    
    
    
}

