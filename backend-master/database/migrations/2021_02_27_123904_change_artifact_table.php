<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeArtifactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('artifacts', function (Blueprint $table) {
            $table->dropColumn('artifact_parent_id');
            $table->unsignedBigInteger('parent_artifact_id')->index()->after('artifact_id');
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
            $table->dropColumn('parent_artifact_id');
            $table->unsignedBigInteger('artifact_parent_id')->index()->after('artifact_id');
        });
    }
}
