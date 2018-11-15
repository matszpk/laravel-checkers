<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    //
    protected $table = 'games';

    protected $fillable = [ 'board', 'player1_move' ];

    public function player1()
    {
        return $this->belongsTo(User::class, 'player1_id');
    }
    public function player2()
    {
        return $this->belongsTo(User::class, 'player2_id');
    }
    public function moves()
    {
        return $this->hasMany(Move::class, 'ingame_id');
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getName()
    {
        return trans('game.beginAt') .' '. $this->created_at .' '. trans('game.by') .' '.
                $this->player1->name .' '. trans('game.andBy') .' '.
                $this->player2->name;
    }
}
