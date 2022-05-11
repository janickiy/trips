<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtifactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artifacts', function (Blueprint $table) {
            $table->bigIncrements('artifact_id'); // 2^64
            $table->unsignedBigInteger('artifact_parent_id')->index();
            $table->unsignedTinyInteger('artifact_type'); // 255
            $table->unsignedBigInteger('created_by_user_id')->index(); // 4,294,967,295
            $table->unsignedInteger('created_at'); 
            $table->unsignedInteger('last_midified_by_user_id'); 
            $table->unsignedInteger('last_midified_at');
            $table->unsignedSmallInteger('version'); // 65535           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artifacts');
    }
}
