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

    public function index(string $userId)
    {
        $temp = NULL;   // temp object for querying
        if ($userId === NULL)
            $temp = Game::orderBy('created_at', 'desc');
        else
            $temp = Game::orderBy('created_at', 'desc')->
                where(function ($query) use ($userId) {
                    $query->where('player1_id',$userId)->
                    orWhere('player2_id',$userId);
                });
        return view('game.games', [ 'viewPurpose' => 'toPlay',
            'pag' => $temp->withCount('comments')->paginate(15) ]);
    }

    public function listGamesToContinue()
    {
        $user = Auth::user();
        return view('game.games', [ 'viewPurpose' => 'toContinue',
            'pag' => Game::orderBy('created_at', 'desc')->
            where(function ($query) use ($user) {
                $query->where('player1_id',$user->id)->
                orWhere('player2_id',$user->id);
            })->
            whereNull('result')->withCount('comments')->paginate(15) ]);

    }

    public function listGamesToJoin()
    {
        $user = Auth::user();
        return view('game.games', [ 'viewPurpose' => 'toJoin',
            'pag' => Game::orderBy('created_at', 'desc')->
            where(function ($query) {
                $query->whereIsNull('player1_id')->
                orWhereIsNull('player2_id');
            })->whereNull('result')->withCount('comments')->paginate(15) ]);

    }

    public function listGamesToReplay(string $userId)
    {
        return view('game.toreplay', [ 'viewPurpose' => 'toReplay',
            'pag' => Game::orderBy('created_at', 'desc')->
            where(function ($query) {
                $query->whereIsNotNull('player1_id')->
                andWhereIsNotNull('player2_id');
            })->
            whereNotNull('result')->withCount('comments')->paginate(15) ]);
    }

    public function getGameState(string $gameId)
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

    public function getGameData(string $gameId)
    {
        return Game::with(['moves'  => function($query) {
                $query->orderBy('done_at', 'asc'); },
                'comments' => function($query) {
                $query->orderBy('created_at', 'desc'); },
                'comments.writtenBy' ])->find($gameId);
    }

    // play game
    public function playGame(string $gameId)
    {
        $game = $this->getGameData($gameId);
        $this->authorize('play', $game);
        return view('game.play', [ 'replay' => False,'data' => $game ]);
    }

    // replay game
    public function replayGame(string $gameId)
    {
        $game = $this->getGameData($gameId);
        $this->authorize('replay', $game);
        return view('game.replay', [ 'data' => $game ]);
    }

    public function newGame()
    {
        return view('game.newgame');
    }

    /* create new game and begin it as player1 or player2
     */
    public function createGame(bool $asPlayer1)
    {
        $user = Auth::user();
        $gameLogic = new GameLogic();

        $game = new Game([]);
        $outBoard = '';
        for ($i = 0; $i < GameLogic::BOARDDIM*GameLogic::BOARDDIM; $i++)
            $outBoard .= $gameLogic->getBoard()[$i];
        $currentTime = now();
        $game = new Game([]);
        $game->board = $outBoard;
        if ($asPlayer1)
        {
            $game->player1()->associate($user);
            $game->begin1_at = $currentTime;
            $game->player1_move = True;
        }
        else
        {
            $game->player2()->associate($user);
            $game->begin2_at = $currentTime;
            $game->player1_move = False;
        }
        $game->save();
        return redirect(route('game.play', $game->id));
    }

    public function joinToGame(string $gameId)
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

    public function makeMove(string $gameId, int $startPos, int $endPos)
    {
        $error = NULL;
        $outIsPlayer1Move = False;

        DB::transaction(function() use ($gameId, $startPos, $endPos, &$error,
                &$outIsPlayer1Move) {
            $game = Game::find($gameId);

            $currentTime = now();
            // authorizatrion
            $this->authorize('makeMove', $game);
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
                $error = $ex->getMessage();
                return;
            }

            // try to make move
            try
            {
                $gameLogic->makeMove($startPos, $endPos);
            }
            catch (GameException $ex)
            {
                $error = $ex->getMessage();
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
