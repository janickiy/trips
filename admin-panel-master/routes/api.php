<?php

use Illuminate\Http\Request;



Route::get('dashboard_statistics', 'Dashboard\DashboardController@statistics');
Route::get('trips_statistics/dau_mau', 'Dashboard\DashboardController@getDauMau');







// Включить CORS:
Route::middleware(['cors'])->group(function () {
    Route::middleware('optimizeImages')->group(function () {
        // all images will be optimized automatically
        Route::post("upload_city_photo", "CityPhotosController@uploadCityPhoto");
        Route::post("delete_city_photo", "CityPhotosController@deletePhoto");
    });
    

    Route::post('pics', 'Api\StaticController@pics');
    Route::delete('pics', 'Api\StaticController@deletePic');
    
    
    
    Route::get('search_city', 'Cities1000Controller@search');

    
});




Route::post('contries_count', 'WikiDownloaderController@getContriesCount');
Route::post('contries_last_page', 'WikiDownloaderController@getContriesLastPage');
Route::post('contries_save_page', 'WikiDownloaderController@saveContriesLastPage');
Route::post('contries_parse_page', 'WikiDownloaderController@contriesParsePage');
Route::get('get_country_source', 'WikiDownloaderController@getCountrySource');
Route::post('add_new_country', 'WikiDownloaderController@addNewCountry');

Route::get('country', 'WikiDownloaderController@apiCountry');


Route::get('cities_1000', 'CSVParserController@read_cities_1000');
Route::get('download_cities_1000_csv', 'CSVParserController@downloadCitiesInCSV');
Route::get('download_countries_csv', 'CSVParserController@downloadCountriesInCSV');
Route::get('wikidata_test', 'WikiDataController@test');




// Поиск за координатами в радиусе 100 км:
Route::get('find_by_coordinates_test', 'WikidataCityParserLogController@findExample');
Route::get('find_by_coordinates_test/{city_id}', 'WikidataCityParserLogController@parseCityFromWikiData');
Route::get('find_by_geonames_id/{city_id}', 'WikidataCityParserLogController@parseCityFromWikiDataByGeoNamesID');

Route::get('parser_cities_info', 'WikidataCityParserLogController@parserCitiesInfo');
Route::get('parser_get_city_in_work', 'WikidataCityParserLogController@parserGetCityInWork');
Route::post('get_wikidata_city_parser_logs', 'WikidataCityParserLogController@get_wikidata_city_parser_logs');


Route::get('travelpayouts_country_parser', 'TravelPayoutsController@countriesParser');
Route::get('travelpayouts_city_parser', 'TravelPayoutsController@citiesParser');

// Route::get('summary_data', 'CpController@summaryData');

// Сгенерировать падежи:
Route::get('get_cases', 'MorphosController@getCases');

Route::get('check_travel_payout_iata_dublicates', 'TravelPayoutsController@checkIataDup');
Route::get('check_city1000_iata_dublicates', 'Cities1000Controller@checkIataDup');
Route::get('check_travel_payout_matching', 'TravelPayoutsController@matchTravelP');

Route::get('travelpayouts_empty_coordinates', 'TravelPayoutsController@emptyCoordinates');
Route::get('travelpayouts_empty_name_ru', 'TravelPayoutsController@emptyNameRu');


Route::get('check_entity_duplicates', 'WikiDownloaderController@checkEntityDuplicates');
// Поиск по Q
Route::get('find_by_entity', 'WikiDownloaderController@findByEntity');
Route::get('get_city_for_sync', 'WikiDownloaderController@getCityForSync');
Route::get('get_cities_by_q_entity', 'WikiDataController@getCitiesByQEntity');
Route::get('get_name_ru_null', 'WikiDataController@getNameRuNull');


// Замена диактириков:
Route::get('find_diacritics', 'Cities1000Controller@findDiacritics');
Route::get('change_diacritics', 'Cities1000Controller@changeDiacritics');


// Программа парсинга из викидаты:
Route::get('download_city_from_wiki_data', 'WikiDataParser\WikiDataParserController@index');


// Добавить новые фото из папки в базу городов:
Route::get('add_new_city_photos_from_storage', 'CityPhotosController@addNewCityPhotosFromStorage');


// Спарсить информацию о городе и обновить базе:
Route::get('parse_wikidata_by_q_entity', 'WikiDataParser\ParseCityController@getCitiesByQEntity');