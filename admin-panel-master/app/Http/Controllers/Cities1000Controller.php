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
use App\Cities1000;
use Carbon\Carbon;

class Cities1000Controller extends Controller
{

    public function __construct()
    {
        $this->storageFolder = "cities_1000";
        $this->feature_classes = [
            "A" => "country, state, region",
            "H" => "stream, lake",
            "L" => "parks,area",
            "P" => "city, village",
            "R" => "road, railroad",
            "S" => "spot, building, farm",
            "T" => "mountain,hill,rock",
            "U" => "undersea",
            "V" => "forest,heath",
        ];

        $this->feature_codes = [
            "PPL" =>"населенный пункт",
            "PPLA" =>"центр административного деления первого порядка",
            "PPLC" =>"столица политического образования",
            "PPLA2" =>"центр административного деления второго порядка",
            "PPLW" =>"разрушенный населенный пункт",
            "PPLA3" =>"центр административного деления третьего порядка",
            "PPLX" =>"участок населенного пункта",
            "PPLA4" =>"центр административного деления четвертого порядка",
            "PPLL" =>"населенный пункт",
            "PPLS" =>"населенные пункты",
            "PPLQ" =>"покинутый населенный пункт",
            "PPLF" =>"деревня",
            "PPLA5" =>"seat of a fifth-order administrative division",
            "PPLG" =>"здание правительства политического образования",
            "PPLH" =>"historical populated place",
            "PPLCH" =>"historical capital of a political entity",
            "PPLR" =>"религиозная община",
            "STLMT" =>"еврейские поселения",
        ];

        $this->diacritics = [
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'æ' => 'a',
            'ã' => 'a',
            'å' => 'a',
            'ā' => 'a',
            'ā' => 'a',
            'ᾶ' => 'a',
            'a' => 'a',
            'ă' => 'a',
            
            'ć' => 'c',
            'ĉ' => 'c', 
            'č' => 'c', 
            'ç' => 'c',
            
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ẽ' => 'e',
            'ē' => 'e',
            'ĕ' => 'e',
            'ė' => 'e',
            'ë' => 'e',
            'ě' => 'e',
            'ę' => 'e',
            
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            
            'õ' => 'o',
            'ὀ' => 'o',
            'ơ' => 'o',
            'ø' => 'o',
            'ǫ' => 'o',
            'ö' => 'o',
            
            'œ' => 'oe',
            'ș' => 's',
            
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ŭ' => 'u',
            'ū' => 'u',
            'ü' => 'u',
            'ǔ' => 'u',
            

        ];

/*
À => A
Á => A
Â => A
Ã => A
Ä => A
Å => A
Ç => C
È => E
É => E
Ê => E
Ë => E
Ì => I
Í => I
Î => I
Ï => I
Ñ => N
Ò => O
Ó => O
Ô => O
Õ => O
Ö => O
Ù => U
Ú => U
Û => U
Ü => U
Ý => Y
ß => s
à => a
á => a
â => a
ã => a
ä => a
å => a
ç => c
è => e
é => e
ê => e
ë => e
ì => i
í => i
î => i
ï => i
ñ => n
ò => o
ó => o
ô => o
õ => o
ö => o
ù => u
ú => u
û => u
ü => u
ý => y
ÿ => y
Ā => A
ā => a
Ă => A
ă => a
Ą => A
ą => a
Ć => C
ć => c
Ĉ => C
ĉ => c
Ċ => C
ċ => c
Č => C
č => c
Ď => D
ď => d
Đ => D
đ => d
Ē => E
ē => e
Ĕ => E
ĕ => e
Ė => E
ė => e
Ę => E
ę => e
Ě => E
ě => e
Ĝ => G
ĝ => g
Ğ => G
ğ => g
Ġ => G
ġ => g
Ģ => G
ģ => g
Ĥ => H
ĥ => h
Ħ => H
ħ => h
Ĩ => I
ĩ => i
Ī => I
ī => i
Ĭ => I
ĭ => i
Į => I
į => i
İ => I
ı => i
Ĳ => IJ
ĳ => ij
Ĵ => J
ĵ => j
Ķ => K
ķ => k
ĸ => k
Ĺ => L
ĺ => l
Ļ => L
ļ => l
Ľ => L
ľ => l
Ŀ => L
ŀ => l
Ł => L
ł => l
Ń => N
ń => n
Ņ => N
ņ => n
Ň => N
ň => n
ŉ => N
Ŋ => n
ŋ => N
Ō => O
ō => o
Ŏ => O
ŏ => o
Ő => O
ő => o
Œ => OE
œ => oe
Ŕ => R
ŕ => r
Ŗ => R
ŗ => r
Ř => R
ř => r
Ś => S
ś => s
Ŝ => S
ŝ => s
Ş => S
ş => s
Š => S
š => s
Ţ => T
ţ => t
Ť => T
ť => t
Ŧ => T
ŧ => t
Ũ => U
ũ => u
Ū => U
ū => u
Ŭ => U
ŭ => u
Ů => U
ů => u
Ű => U
ű => u
Ų => U
ų => u
Ŵ => W
ŵ => w
Ŷ => Y
ŷ => y
Ÿ => Y
Ź => Z
ź => z
Ż => Z
ż => z
Ž => Z
ž => z
ſ => s
*/  
        
    }



    public function updateCityInfo()
    {
        // Найти город, с указанным именем, который находится ближе всего к указанным координатам, и принадлежит указанной стране
    }


    public function search(Request $request)
    {
        $city = Cities1000::where('name_ru', 'like', '%' . $request->q . '%')->orderBy('id','desc')->paginate(30, ['id', 'name_ru']);
        
        return response()->json($city);
    }
    

    /**
     *  Items list:
     */
    public function itemsList(Request $request)
    {

        // Поиск по названию и сортировка по населению:
        if($request->has('q')) {

            $items = Cities1000::with('country')
            ->orWhere('name','like','%' . $request->q . '%')
            ->orWhere('name_ru','like','%' . $request->q . '%')
            ->orWhere('wiki_entity','like','%' . $request->q . '%');

            if($request->has('population_sort')) {
                if(in_array($request->population_sort, ['asc', 'desc'])) {
                    $items = $items->orderBy('population', $request->population_sort);
                } else {
                    return redirect('cp/cities');
                }
            } else {
                $items = $items->orderBy('id','desc');
            }

        } else if(!$request->has('q') and $request->has('population_sort'))  {

            // Сортировка только по населению:
            if(in_array($request->population_sort, ['asc', 'desc'])) {
                $items = Cities1000::with('country')->orderBy('population', $request->population_sort);
            } else {
                return redirect('cp/cities');
            }

        } else {

            $items = Cities1000::with('country');
            
            if($request->has('country_code')) {
                $items = $items->where('country_code', $request->country_code);
            }
            
            $items = $items->orderBy('population','desc');
        }

        // Пагинация
        $allItems = $items;
        $itemsCount = $allItems->count();
        $items = $items->paginate(20);
        $geonamesClasses = $this->feature_classes;



        return view(
            'cp.Cities1000.items_list',
            array(
                'items' => $items->appends(Input::except('page')),
            ),
            compact('items', 'itemsCount', 'geonamesClasses')
        );

    }



    /**
     *  Add item:
     */
    public function addItem()
    {
        return view('cp.Cities1000.add_item');
    }

    /**
     *  Add item POST handler:
     */
    public function postAddItem(Request $request)
    {
        $item = new Cities1000;
        $item->fill($request->all());

        $filesFields = array("");

        foreach($filesFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
            }
        }

        $item->custom_edited = 1;
        $item->need_custom_moderation = 0;
        
        $item->modification_date = Carbon::now();
        $item->created_at = Carbon::now();
        $item->updated_at = Carbon::now();
        
        if($request->has('wiki_entity')) {
            if(strlen($request->wiki_entity) > 0) {
                $item->wiki_entity = ucfirst($request->wiki_entity);
            } else {
                $item->wiki_entity = null;
            }
        } else {
            $item->wiki_entity = null;
        }

        $item->save();


        return redirect('cp/cities/edit/'.$item->id)->with('item_created', __('cities_1000.item_created'));
    }

    /**
     *  Edit item:
     */
    public function editItem($id)
    {
       $item = Cities1000::where('id', $id)->first();

       if($item == null) {
            return redirect('cp/cities');
       }

       $geonamesClasses = $this->feature_classes;
       $geonamesCodes = $this->feature_codes;
/*
       if(env('STATIC_SERVER')) {
            return view('cp.Cities1000.edit_item_static', compact('id','item', 'geonamesClasses', 'geonamesCodes'));
       }
*/ 
       return view('cp.Cities1000.edit_item', compact('id','item', 'geonamesClasses', 'geonamesCodes'));
    }

    /**
     *  Edit item POST handler:
     */
    public function postEditItem(Request $request)
    {
        $filesFields = array("");

        $item = Cities1000::where('id', $request->id)->first();

        if($item == null) {
            return redirect('cp/cities');
        }

        if($item != null) {

            // RESTORE:
            if(isset($request->restore)) {
                $item->updated_at = Carbon::now();
                $item->deleted_at = null;
                $item->save();
                
                return redirect('cp/cities/edit/'.$item->id)->with('restore_item', __('country.item_restored'));
            }
        
            // DELETE:
            if(isset($request->delete)) {
                
                $item->deleted_at = Carbon::now();
                $item->save();

                return redirect('cp/cities/edit/'.$item->id)->with('deleted_item', __('cities_1000.item_deleted'));
            }

            // UPDATE:
            if(isset($request->submit)) {

                $item->fill($request->all());
                $item->custom_edited = 1;
                $item->need_custom_moderation = 0;

                // upload files:
                foreach($filesFields as $fileField) {
                    if ($request->hasFile($fileField)) {
                        $item->$fileField = 'storage/' . $this->storageFolder . '/' . $this->store($request->file($fileField), $this->storageFolder);
                    }
                }

                // delete files:
                foreach($filesFields as $fileField) {
                    if ($request->has('delete_' . $fileField)) {
                        if(Storage::disk('public')->delete($item->$fileField)) {
                            $item->$fileField = null;
                        }
                    }
                }

                $item->wiki_entity = ucfirst($request->wiki_entity);
                $item->modification_date = Carbon::now();
                $item->updated_at = Carbon::now();
                $item->save();

                return redirect('cp/cities/edit/'.$item->id)->with('update_item', __('cities_1000.item_updated'));
            }

        }

        // ERROR:
        return redirect('cp/cities')->with('not_found', __('cities_1000.item_not_found'));

    }



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



    /**
     *  Items list:
     */
    public function citiesOnModeration(Request $request)
    {

        // Поиск по названию и сортировка по населению:
        if($request->has('q')) {

            $items = Cities1000::with('country')
            ->whereNull('wiki_entity')->where(function ($query) use ($request) {
                $query
                    ->orWhere('name','like','%' . $request->q . '%')
                    ->orWhere('name_ru','like','%' . $request->q . '%')
                    ->orWhere('wiki_entity','like','%' . $request->q . '%');
            });
            
            if($request->has('iata') and $request->iata == 'on') {
                $items = $items->whereNotNull('iata_code');
            }


            if($request->has('population_sort')) {
                if(in_array($request->population_sort, ['asc', 'desc'])) {
                    $items = $items->orderBy('population', $request->population_sort);
                } else {
                    return redirect('cp/cities');
                }
            } else {
                $items = $items->orderBy('id','desc');
            }

        } else if(!$request->has('q') and $request->has('population_sort'))  {

            // Сортировка только по населению:
            if(in_array($request->population_sort, ['asc', 'desc'])) {
                $items = Cities1000::with('country')->whereNull('wiki_entity')->orderBy('population', $request->population_sort);
            } else {
                return redirect('cp/cities');
            }

        } else {
            if($request->has('iata') and $request->iata == 'on') {
                $items = Cities1000::with('country')->whereNull('wiki_entity')->whereNotNull('iata_code')->orderBy('population','desc');
            } else {
                $items = Cities1000::with('country')->whereNull('wiki_entity')->orderBy('population','desc');
            }
        }

        // Пагинация
        $allItems = $items;
        $itemsCount = $allItems->count();
        $items = $items->paginate(20);
        $geonamesClasses = $this->feature_classes;

        return view(
            'cp.CitiesModeration.items_list',
            array(
                'items' => $items->appends(Input::except('page')),
            ),
            compact('items', 'itemsCount', 'geonamesClasses')
        );

    }


	public function checkIataDup() 
	{
        $items = Cities1000::whereNotNull('iata_code')->get();
        
        $array = [];
        
        foreach($items as $item) {
            if(strlen($item->iata_code) > 0) {
                if(isset($array[$item->iata_code])) {
                    $array[$item->iata_code] += 1;
                } else {
                    $array[$item->iata_code] = 0;
                }
            }
        }
        
        foreach($array as $iata => $count) {
           
            if($count > 0) {
                echo $iata . ' (' . $count . ')<br>';
            }
        }
        
        if(count($items) != count($array)) {
            echo 'Есть дубли!';
        } else {
            echo 'Дублей нет!';
        }
        
        // dd($array);
    }

    public function update_city_names_ru()
    {
        return view('cp.Cities1000.update_city_names_ru');
    }
    
    public function findDiacritics()
    {
        $diacritics = [];
        
        $cities = Cities1000::orderBy('population', 'desc')->get();
        
        foreach($cities as $city) {
            
            //$array = $this->checkLetters($city->name);
            $array = $this->checkLetters($city->without_diacritics);
            
            if(count($array) > 0) {
                foreach($array as $letter) {
                    if(!in_array($letter, $diacritics)) {
                        $diacritics[] = $letter;
                    }
                }
            }
        }
        
        dd($diacritics);

    }
    
    public function checkLetters($string)
    {
        $array = [];
        
        $alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z', 
        'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
        ' ', '-', '\'');

       
        $letters = str_split(mb_convert_encoding($string, "UTF-8"));
        
        foreach($letters as $letter) {
            if(!in_array($letter, $alphabet)) {
                $array[] = $letter;
            }
        }
        
        return $array;
    }
    
    
    
    public function changeDiacritics()
    {
        /*
        $name = 'Some nàme cííty';
        
        foreach($this->diacritics as $diacritic => $letter) {
            if(Str::contains($name, $diacritic)) {
                $name = str_replace($diacritic, $letter, $name);
            }
        }
        
        echo $name;
        */

        $cities = Cities1000::orderBy('population', 'desc')->get();
        
        foreach($cities as $city) {
            
            /*
            $name = $city->name;
            
            foreach($this->diacritics as $diacritic => $letter) {
                if(Str::contains($name, $diacritic)) {
                    $name = str_replace($diacritic, $letter, $name);
                }
            }
            */

            $city->without_diacritics = $this->remove_accents($city->name);
            $city->save();

        }

        return response()->json(count($cities));
    }
    
public function remove_accents($string) {
    if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

    $chars = array(
    // Decompositions for Latin-1 Supplement
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );

    $string = strtr($string, $chars);

    return $string;
}
    
}
