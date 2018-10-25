<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Userrole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('role');
        });
        Schema::table('users', function(Blueprint $table) {
            $table->enum('role', ['PLAYER', 'ADMIN']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('role');
        });
        Schema::table('users', function(Blueprint $table) {
            $table->string('role',10);
        });
    }
}
