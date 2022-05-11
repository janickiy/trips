<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('artifact_id')->index();
            $table->unsignedBigInteger('created_by_user_id')->index();
            $table->unsignedTinyInteger('category_id'); // 255
            $table->string('title')->nullable();
            $table->integer('departure_city_id')->nullable();
            $table->integer('arrival_city_id')->nullable();
            $table->string('departure_iata_code')->nullable();
            $table->string('arrival_iata_code')->nullable();
            $table->integer('departure_unix_time')->nullable();
            $table->integer('arrival_unix_time')->nullable();
            $table->integer('flight_number')->nullable();
            $table->string('carrier')->nullable();
            $table->integer('fly_min')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets_attributes');
    }
}
