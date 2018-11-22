<?php

namespace App\Http\Controllers;

use App\Comment;
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
};
