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
        return $this->belongsTo(User::class, 'writer_id');
    }
}
