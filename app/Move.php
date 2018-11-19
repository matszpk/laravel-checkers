<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    //
    protected $table = 'moves';

    public function ingame()
    {
        return $this->belongsTo(Game::class, 'ingame_id');
    }
}
