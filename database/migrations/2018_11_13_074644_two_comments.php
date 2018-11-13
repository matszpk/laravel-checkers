<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TwoComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::drop('comments');
        Schema::create('ucomments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('commentable_id');
            $table->foreign('commentable_id')->references('id')->on('users')->
                    onDelete('cascade');
            $table->text('content');
            $table->unsignedBigInteger('likes')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('writer_id');
            $table->foreign('writer_id')->references('id')->on('users')->
                    onDelete('cascade');
        });
        Schema::create('gcomments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('commentable_id');
            $table->foreign('commentable_id')->references('id')->on('games')->
                    onDelete('cascade');
            $table->text('content');
            $table->unsignedBigInteger('likes')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('writer_id');
            $table->foreign('writer_id')->references('id')->on('users')->
                    onDelete('cascade');
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
        Schema::drop('gcomments');
        Schema::drop('ucomments');
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('commentable');
            $table->text('content');
            $table->unsignedBigInteger('likes')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('writer_id');
            $table->foreign('writer_id')->references('id')->on('users')->
                    onDelete('cascade');
        });
    }
}
