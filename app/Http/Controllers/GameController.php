<?php

namespace App\Http\Controllers;

use App\User;
use App\Game;
use App\Logic\GameLogic;
use App\Logic\GameException;
use App\Move;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(int $userId)
    {
        $user = User::find($userId);
        return view('game.games', [ 'pag' => Game::orderBy('created_at', 'desc')->
            where(function ($query) use ($userId) {
                $query->where('player1_id',$userId)->
                orWhere('player2_id',$userId);
            })->
            withCount('comments')->paginate(15) ]);
    }

    public function listGamesToContinue()
    {
        $user = Auth::user();
        return view('game.tocont', [ 'pag' => Game::orderBy('created_at', 'desc')->
            where(function ($query) use ($user) {
                $query->where('player1_id',$user->id)->
                orWhere('player2_id',$user->id);
            })->
            whereNull('result')->withCount('comments')->paginate(15) ]);
    }

    public function getGameState(int $gameId)
    {
        $game = Game::find($gameId);
        // authorizatrion
        $this->authorize('play', $game);

        $board = [];
        for ($i = 0; $i < GameLogic::BOARDDIM*GameLogic::BOARDDIM; $i++)
            $board[$i] = $game->board[$i];
        return [ 'board' => $board, 'player1Move' => $game->player1_move,
                    'lastBeat' =>[ $game->last_start, $game->last_beat ] ];
    }

    /* create new game and begin it as player1 or player2
     */
    public function createNewGame(bool $asPlayer1)
    {
        $user = Auth::user();
        $gameLogic = new GameLogic();
        // authorizatrion
        $this->authorize('joinToGame', $game);

        $outBoard = '';
        for ($i = 0; $i < GameLogic::BOARDDIM*GameLogic::BOARDDIM; $i++)
            $outBoard .= $gameLogic->getBoard()[$i];
        $game->board = $outBoard;
        $currentTime = now();
        $game = new Game([]);
        if ($asPlayer1)
        {
            $game->player1()->associate($user);
            $game->begin1_at = $currentTime;
        }
        else
        {
            $game->player2()->associate($user);
            $game->begin2_at = $currentTime;
        }
        $game->save();
        return route('gameplay', [ 'id' => $game->id ]);
    }

    public function beginGameAsSecond(int $gameId)
    {
        $user = Auth::user();
        DB::transaction(function() use($gameId) {
            $game = Game::find($gameId);
            // authorizatrion
            $this->authorize('joinToGame', $game);
            $currentTime = now();
            if ($game->player1_id == NULL)
            {
                $game->player1()->associate($user);
                $game->begin1_at = $currentTime;
            }
            else if ($game->player2_id == NULL)
            {
                $game->player2()->associate($user);
                $game->begin2_at = $currentTime;
            }
            else
                throw new Exception('ERRROR');
            $game->save();
        });
        return route('gameplay', [ 'id' => $gameId ]);
    }

    private const GameResultNames = [NULL, 'player1', 'player2', 'draw'];

    public function makeMove(int $gameId, int $startPos, int $endPos)
    {
        $error = NULL;
        $outIsPlayer1Move = False;

        DB::transaction(function() use ($gameId, $startPos, $endPos, &$error,
                &$outIsPlayer1Move) {
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

            if ($gameLogic->checkGameEnd() != GameLogic::NOTEND)
            {
                // if end of game
                $error = 'error' => $ex->getMessage();
                return;
            }

            // try to make move
            try
            {
                $gameLogic->makeMove($startPos, $endPos);
            }
            catch (GameException $ex)
            {
                $error = 'error' => $ex->getMessage();
                return;
            }
            // save move in database
            $move = new Move([ 'startpos' => $startPos, 'endPos' => $endPos,
                    'done_at' => $currentTime, 'done_by_player1' => $doneByPlayer1 ]);
            $lastBeat = $gameLogic->getLastBeat();

            // update board
            $outBoard = '';
            for ($i = 0; $i < GameLogic::BOARDDIM*GameLogic::BOARDDIM; $i++)
                $outBoard .= $gameLogic->getBoard()[$i];
            $game->board = $outBoard;

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
            $game->save();
        });

        if ($error !== NULL)
            return [ 'error' => $error ];
        return [ 'player1Move' => $outIsPlayer1Move ];
    }
}
