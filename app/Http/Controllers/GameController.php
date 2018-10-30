<?php

namespace App\Http\Controllers;

use App\User;
use App\Game;
use App\Move;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', 'verified');
    }

    /* create new game and begin it as player1 or player2
     */
    public function createNewGame(Request $request, bool $asPlayer1)
    {
    }

    public function beginGameAsSecond(Request $request, int $gameId)
    {
    }

    public function makeMove(Request $request, int $gameId, int $startPos, int $endPos)
    {
        $game = Game::find($gameId);
        $session = $request->session();
        $gameState = $session->get('game' . $gameId);
        if ($gameState == NULL)
        {
            //
            string $gameState = array_fill(0, 64, ' ');
            // just evaluate moves
            $moves = Move::where('game', $game)->orderBy('created_at');
            foreach ($moves as $move)
                Same::makeMove($move->startpos, $move->endpos);
        }
        Same::makeMove(startPos, endPos);
        // store game state after this move to session
        $session->put('game' . $gameId, $gameState);
    }
};
