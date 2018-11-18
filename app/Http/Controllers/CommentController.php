<?php

namespace App\Http\Controllers;

use App\Comment;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function likeComment(string $commentId)
    {
        $out = NULL;
        DB::transaction(function () use ($commentId, &$out) {
            $data = Comment::findOrFail($commentId);
            $this->authorize('giveOpinion', $data);

            $data->likes += 1;
            $data->save();
            $out = [ 'likes' => $data->likes ];
        });
        return $out;
    }
}
