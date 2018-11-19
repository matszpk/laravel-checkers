<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    //
    protected $table = 'moves';
    
    public $timestamps = false;
    
    protected $fillable = [ 'startpos', 'endpos', 'done_by_player1', 'done_at' ];

    public function ingame()
    {
        return $this->belongsTo(Game::class, 'ingame_id');
    }
}
