<?php

namespace App\Http\Controllers;

use App\Comment;

class CommentController extends Controller
{
    use LikelableController;
    
    public const MainModel = Comment::class;
    
    //
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
}
