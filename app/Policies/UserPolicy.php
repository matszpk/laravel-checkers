<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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

    public function update(?User $user, User $toChange)
    {
        return $user->id === $toChange->id ||
            $user->role == 'ADMIN';
    }

    public function viewEmail(?User $user, User $toView)
    {
        return $user->id === $toView->id ||
            $user->role == 'ADMIN';
    }

    public function changeEmail(?User $user, User $toChange)
    {
        return ($user->id === $toChange->id && $user->hasVerifiedEmail()) ||
            $user->role == 'ADMIN';
    }

    public function viewUpdateAt(?User $user, User $toView)
    {
        return $user->id === $toView->id ||
            $user->role == 'ADMIN';
    }

    public function giveOpinion(?User $user, User $toOpinion)
    {
        return $user->hasVerifiedEmail() && $user->id != $toOpinion->id;
    }
}
