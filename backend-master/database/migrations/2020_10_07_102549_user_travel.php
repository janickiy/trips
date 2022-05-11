<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class UserTravel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_travels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->default(0);
            $table->string('name')->nullable();
            $table->timestamps();
        });
        
        DB::table('oauth_clients')->insert(
            [
                'name' => 'trips',
                'secret' => 'KdmIhneZq7UKXaFAQgDlEVbhgs3sD6dchuHDd5wg', 
                'personal_access_client' => 1, 
                'redirect' => 'http://localhost', 
                'password_client' => 0, 
                'revoked' => 0, 
                'created_at' => '2020-10-09 13:56:55',
                'updated_at' => '2020-10-09 13:56:55'
            ]
        );
        
        DB::table('oauth_personal_access_clients')->insert(
            [
                'client_id' => 1, 
                'created_at' => '2020-10-09 13:56:55',
                'updated_at' => '2020-10-09 13:56:55'
            ]
        );
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_travels');
    }
}
