<?php 

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Cities1000;
use Carbon\Carbon;

use Illuminate\Support\Collection;
use ImageOptimizer;
use Intervention\Image\ImageManagerStatic as ImageManagerStatic;

class CityPhotosController extends Controller
{

    public function __construct()
    {
        //$this->staticServer = 'http://static.dev.trips.com.yy';
    }

    public function itemsList(Request $request)
    {
        // Поиск по названию и сортировка по населению:
        if($request->has('q')) {

            // Поиск по названию:
            $items = Cities1000::with('country')
                ->where(function ($query) use ($request) {
                    $query
                        ->orWhere('name','like','%' . $request->q . '%')
                        ->orWhere('name_ru','like','%' . $request->q . '%');
                });
            
            // Если выбрать "без фото", то берем все:
            if($request->has('no_photo') and $request->no_photo == 'on') {
                
            } else {
                // Иначе берем только с фото:
                $items = $items->where('has_photo', 1);
            }
            
            // Сортируем по убыванию населения:
            if($request->has('population_sort')) {
                if(in_array($request->population_sort, ['asc', 'desc'])) {
                    $items = $items->orderBy('population', $request->population_sort);
                } else {
                    return redirect('cp/cities');
                }
            } else {
                $items = $items->orderBy('population','desc');
            }

        } else if(!$request->has('q') and $request->has('population_sort'))  {

            // Сортировка только по населению:
            if(in_array($request->population_sort, ['asc', 'desc'])) {
                $items = Cities1000::with('country')->where('has_photo', 1)->orderBy('population', $request->population_sort);
            } else {
                return redirect('cp/cities');
            }

        } else {
            
            // По-умолчанию отображаем города с фото:
            $items = Cities1000::with('country')->where('has_photo', 1)->orderBy('population','desc');
        }

        $items = $items->paginate(12);

        
        if(env('STATIC_SERVER') != null) {
            return view(
                'cp.CityPhotos.items_list_static_endpoint',
                array(
                    'items' => $items->appends(Input::except('page')),
                ),
                compact('items')
            );
        }

        return view(
            'cp.CityPhotos.items_list_all',
            array(
                'items' => $items->appends(Input::except('page')),
            ),
            compact('items')
        );
        
    }
    
    
    
    
    
    
    
    public function cityPhotosList(Request $request)
    {
        $files = collect(Storage::files('public/storage/city_photos'));
        $items = $this->paginate($files, 12, $request->page, ['path' => url('cp/city_photos')]);
         
        return view('cp.CityPhotos.items_list', compact('items'));
    }
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ? : (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage), 
            $items->count(), 
            $perPage, 
            $page, 
            $options
        );
    }
    
    public function uploadCityPhoto(Request $request)
    {
        if($request->hasFile('photo')) {
            
            // Кладу фото города в папку:
            $url = $this->storePhoto($request->file('photo'), 'city_photos', $request->city_id);

            // Получаю полный URL:
            $imgSrc = storage_path('app/public/storage/city_photos/' . $url);
            
            // Уменьшаю до 2000рх по ширине:
            $img = ImageManagerStatic::make($imgSrc)->resize(2000, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($imgSrc);
            
             $item = Cities1000::where('id', $request->city_id)->first();
             
             if($item != null) {
                 $item->has_photo = 1;
                 $item->save();
             }
            
            
            return response()->json([
                'status' => 200,
                'data' => $url,
                'message' => 'Файл загружен.'
            ]);
        }
        
        return response()->json([
            'status' => 500,
            'data' => null,
            'message' => 'Файл не загружен.'
        ]);
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
    
    
    
    /**
     *  Просканировать папку и добавить фотографии, если их нет
     */
    public function addNewCityPhotosFromStorage(Request $request)
    {
        $fileIDs = [];
        $files = collect(Storage::files('public/storage/city_photos'));
        
        foreach($files as $file) {
            $parts = explode('/', $file);
            $fileIDs[] = explode('.', $parts[count($parts) - 1])[0];
        }
        
        $cities = Cities1000::all();
       
        $dd = []; 
        foreach($cities as $city) {
            if(in_array($city->id, $fileIDs)) {
                $city->has_photo = 1;
                $city->save();
            }
        }
    }
    
    public function testUploadPhotos(Request $request)
    {
        if($request->hasFile('photo')) {
            
            // Кладу фото города в папку:
            $url = $this->storePhoto($request->file('photo'), 'city_photos', $request->city_id);
            
            // Получаю полный URL:
            $imgSrc = storage_path('app/public/storage/city_photos/' . $url);
            
            // Уменьшаю до 2000рх по ширине:
            $img = ImageManagerStatic::make($imgSrc)->resize(2000, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($imgSrc);
            
            // Оптимизирую размер:
            ImageOptimizer::optimize($imgSrc);
 
        }
        
        return view('cp.CityPhotos.test_upload_photos');
    }
    
    public function deletePhoto(Request $request)
    {
         $item = Cities1000::where('id', $request->id)->first();
         
         if($item != null) {
             $item->has_photo = 0;
             $item->save();
         }
         
         Storage::disk('public')->delete('storage/city_photos/' . $request->id . '.jpg');
         
        return response()->json([
            'status' => 200,
            'data' => $item,
            'message' => 'Файл удален.'
        ]);
    }
    
}

