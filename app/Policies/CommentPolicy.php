<?php

namespace App\Policies;

use App\User;
use App\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function giveOpinion(?User $user, Comment $toOpinion)
    {
        return $user->hasVerifiedEmail() && $user->id != $toOpinion->writer_id;
    }
}
