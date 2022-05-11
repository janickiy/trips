<?php 

Route::get('test_chat', 'HomeController@test_chat')->name('home');
Route::get('run_chat', 'HomeController@run_chat');


Route::get('', 'HomeController@welcome');

Auth::routes(['verify' => true, 'register' => false]);

Route::get('/home', 'HomeController@index')->name('home');

## copy from storages.php
Route::get('storage/{folder}/{filename}', 'HomeController@storageFolderFilename');


## copy from web_green_admin_routes.php

/**
 *    Route prefix:
 */
Route::group(["prefix" => "cp", "middleware" => ["auth", "localization", "administrator"]], function() {

    /**
     *    Dashboard:
     */
    Route::get("dashboard", "Dashboard\DashboardController@index");
    
    
    
    
    Route::post('upload_cities_csv', 'CSVParserController@UploadCitiesCSV');
    
    
    Route::get("/", "CpController@index");
    
    Route::get("wikitest", "WikiController@index");
    Route::get("prepared_queries", "WikiController@preparedQueries");
    Route::post("prepared_queries", "WikiController@preparedQueries");
    
    Route::get("instanse_of", "WikiController@getInstanceOf");
    Route::get("get_country", "WikiController@getCountryView");
    Route::get("city_properties", "WikiController@cityProperties");
    Route::get("download_countries", "WikiDownloaderController@downloadCountries");
    
    
    /**
     *    User web-routes:
     */
    Route::get("user", "UserController@itemsList");
    Route::get("user/add", "UserController@addItem");
    Route::get("user/edit/{id}", "UserController@editItem");
        Route::post("user/add", "UserController@postAddItem");
        Route::post("user/edit", "UserController@postEditItem");
    
    /**
     *    Country web-routes:
     */
    Route::get("country", "CountryController@itemsList");
    Route::get("country/add", "CountryController@addItem");
    Route::get("country/edit/{id}", "CountryController@editItem");
        Route::post("country/add", "CountryController@postAddItem");
        Route::post("country/edit", "CountryController@postEditItem");
    
    /**
     *    Cities1000 web-routes:
     */
    Route::get("cities", "Cities1000Controller@itemsList");
    Route::get("cities/add", "Cities1000Controller@addItem");
    Route::get("cities/edit/{id}", "Cities1000Controller@editItem");
        Route::post("cities/add", "Cities1000Controller@postAddItem");
        Route::post("cities/edit", "Cities1000Controller@postEditItem");
    
    Route::get("cities_on_moderation", "Cities1000Controller@citiesOnModeration");
    
    
    Route::get("cities-map", "CitiesMapController@index");
    
    
    
    /**
     *    WikidataCityParserLog web-routes:
     */
    Route::get("wikidata_city_parser_log", "WikidataCityParserLogController@itemsList");
    Route::get("wikidata_city_parser_log/add", "WikidataCityParserLogController@addItem");
    Route::get("wikidata_city_parser_log/edit/{id}", "WikidataCityParserLogController@editItem");
        Route::post("wikidata_city_parser_log/add", "WikidataCityParserLogController@postAddItem");
        Route::post("wikidata_city_parser_log/edit", "WikidataCityParserLogController@postEditItem");
    
    Route::get("geodata_cities_wikidata", "WikidataCityParserLogController@geodataCitiesWikidata");
    Route::get("wikidata_city_parser_full", "WikidataCityParserLogController@wikidata_city_parser_full");
    Route::get("geodata_cities_wikidata_by_geo_id", "WikidataCityParserLogController@geodataCitiesWikidataByGeoID");
    
    
    
    
    /**
     *    WikiData Cities web-routes:
     */
    Route::get("wikidata_cities", "WikiData\WikiDataController@itemsList");
    Route::get("wikidata_cities/add", "WikiData\WikiDataController@addItem");
    Route::get("wikidata_cities/edit/{id}", "WikiData\WikiDataController@editItem");
        Route::post("wikidata_cities/add", "WikiData\WikiDataController@postAddItem");
        Route::post("wikidata_cities/edit", "WikiData\WikiDataController@postEditItem");
    
    
    /**
     *    WikiData Countries web-routes:
     */
    Route::get("wikidata_countries", "WikiData\CountryController@itemsList");
    Route::get("wikidata_countries/add", "WikiData\CountryController@addItem");
    Route::get("wikidata_countries/edit/{id}", "WikiData\CountryController@editItem");
        Route::post("wikidata_country/add", "WikiData\CountryController@postAddItem");
        Route::post("wikidata_country/edit", "WikiData\CountryController@postEditItem");
    
    /**
     *    Wikidata Regions web-routes:
     */
    Route::get("wikidata_regions", "WikiData\RegionController@itemsList");
    Route::get("wikidata_regions/add", "WikiData\RegionController@addItem");
    Route::get("wikidata_regions/edit/{id}", "WikiData\RegionController@editItem");
        Route::post("wikidata_region/add", "WikiData\RegionController@postAddItem");
        Route::post("wikidata_region/edit", "WikiData\RegionController@postEditItem");
    
    
    /**
     *    Travelpayout Cities web-routes:
     */
    Route::get("travelpayouts", "TravelpayoutController@itemsList");
    Route::get("travelpayouts/add", "TravelpayoutController@addItem");
    Route::get("travelpayouts/edit/{id}", "TravelpayoutController@editItem");
        Route::post("travelpayout/add", "TravelpayoutController@postAddItem");
        Route::post("travelpayout/edit", "TravelpayoutController@postEditItem");
    
    
    /**
     *    Travelpayout Countries web-routes:
     */
    Route::get("travelpayouts_countries", "Travelpayout\CountryController@itemsList");
    Route::get("travelpayouts_countries/add", "Travelpayout\CountryController@addItem");
    Route::get("travelpayouts_countries/edit/{id}", "Travelpayout\CountryController@editItem");
        Route::post("travelpayouts_countries/add", "Travelpayout\CountryController@postAddItem");
        Route::post("travelpayouts_countries/edit", "Travelpayout\CountryController@postEditItem");
        Route::get("travelpayouts_countries/parse_all", "Travelpayout\CountryController@countriesParser");
    
    
        
    /**
     *    GeoNames Cities web-routes:
     */
    Route::get("geonames_cities", "GeoNames\CityController@itemsList");
    Route::get("geonames_cities/add", "GeoNames\CityController@addItem");
    Route::get("geonames_cities/edit/{id}", "GeoNames\CityController@editItem");
        Route::post("geonames_cities/add", "GeoNames\CityController@postAddItem");
        Route::post("geonames_cities/edit", "GeoNames\CityController@postEditItem");
    
      
    Route::get("update_city_names_ru", "Cities1000Controller@update_city_names_ru");
    
    //Route::get("city_photos", "CityPhotosController@cityPhotosList");
    
    
    Route::middleware('optimizeImages')->group(function () {
        // all images will be optimized automatically
        Route::post("upload_city_photo", "CityPhotosController@uploadCityPhoto");
    });
    
    
    /**
     *    City Photos web-routes:
     */
    Route::get("city_photos", "CityPhotosController@itemsList");
    Route::get("test_upload_photos", "CityPhotosController@testUploadPhotos");
    Route::post("test_upload_photos", "CityPhotosController@testUploadPhotos")->middleware('optimizeImages');
    
    Route::post("delete_city_photo", "CityPhotosController@deletePhoto");
    /*
    Route::get("cities/add", "Cities1000Controller@addItem");
    Route::get("cities/edit/{id}", "Cities1000Controller@editItem");
        Route::post("cities/add", "Cities1000Controller@postAddItem");
        Route::post("cities/edit", "Cities1000Controller@postEditItem");
    
    Route::get("cities_on_moderation", "Cities1000Controller@citiesOnModeration");
    */
    
        /**
         *    App user web-routes:
         */
        Route::get("trips_user", "TripsApp\UserController@itemsList");
        Route::get("trips_user/add", "TripsApp\UserController@addItem");
        Route::get("trips_user/edit/{id}", "TripsApp\UserController@editItem");
            Route::post("trips_user/add", "TripsApp\UserController@postAddItem");
            Route::post("trips_user/edit", "TripsApp\UserController@postEditItem");
            
            Route::post("export_users_csv", "TripsApp\UserController@exportUsersCSV");
    
    
    /**
     *    Region web-routes:
     */
    Route::get("region", "RegionController@itemsList");
    Route::get("region/add", "RegionController@addItem");
    Route::get("region/edit/{id}", "RegionController@editItem");
        Route::post("region/add", "RegionController@postAddItem");
        Route::post("region/edit", "RegionController@postEditItem");
      
      
      
        Route::get("download_sql", "SQLite\ExportController@download");
       
    
    /**
     *    Дубликаты web-routes:
     */
    Route::get("duplicates", "DuplicatesController@index");
    /*
    Route::get("duplicates/add", "RegionController@addItem");
    Route::get("duplicates/edit/{id}", "RegionController@editItem");
        Route::post("duplicates/add", "RegionController@postAddItem");
        Route::post("duplicates/edit", "RegionController@postEditItem");
    */
    
    
    
    
    
    });
    



Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('clear_app_cache', 'HomeController@clearAppCache');

# test route cache
Route::get('/huynya', 'HomeController@welcome');
