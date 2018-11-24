<?php

namespace App\Http\Controllers;

use App\Comment;
use App\User;
use Illuminate\Http\Request;

trait CommentableController
{
    public function addComment(Request $request, string $commentableId)
    {
        $data = (Self::MainModel)::findOrFail($commentableId);
        $this->authorize('giveOpinion', $data);
        // validation
        $this->validate($request, [ 'content' => 'required|string|max:30000' ]);

        $comment = new Comment(['content' => $request->input('content') ]);
        $comment->writtenBy()->associate($request->user());
        $data->comments()->save($comment);
        return back();
    }
    
    public function getComments($data, $pageLength = 15)
    {
        $comments = $data->comments()->orderBy('created_at', 'desc')->paginate($pageLength);
        // get writers for comments
        $writerIds = $comments->pluck('writer_id');
        $writers = User::find($writerIds, ['id','name'])->keyBy('id');
        return [ 'writers' => $writers, 'comments' => $comments ];
    }
};
