<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Likes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('games', function(Blueprint $table) {
            $table->unsignedInteger('likes')->default(0);
        });
        // Early migration - no data, do not write in that way!
        Schema::table('comments', function(Blueprint $table) {
            $table->dropColumn('likes')->default(0);
        });
        Schema::table('comments', function(Blueprint $table) {
            $table->unsignedInteger('likes')->default(0);
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
        Schema::table('games', function(Blueprint $table) {
            $table->dropColumn('likes');
        });
        // Early migration - no data, do not write in that way!
        Schema::table('comments', function(Blueprint $table) {
            $table->dropColumn('likes')->default(0);
        });
        Schema::table('comments', function(Blueprint $table) {
            $table->unsignedInteger('likes');
        });
    }
}
