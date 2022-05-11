<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFileAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files_attributes', function (Blueprint $table) {
            $table->unsignedTinyInteger('upload_is_complete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files_attributes', function (Blueprint $table) {
            $table->dropColumn('upload_is_complete');
        });
    }
}
