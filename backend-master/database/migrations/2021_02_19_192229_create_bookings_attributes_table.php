<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('artifact_id')->index();
            $table->unsignedBigInteger('created_by_user_id')->index();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->double('latitude', 11, 8)->nullable(); // 49.2940892
            $table->double('longitude', 11, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings_attributes');
    }
}
