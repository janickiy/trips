<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CityTicketCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
        
        DB::table('city_ticket_categories')->insert([
            [
                'name' => 'Авиа', 
                'image' => 'images/phone_icon.png', 
                'created_at' => '2020-10-09 13:56:55',
                'updated_at' => '2020-10-09 13:56:55'
            ],
            [
                'name' => 'Ж/д', 
                'image' => 'images/phone_icon.png', 
                'created_at' => '2020-10-09 13:56:55',
                'updated_at' => '2020-10-09 13:56:55'
            ],
            [
                'name' => 'Авто', 
                'image' => 'images/phone_icon.png', 
                'created_at' => '2020-10-09 13:56:55',
                'updated_at' => '2020-10-09 13:56:55'
            ],
            [
                'name' => 'Паром', 
                'image' => 'images/phone_icon.png', 
                'created_at' => '2020-10-09 13:56:55',
                'updated_at' => '2020-10-09 13:56:55'
            ]
        ]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_ticket_categories');
    }
}
