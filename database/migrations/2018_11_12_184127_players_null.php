<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlayersNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        //
        Schema::table('games', function (Blueprint $table) {
            $table->unsignedBigInteger('player1_id')->nullable()->change();
            $table->unsignedBigInteger('player2_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        //
        Schema::table('games', function (Blueprint $table) {
            $table->unsignedBigInteger('player1_id')->nullable(false)->change();
            $table->unsignedBigInteger('player2_id')->nullable(false)->change();
        });
    }
}
