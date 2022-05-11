<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityDatesAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities_attributes', function (Blueprint $table) {
            $table->integer('departure_date')->nullable();
            $table->integer('arrival_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities_attributes', function (Blueprint $table) {
            $table->dropColumn('departure_date');
            $table->dropColumn('arrival_date');
        });
    }
}
