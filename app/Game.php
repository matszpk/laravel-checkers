<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    //
    protected $table = 'games';

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

    public function getName()
    {
        return trans('game.beginAt') .' '. $this->created_at .' '. trans('game.by') .' '.
                $this->player1->getResults()->name .' '. trans('game.andBy') .' '.
                $this->player2->getResults()->name;
    }
}
