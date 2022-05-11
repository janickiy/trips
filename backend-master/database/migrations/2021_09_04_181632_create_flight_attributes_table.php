<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('flights_attributes')) {
            Schema::create('flights_attributes', function (Blueprint $table) {
                $table->unsignedBigInteger('artifact_id')->index();
                $table->unsignedBigInteger('created_by_user_id')->index();
                
                $table->string('title')->nullable();
                $table->integer('departure_city_id')->nullable();
                $table->integer('arrival_city_id')->nullable();
                $table->string('departure_iata_code')->nullable();
                $table->string('arrival_iata_code')->nullable();
                $table->string('flight_number')->nullable();
                $table->string('carrier')->nullable();
            });
        }
        
        if (Schema::hasTable('tickets_attributes')) {
            
            Schema::table('tickets_attributes', function (Blueprint $table) { 
                $table->dropColumn('title');
                $table->dropColumn('departure_city_id');
                $table->dropColumn('arrival_city_id');
                $table->dropColumn('departure_iata_code');
                $table->dropColumn('arrival_iata_code');
                $table->dropColumn('flight_number');
                $table->dropColumn('carrier');
            });

            Schema::rename('tickets_attributes', 'transfers_attributes');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flights_attributes');
        
        if (Schema::hasTable('transfers_attributes')) {
            
            Schema::rename('transfers_attributes', 'tickets_attributes');
            
            Schema::table('tickets_attributes', function (Blueprint $table) { 
                $table->string('title')->nullable();
                $table->integer('departure_city_id')->nullable();
                $table->integer('arrival_city_id')->nullable();
                $table->string('departure_iata_code')->nullable();
                $table->string('arrival_iata_code')->nullable();
                $table->integer('flight_number')->nullable();
                $table->string('carrier')->nullable();
            });
        }
    }
}
