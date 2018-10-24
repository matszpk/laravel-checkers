<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    //
    protected $table = 'game';

    public function player1()
    {
        return $this->belongsTo('App\User', 'player1_id');
    }
    public function player2()
    {
        return $this->belongsTo('App\User', 'player2_id');
    }
    public function moves()
    {
        return $this->hasMany('App\Move', 'ingame_id');
    }
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }
}
