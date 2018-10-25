<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cmtwriter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedInteger('writer_id');
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
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['writer_id']);
            $table->dropColumn('writer_id');
        });
    }
}
