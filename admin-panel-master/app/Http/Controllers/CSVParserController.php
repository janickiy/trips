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

use App\City1000;
use App\Cities1000;

use League\Csv\Writer;
use Wikidata\Wikidata;
use Carbon\Carbon;

use League\Csv\Reader;
use League\Csv\Statement;

// https://packagist.org/packages/aternus/geonames-client

class CSVParserController extends Controller
{

    /**
     *  Store any file:
     */
    public function store($file, $folder)
    {
        # Truncate long file names:
        $uploadedFilename = str_limit(
            str_before($file->getClientOriginalName(),
            '.' . $file->getClientOriginalExtension()), 
            100, 
            ''
        );
        
        # Add some hash:
        $goodFilename = Str::slug($uploadedFilename . '-' . str_random(5), '-') . '.' . $file->getClientOriginalExtension();
        
        # Put:
        Storage::disk('public')->putFileAs('storage/' . $folder . '/', $file, $goodFilename);
        
        return $goodFilename;
    }

    public function __construct()
    {
        $this->cities1000 = 'cities1000.txt';
        
        
        $this->heders = [
        'geonameid' => 'integer id of record in geonames database',
        'name' => 'name of geographical point (utf8) varchar(200)',
        'asciiname' => 'name of geographical point in plain ascii characters, varchar(200)',
        'alternatenames' => 'alternatenames, comma separated, ascii names automatically transliterated, convenience attribute from alternatename table, varchar(10000)',
        'latitude' => 'latitude in decimal degrees (wgs84)',
        'longitude' => 'longitude in decimal degrees (wgs84)',
        'feature_class' => 'see http://www.geonames.org/export/codes.html, char(1)',
        'feature_code' => 'see http://www.geonames.org/export/codes.html, varchar(10)',
        'country_code' => 'ISO-3166 2-letter country code, 2 characters',
        'cc2' => 'alternate country codes, comma separated, ISO-3166 2-letter country code, 200 characters',
        'admin1_code' => 'fipscode (subject to change to iso code), see exceptions below, see file admin1Codes.txt for display names of this code; varchar(20)',
        'admin2_code' => 'code for the second administrative division, a county in the US, see file admin2Codes.txt; varchar(80)',
        'admin3_code' => 'code for third level administrative division, varchar(20)',
        'admin4_code' => 'code for fourth level administrative division, varchar(20)',
        'population' => 'bigint (8 byte int)', 
        'elevation' => 'in meters, integer',
        'dem' => 'digital elevation model, srtm3 or gtopo30, average elevation of 3""x3"" (ca 90mx90m) or 30""x30"" (ca 900mx900m) area in meters, integer. srtm processed by cgiar/ciat.',
        'timezone' => 'the iana timezone id (see file timeZone.txt) varchar(40)',
        'modification_date' => 'date of last modification in yyyy-MM-dd format',
        ];
        
        $this->integerFields = [
            "elevation",
             "dem",
             "timezone",
        ];
    }

    public function read_cities_1000()
    {
        ini_set("auto_detect_line_endings", true);
        //$file = Storage::disk('public')->get($this->cities1000);
        //$file = File::get(storage_path('app/public/'.$this->cities1000));
       // dd($file[1]);
        
        die('спаршено!');
        
        $errorsArray = [];
        $duplicatesArray = [];
        
        $handle = @fopen(storage_path('app/public/'.$this->cities1000), "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                
                //$parts = explode('\t', $buffer);
                $parts = preg_split('/[\t]/', $buffer);
                
                $array = [];
                $i = 0;
                
                foreach($this->heders as $header => $headerDescription) {
                    
                    if(isset($parts[$i])) {
                        $array[$header] = $parts[$i];
                    } else {
                        Log::info('Нема індекса!', $parts);
                        $errorsArray[$parts[0]] = $parts;
                    }
                    $i++;
                }
                
                if(count($array) == 19) {
                    // dump($array);
                    // dd($array);

                    $exist = City1000::where('geonameid', $array['geonameid'])->first();
                    
                    if($exist == null) {
                        $city = new City1000;
                     
                        foreach($this->heders as $header => $headerDescription) {
                            
                            if(isset($array[$header])) {
                                
                                if(in_array($header, $this->integerFields)) {
                                    $city->$header = intval ($array[$header]);
                                } else {
                                    $city->$header = $array[$header];
                                }
                            }
                            
                        }

                        $city->save();
                    } else {
                        $duplicatesArray[] = $exist;
                    }
                    
                } else {
                    $errorsArray[$parts[0]] = $parts;
                }
            }
            if (!feof($handle)) {
                echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
            }
            fclose($handle);
            
            dump('errors array:', $errorsArray);
            dd('duplicates array:', $duplicatesArray);
        }
    }

    public function downloadCitiesInCSV(Request $request)
    {
        $filename = 'cities_dump_'.str_replace(' ', '_', str_replace(':', '_', Carbon::now())).'.csv';
        
        $headers = array(
                'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Disposition' => 'attachment; filename=' . $filename,
                'Expires' => '0',
                'Pragma' => 'public',
            );


        $handle = fopen($filename, 'w');
        
        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM
        
        fputcsv($handle, [
            "id",
            "wiki_entity",
            "geonameid",
            "name",
            "name_ru",
            "genitive",
            "dative",
            "accusative",
            "instrumental",
            "prepositional",
            "latitude",
            "longitude",
            //"feature_class",
            //"feature_code",
            "country_code",
            'iata_code',
            //"region",
            //"admin1_code",
            //"admin2_code",
            //"admin3_code",
            //"admin4_code",
            "population",
            "modification_date",
            "custom_edited",
        ], ";"  );


        // Поиск по названию и сортировка по населению:
        if($request->has('q')) {
            
            $items = DB::table("cities_1000")
                ->orWhere('name','like','%' . $request->q . '%')
                ->orWhere('name_ru','like','%' . $request->q . '%');

            if($request->has('population_sort')) {
                if(in_array($request->population_sort, ['asc', 'desc'])) {
                    $items = $items->orderBy('population', $request->population_sort);
                } else {
                    $items = $items->orderBy('population', 'desc');
                }
            } else {
                $items = $items->orderBy('id','desc');
            }
            
        } else if(!$request->has('q') and $request->has('population_sort'))  {
            
            // Сортировка только по населению:
            if(in_array($request->population_sort, ['asc', 'desc'])) {
                $items = DB::table("cities_1000")->orderBy('population', $request->population_sort);
            } else {
                return redirect('cp/cities_1000');
            }
            
            
        } else {
            
            $items = DB::table("cities_1000")->orderBy('id','desc');
            
        }
        
        
        
        $items->chunk(5000, function ($data) use ($handle) {
            foreach ($data as $row) {

                if($row->deleted_at == null) {
                    // Add a new row with data
                    fputcsv($handle, [
                        $row->id,
                        $row->wiki_entity,
                        $row->geonameid,
                        $row->name,
                        $row->name_ru,
                        $row->genitive,
                        $row->dative,
                        $row->accusative,
                        $row->instrumental,
                        $row->prepositional,
                        $row->latitude,
                        $row->longitude,
                        //$row->feature_class,
                        //$row->feature_code,
                        $row->country_code,
                        $row->iata_code,
                        //$row->region,
                        //$row->admin1_code,
                        //$row->admin2_code,
                        //$row->admin3_code,
                        //$row->admin4_code,
                        $row->population,
                        $row->modification_date,
                        $row->custom_edited,
                    ], ';');
                }
            }
        });

        fclose($handle);

        return response()->download(public_path() . '/' . $filename, $filename, $headers)->deleteFileAfterSend(true);
    }
    
    
    public function downloadCountriesInCSV(Request $request)
    {
        $filename = 'countries_dump_'.str_replace(' ', '_', str_replace(':', '_', Carbon::now())).'.csv';
        
        $headers = array(
                'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Disposition' => 'attachment; filename=' . $filename,
                'Expires' => '0',
                'Pragma' => 'public',
            );


        $handle = fopen($filename, 'w');
        
        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM
        
        fputcsv($handle, [
            "id",
            "wiki_id",
            "wiki_link",
            "name_en",
            "name_ru",
            "code",
            //"vi",
            //"tv",
            //"ro",
            //"pr",
            //"da",
            "moderated",
            "updated_at",
        ], ";"  );


        // Поиск по названию и сортировка по населению:
        if($request->has('q')) {
            
            $items = DB::table("countries")
                ->orWhere('name_ru','like','%' . $request->q . '%')
                ->orWhere('name_en','like','%' . $request->q . '%');

            if($request->has('population_sort')) {
                if(in_array($request->population_sort, ['asc', 'desc'])) {
                    $items = $items->orderBy('population', $request->population_sort);
                } else {
                    $items = $items->orderBy('population', 'desc');
                }
            } else {
                $items = $items->orderBy('id','desc');
            }
            
        } else if(!$request->has('q') and $request->has('population_sort'))  {
            
            // Сортировка только по населению:
            if(in_array($request->population_sort, ['asc', 'desc'])) {
                $items = DB::table("countries")->orderBy('population', $request->population_sort);
            } else {
                return redirect('cp/cities_1000');
            }
            
            
        } else {
            
            $items = DB::table("countries")->orderBy('id','desc');
            
        }
        
        
        
        $items->chunk(5000, function ($data) use ($handle) {
            foreach ($data as $row) {
                
                if($row->deleted_at == null) {
                    // Add a new row with data
                    fputcsv($handle, [
                        $row->id,
                        $row->wiki_id,
                        $row->wiki_link,
                        $row->name_en,
                        $row->name_ru,
                        $row->code,
                        //$row->vi,
                        //$row->tv,
                        //$row->ro,
                        //$row->pr,
                        //$row->da,
                        $row->moderated,
                        $row->updated_at,
                    ], ';');
                }
            }
        });

        fclose($handle);

        return response()->download(public_path() . '/' . $filename, $filename, $headers)->deleteFileAfterSend(true);
    }
    
    public function UploadCitiesCSV(Request $request)
    {
        if ($request->hasFile('csv_file')) {
            $fileURL = $this->store($request->file('csv_file'), 'city_csv');
        } else {
            return redirect()->back();
        }
        
        // Читаю заголовки:
        $csv = Reader::createFromPath(Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . 'storage/city_csv/' . $fileURL, 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setDelimiter(';');

        $stmt = (new Statement());
        $records = $stmt->process($csv);
        
        // Обновляю поля:
        if(count($records) > 0) {
            foreach ($records as $record) {
                
                $city = Cities1000::where('id', $record['id'])->first();
                $cityArray = $city->toArray();
                
                if($city != null) {
                    foreach ($record as $field => $value) {
                        if($field != 'id') {
                            if(array_key_exists($field, $cityArray)) {
                                $city->$field = $value;
                            }
                        }
                    }
                    $city->save();
                }
            } 
        }        
        
        unset($csv);
        unset($records);
        unset($stmt);
        
        sleep(2);
        Storage::disk('public')->delete('storage/city_csv/' . $fileURL);
        return redirect()->back();
    }
    

    
}