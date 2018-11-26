<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FkeyIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (config('database.default') != 'mysql')
        {
            Schema::table('moves', function(Blueprint $table) {
                $table->index('ingame_id');
            });
            Schema::table('games', function(Blueprint $table) {
                $table->index('player1_id');
                $table->index('player2_id');
            });
            Schema::table('comments', function(Blueprint $table) {
                $table->index('writer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        if (config('database.default') != 'mysql')
        {
            Schema::table('moves', function(Blueprint $table) {
                $table->dropIndex(['ingame_id']);
            });
            Schema::table('games', function(Blueprint $table) {
                $table->dropIndex(['player1_id']);
                $table->dropIndex(['player2_id']);
            });
            Schema::table('comments', function(Blueprint $table) {
                $table->dropIndex(['writer_id']);
            });
        }
    }
}
