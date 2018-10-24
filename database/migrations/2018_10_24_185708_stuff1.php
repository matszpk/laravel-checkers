<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Stuff1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('games', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('player1_id');
            $table->unsignedInteger('player2_id');
            $table->foreign('player1_id')->references('id')->on('users')->
                    onDelete('cascade')->nullable();
            $table->foreign('player2_id')->references('id')->on('users')->
                    onDelete('cascade')->nullable();
            $table->timestamps();
            $table->timestamp('begin1_at')->nullable();
            $table->timestamp('begin2_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->enum('result',['draw','winner1','winner2'])->nullable();
        });

        Schema::create('moves', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ingame_id');
            $table->foreign('ingame_id')->
                    references('id')->on('games')->onDelete('cascade');
            $table->boolean('done_by_player1');
            $table->unsignedTinyInteger('startpos');
            $table->unsignedTinyInteger('endpos');
            $table->timestamp('done_at');
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
        Schema::dropIfExists('moves');
        Schema::dropIfExists('games');
    }
}
