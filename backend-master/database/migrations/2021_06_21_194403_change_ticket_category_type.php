<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTicketCategoryType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets_attributes', function (Blueprint $table) {
            $table->integer('category_id')->nullable()->change();
            $table->string('flight_number')->change();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets_attributes', function (Blueprint $table) {
            $table->integer('category_id')->nullable(false)->change();
            $table->integer('flight_number')->nullable()->change();
        });
    }
}
