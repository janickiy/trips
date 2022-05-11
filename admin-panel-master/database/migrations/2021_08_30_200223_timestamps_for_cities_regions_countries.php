<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TimestampsForCitiesRegionsCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('cities_1000')) {            
            Schema::table('cities_1000', function (Blueprint $table) {
                $table->timestamps();
                $table->softDeletes();
            });
        }
        
        if (Schema::hasTable('regions')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        
        if (Schema::hasTable('countries')) {
            Schema::table('countries', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('cities_1000')) {            
            Schema::table('cities_1000', function (Blueprint $table) {
                $table->dropTimestamps();
                $table->dropSoftDeletes();
            });
        }
        
        if (Schema::hasTable('regions')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        
        if (Schema::hasTable('countries')) {
            Schema::table('countries', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
    
}
