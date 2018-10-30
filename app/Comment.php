<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $table = 'comments';

    public $fillable = [ 'content', 'writer' ];

    public function commentable()
    {
        return $this->morphTo('commentable');
    }
    public function writtenBy()
    {
        return $this->belongsTo('App\User', 'writer_id');
    }
}
