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
use App\Country;
use App\City;
use Illuminate\Pagination\LengthAwarePaginator;

class DuplicatesController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {      
        $items = [];
        
        $ru = DB::select("SELECT `name_ru`, `id`, `name`, `country_code`, COUNT(`name_ru`) AS `count` FROM `cities_1000` WHERE `country_code` = 'RU' GROUP BY `name_ru` HAVING `count` > 1 ORDER BY `count` desc");

        $en = DB::select("SELECT `name_ru`, `id`, `name`, `country_code`, COUNT(`name`) AS `count` FROM `cities_1000` WHERE `country_code` = 'US' GROUP BY `name` HAVING `count` > 1 ORDER BY `count` desc");

        $items = array_merge($ru, $en);

        // The total number of items. If the `$items` has all the data, you can do something like this:
        $total = count($items);

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($items, $perPage * ($currentPage - 1), $perPage);

        $paginator = new LengthAwarePaginator($currentItems, $total, $perPage, $currentPage, [
            'path'  => $request->url(),
            'query' => $request->query(),
        ]);
                
        // return response()->json($paginator);

        return view(
            'cp.Duplicates.items_list',
            array(
                'items' => $paginator->appends(Input::except('page')),
            ),
            compact('currentItems')
        );

    }

}
