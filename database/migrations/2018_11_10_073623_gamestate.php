<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Gamestate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('games', function (Blueprint $table) {
            $table->char('board', 100);
            $table->boolean('player1_move');
            $table->unsignedTinyInteger('prev_start')->nullable();
            $table->unsignedTinyInteger('beat')->nullable();
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
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('board');
            $table->dropColumn('player1_move');
            $table->dropColumn('prev_start');
            $table->dropColumn('beat');
        });
    }
}
