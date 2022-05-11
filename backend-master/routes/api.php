<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 *  Документация: https://data.trips.im/api/trips-documentation
 */

// Включить CORS:
Route::middleware(['cors'])->group(function () {

    // Регистрация и вход:
    // Route::post('register', 'Api\RegisterController@register');
    Route::post('login', 'Api\RegisterController@login');
    Route::post('send_auth_email', 'Api\RegisterController@sendAuthEmail');

    Route::get('login/{provider}', 'Api\AuthController@providerLogin');
    Route::post('login/{provider}', 'Api\AuthController@providerLogin');

    /**
     *  Защита паролем:
     */
    Route::middleware(['auth:api', 'checkBan'])->group(function () {

        /**
         *  Пользователь:
         */
        Route::get("user", "Api\UserController@read"); 
        Route::post("user_name", "Api\UserController@changeUserName"); 
        Route::post("logout", "Api\UserController@logout"); 
        
        Route::get("delete_account", "Api\UserController@createDeleteAccountCode"); 
        Route::post("delete_account", "Api\UserController@deleteAccount"); 
        // Route::post("change_password", "Api\UserController@changePassword"); 
      
        /**
         *  WSS:
         */
        Route::get("get_wss_link", "WebSocket\AccessCodeController@getWssLink"); 
        Route::get("check_wss_code", "WebSocket\AccessCodeController@checkWssCode"); 
        
		// Снэпшот базы данных городов: 
		Route::get('get_cities_updates', 'Api\CityController@getCitiesUpdate');
		Route::post('get_cities_updates', 'Api\CityController@getCitiesUpdate');
			
		  
    });
    
    Route::post("dispatch_job", "HomeController@dispatchNewJob"); 
    
    Route::get('storage_statistics', 'FileController@storageStatistics');
    
    
});
    
Route::get("s3_test", "FileUpload\FileUploadController@fileUpload");
 
    
    
Route::middleware(['auth:api', 'checkBan'])->group(function () {
    Route::get("user", "Api\UserController@read");
   
    Route::get('/file_upload/{any?}', 'TusController@index')->where('any', '.*');
    Route::post('/file_upload/{any?}', 'TusController@index')->where('any', '.*');
    Route::put('/file_upload/{any?}', 'TusController@index')->where('any', '.*');
    Route::patch('/file_upload/{any?}', 'TusController@index')->where('any', '.*');
    Route::delete('/file_upload/{any?}', 'TusController@index')->where('any', '.*');

   // GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS
});

//Route::options('/file_upload/{any?}', 'TusController@index')->where('any', '.*');
Route::middleware(['cors'])->group(function () {
    Route::options('/file_upload/{any?}', 'TusController@index')->where('any', '.*');
});

Route::middleware(['auth:api', 'checkBan', 'cors'])->group(function () {
    Route::any('file_download', 'FileController@download');
});


