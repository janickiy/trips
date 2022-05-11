<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTitkectAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets_attributes', function (Blueprint $table) {
            $table->renameColumn('departure_unix_time', 'departure_at');
            $table->renameColumn('arrival_unix_time', 'arrival_at');
            $table->dropColumn('fly_min');
        });
        
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets_attributes', function (Blueprint $table) {
            $table->renameColumn('departure_at', 'departure_unix_time');
            $table->renameColumn('arrival_at', 'arrival_unix_time');
            $table->integer('fly_min')->nullable();
        });
    }
}
