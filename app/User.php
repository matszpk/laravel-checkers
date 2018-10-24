<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function gamesAsPlayer1()
    {
        return $this->hasMany('App\Game', 'player1_id');
    }

    public function gamesAsPlayer2()
    {
        return $this->hasMany('App\Game', 'player2_id');
    }
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }
}
