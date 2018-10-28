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
        return $user->id === $toChange->id;
    }

    public function viewEmail(?User $user, User $toView)
    {
        return $user->id === $toView->id;
    }

    public function viewUpdateAt(?User $user, User $toView)
    {
        return $user->id === $toView->id;
    }
}
