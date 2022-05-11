<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IsTripDescriptionNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('notes_attributes')) {
			Schema::table('notes_attributes', function (Blueprint $table) {
				$table->boolean('is_trip_description')->default(0);
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
        if(Schema::hasTable('notes_attributes')) {
			Schema::table('notes_attributes', function (Blueprint $table) {
				if (Schema::hasColumn('notes_attributes', 'is_trip_description')) {
					$table->dropColumn('is_trip_description');
				}
				
			});
		}
    }
}
