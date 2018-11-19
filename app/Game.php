<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    //
    protected $table = 'games';

    protected $fillable = [ 'board', 'player1_move', 'result',
        'last_start', 'last_beat', 'player1_move', 'end_at' ];

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
        $player1Name = $this->player1!=NULL ? $this->player1->name : '-';
        $player2Name = $this->player2!=NULL ? $this->player2->name : '-';
        return trans('game.beginAt') .' '. $this->created_at .' '. trans('game.by') .' '.
                $player1Name .' '. trans('game.andBy') .' '. $player2Name;
    }
}
