<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('comments', function(Blueprint $table) {
            $table->index('created_at');
        });
        Schema::table('games', function(Blueprint $table) {
            $table->index('created_at');
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
        Schema::table('comments', function(Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
        Schema::table('games', function(Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
}
