<?php

namespace App\Http\Controllers;

use \Exception;
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

    public function index(string $userId = NULL)
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

    /// list games to continue (just play)
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

    // list game to join (games which you can join)
    public function listGamesToJoin()
    {
        return view('game.games', [ 'viewPurpose' => 'toJoin',
            'pag' => Game::orderBy('created_at', 'desc')->
            where(function ($query) {
                $query->whereNull('player1_id')->
                orWhereNull('player2_id');
            })->whereNull('result')->withCount('comments')->paginate(15) ]);

    }

    public function listGamesToReplay()
    {
        return view('game.games', [ 'viewPurpose' => 'toReplay',
            'pag' => Game::orderBy('created_at', 'desc')->
            where(function ($query) {
                $query->whereNotNull('player1_id')->
                whereNotNull('player2_id');
            })->
            whereNotNull('result')->withCount('comments')->paginate(15) ]);
    }
    
    // get game moves as list accepted in response output
    // in format: [ (start0, end0, player1_0), ... ]
    private static function getMovesAsOutList($moves)
    {
        return $moves->map(function($m) {
            return [ $m->startpos, $m->endpos, $m->done_by_player1 ]; });
    }

    public function getGameState(string $gameId)
    {
        $game = Game::with([ 'moves' => function($query) {
            $query->orderBy('done_at', 'asc'); }, 'player1', 'player2' ])
            ->findOrFail($gameId);
        // authorizatrion
        $this->authorize('play', $game);

        return [ 'moves' => Self::getMovesAsOutList($game->moves),
                 'gameName' => $game->getName() ];
    }

    private function getGameData(string $gameId)
    {
        $data = Game::with(['moves'  => function($query) {
                $query->orderBy('done_at', 'asc'); },
                'comments' => function($query) {
                $query->orderBy('created_at', 'desc'); } ])->findOrFail($gameId);
        // get writers for comments
        $writerIds = $data->comments->pluck('writer_id');
        $writers = User::find($writerIds, ['id','name'])->keyBy('id');
        return [ 'data' => $data, 'writers' => $writers,
                'moves' => Self::getMovesAsOutList($data->moves) ];
    }
    
    // choose game side (white or black)
    public function chooseSide(string $gameId)
    {
        $data = Game::findOrFail($gameId);
        $this->authorize('play', $data);
        return view('game.chooseSide', [ 'gameId' => $gameId,
            'gameName' => $data->getName() ]);
    }
    
    private static function getGameTrans()
    {
        return [
            'youDoMove' => __('game.youDoMove'),
            'oponentDoMove' => __('game.oponentDoMove'),
            'youOponentWillBe' => __('game.youOponentWillBe'),
            'result_draw' => __('game.result_draw'),
            'result_player1' => __('game.result_player1'),
            'result_player2' => __('game.result_player2') ];
    }
    
    // play game
    public function playGame(Request $request, string $gameId)
    {
        $data = $this->getGameData($gameId);
        $this->authorize('play', $data['data']);
        $user = Auth::user();
        $game = $data['data'];
        if ($game->player1_id == $user->id && $game->player2_id == $user->id &&
                !$request->exists('player')) 
            return redirect()->route('game.chooseSide', $gameId);
        // validate input
        $this->validate($request, ['player' => 'nullable|integer|min:0|max:1' ]);
        
        $player1Plays = true;
        if ($game->player1_id == $user->id && $game->player2_id == $user->id)
            // same two players, get from input 'player'
            $player1Plays = $request->input('player')==0;
        else
            $player1Plays = ($game->player1_id == $user->id);
        
        return view('game.play', array_merge($data, [ 'replay' => False,
                'player1Plays' => $player1Plays,
                'gameTrans' => Self::getGameTrans() ]));
    }

    // replay game
    public function replayGame(string $gameId)
    {
        $data = $this->getGameData($gameId);
        $this->authorize('replay', $data['data']);
        return view('game.play', array_merge($data, [ 'replay' => True,
                'gameTrans' => Self::getGameTrans()
        ]));
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
        $currentTime = now();
        $game = new Game([]);
        $game->board = implode('', $gameLogic->getBoard());
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
        $game->player1_move = True;
        $game->save();
        return redirect()->route('game.play', $game->id);
    }

    public function joinToGame(string $gameId)
    {
        $user = Auth::user();
        DB::transaction(function() use($gameId, $user) {
            $game = Game::findOrFail($gameId);
            // authorizatrion
            $this->authorize('join', $game);
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
            else if ($game->player1_id != $user->id && $game->player2_id != $user->id)
                throw new Exception('ERRROR');
            $game->save();
        });
        return redirect()->route('game.play', $gameId);
    }

    private const GameResultNames = [NULL, 'winner1', 'winner2', 'draw'];

    public function makeMove(Request $request, string $gameId)
    {
        $error = NULL;
        $outIsPlayer1Move = False;
        $this->validate($request, [
            'startPos' => 'required|integer|min:0|max:' .
                    (GameLogic::BOARDDIM*GameLogic::BOARDDIM-1),
            'endPos' => 'required|integer|min:0|max:' .
                    (GameLogic::BOARDDIM*GameLogic::BOARDDIM-1),
            'countMoves' => 'required|integer|min:0' ]);
        $startPos = $request->input('startPos');
        $endPos = $request->input('endPos');
        $countMoves = $request->input('countMoves');

        DB::transaction(function() use ($gameId, $startPos, $endPos, &$error,
                &$outIsPlayer1Move, $countMoves) {
            $game = Game::withCount('moves')->findOrFail($gameId);
            if ($countMoves != $game->moves_count)
            {
                $error = trans('game.notInSamePoint');
                return;
            }
            
            $currentTime = now();
            // authorizatrion
            $this->authorize('makeMove', $game);
            $lastBeat = NULL;
            if ($game->last_beat !== NULL)
                $lastBeat = [$game->last_start, $game->last_beat];
            
            // initialize Game logic
            $gameLogic = GameLogic::fromData(str_split($game->board),
                    $game->player1_move, $lastBeat);
            $doneByPlayer1 = $gameLogic->isPlayer1MakeMove();
            
            if ($gameLogic->checkGameEnd() != GameLogic::NOTEND)
            {
                // if end of game
                $error = 'Game is not finished';
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
            $move = new Move([ 'startpos' => $startPos, 'endpos' => $endPos,
                    'done_at' => $currentTime, 'done_by_player1' => $doneByPlayer1 ]);
            $lastBeat = $gameLogic->getLastBeat();
            
            // update board
            $game->board = implode('', $gameLogic->getBoard());
            
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
            
            $outIsPlayer1Move = $gameLogic->isPlayer1MakeMove();
        });

        if ($error !== NULL)
            return response()->json([ 'error' => $error ], 400);
        return [ 'player1Move' => $outIsPlayer1Move ];
    }
}
