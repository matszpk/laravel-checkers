<?php

namespace App\Policies;

use App\User;
use App\Game;
use Illuminate\Auth\Access\HandlesAuthorization;

class GamePolicy
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

    public function joinToGame(?User $user, Game $game)
    {
        if (!$user->hasVerifiedEmail())
            return False;
        return ($game->player1_id === NULL ||
                $game->player2_id === NULL);
    }

    public function play(?User $user, Game $game)
    {
        if (!$user->hasVerifiedEmail())
            return False;
        $player1 = $game->player1->getResults();
        if ($player1 !== NULL && $game->player1_move && $user->id === $player1->id)
            return True;
        $player2 = $game->player2->getResults();
        if ($player2 !== NULL && !$game->player1_move &&  $user->id === $player2->id)
            return True;
        return False;
    }

    public function replay(?User $user, Game $game)
    {
        return $user->hasVerifiedEmail();
    }
}
