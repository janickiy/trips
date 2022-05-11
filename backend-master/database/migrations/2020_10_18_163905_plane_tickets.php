<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlaneTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plane_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->default(0);
            $table->bigInteger('city_ticket_id')->default(0); // Какому билету принадлежит информация
            
            $table->string('title')->nullable();
            $table->string('flight_number')->nullable();
            $table->string('carrier')->nullable();
            
            $table->string('departure_iata_code')->nullable();
            $table->bigInteger('departure_unix_time')->default(0);
            $table->timestamp('departure_at')->nullable();

            $table->string('arrival_iata_code')->nullable();
            $table->bigInteger('arrival_unix_time')->default(0);
            $table->timestamp('arrival_at')->nullable();
            
            $table->bigInteger('fly_min')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plane_tickets');
    }
}
