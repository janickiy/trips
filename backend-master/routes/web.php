<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;



Route::get('/', 'HomeController@welcome');
Route::get('clear_app_cache', 'HomeController@clearAppCache');

// Включить CORS:
Route::middleware(['cors'])->group(function () {

    Route::get('login/{provider}',          'Api\AuthController@redirectToProvider');
    Route::get('login/{provider}/callback', 'Api\AuthController@handleProviderCallback');
    
    Route::prefix('schema')->group(function () {
        Route::get("artifact_types", "ArtifactsSchema@index");
    });

    
});

Route::get('/mail_auth_code_template', 'HomeController@authCodeTemplate');
Route::get('/create_user_token_for_testing/{user_id}', 'Api\UserController@getUserTokenForTesting');





// Удалить:
// Route::get('/apple_token_test', 'HomeController@testJWT');
