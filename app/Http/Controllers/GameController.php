<?php

namespace App\Http\Controllers;

use App\User;
use App\Game;
use App\Logic\GameLogic;
use App\Logic\GameException;
use App\Move;
use Illuminate\Http\Request;
use App\Logic\GameLogic;

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

    private const GameResultNames = [NULL, 'player1', 'player2', 'draw'];

    public function makeMove(Request $request, int $gameId, int $startPos, int $endPos)
    {
        $game = Game::find($gameId);

        $currentTime = now();
        // authorizatrion
        $this->authorize('play', $game);
        $lastBeat = NULL;
        if ($game->last_beat !== NULL)
            $lastBeat = [$game->last_start, $game->last_beat];

        $board = [];
        for ($i = 0; $i < GameLogic::BOARDDIM*GameLogic::BOARDDIM; $i++)
            $board[$i] = $game->board[$i];

        // initialize Game logic
        $gameLogic = GameLogic::fromData($game->board, $game->player1_move,
                $last_beat);
        $doneByPlayer1 = $gameLogic->isPlayer1MakeMove();
        // try to make move
        try
        {
            $gameLogic->makeMove($startPos, $endPos);
        }
        catch (GameException $ex)
        {
            return [ 'error' => $ex->getMessage();
        }
        // save move in database
        $move = new Move([ 'startpos' => $startPos, 'endPos' => $endPos,
                'done_at' => $currentTime, 'done_by_player1' => $doneByPlayer1 ]);
        $lastBeat = $gameLogic->getLastBeat();

        // update board
        $outBoard = '';
        for ($i = 0; $i < GameLogic::BOARDDIM*GameLogic::BOARDDIM; $i++)
            $outBoard .= $gameLogic->getBoard()[$i];

        // update last beat
        if ($lastBeat !== NULL)
        {
            $game->last_start = $lastBeat[0];
            $game->last_beat = $lastBeat[1];
        }
        else
        {
            // if null
            $game->last_start = NULL;
            $game->last_beat = NULL;
        }
        // update current isplayer1move
        $game->player1_move = $gameLogic->isPlayer1MakeMove();

        // check game end
        $gameResult = $gameLogic->checkGameEnd();
        if ($gameResult != GameLogic::NOTEND)
        {
            // update game result
            $game->result = Self::GameResultNames[$gameResult];
            $game->end_at = $currentTime;
        }

        $game->moves()->save($move);

        return [ 'player1Move' => $gameLogic->isPlayer1MakeMove() ];
    }
};
