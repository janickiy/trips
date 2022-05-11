<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('password_expired_at')->useCurrent();
            
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');  
            }
            
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');  
            }
            
            if (Schema::hasColumn('users', 'banned')) {
                $table->dropColumn('banned');  
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_expired_at');
        });
    }
}
