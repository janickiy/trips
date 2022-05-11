<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArtifactIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->unsignedBigInteger('version')->change();
            $table->unsignedBigInteger('order_index')->after('version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->unsignedSmallInteger('version')->change();
            $table->unsignedBigInteger('parent_artifact_id')->index()->after('artifact_id');
        });
    }
}
