<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DestroyPreviousSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('auto_tickets');
        Schema::dropIfExists('boat_tickets');
        Schema::dropIfExists('city_bookings');
        Schema::dropIfExists('city_booking_files');
        Schema::dropIfExists('city_files');
        Schema::dropIfExists('city_links');
        Schema::dropIfExists('city_notes');
        Schema::dropIfExists('city_tickets');
        Schema::dropIfExists('city_ticket_categories');
        Schema::dropIfExists('city_ticket_files');
        Schema::dropIfExists('plane_tickets');
        Schema::dropIfExists('train_tickets');
        Schema::dropIfExists('travel_cities');
        Schema::dropIfExists('user_travels');
        Schema::dropIfExists('websockets_statistics_entries');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
