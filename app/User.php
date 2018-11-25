<?php

namespace App;

use Illuminate\Notifications\Notifiable;
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
        'name', 'email', 'password', 'role',
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
        return $this->hasMany(Game::class, 'player1_id');
    }

    public function gamesAsPlayer2()
    {
        return $this->hasMany(Game::class, 'player2_id');
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function writtenComments()
    {
        return $this->hasMany(Comment::class, 'writer_id');
    }

    public function getName()
    {
        return $this->name;
    }
}
