<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLastModifiedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('artifacts', function (Blueprint $table) {
            $table->renameColumn('last_midified_by_user_id', 'last_modified_by_user_id');
            $table->renameColumn('last_midified_at', 'last_modified_at');
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
            $table->renameColumn('last_modified_by_user_id', 'last_midified_by_user_id');
            $table->renameColumn('last_modified_at', 'last_midified_at');
        });
    }
}
