@extends('layout')

@section('top-pageinfo')
    @if ($replay)
        @lang('main.gameReplayTitle')
    @else
        @lang('main.gamePlayTitle')
    @endif
@endsection

@php($boardDim = \App\Logic\GameLogic::BOARDDIM)

@section('script')
$(function() {
var gameBoard = @json($data->board);
var gamePlayer1Move = @json($data->player1_move!=0);
@if ($data->last_beat !== NULL)
    var gameLastBeat = [ {{ $data->last_start }}, {{ $data->last_beat }} ];
@else
    var gameLastBeat = null;
@endif
    var replay = @json($replay==1);
    @if (!$replay)
        var player1Plays = @json($player1Plays);
    @else
        var player1Plays = null;
    @endif

    var gameMoves = @json($moves);

    GameStateURL = @json(route('game.state', $data->id));
    GameMakeMoveURL = @json(route('game.move', $data->id));
    GameTrans = @json($gameTrans);
    Game.player1 = @json($player1);
    Game.player2 = @json($player2);

@if (!$replay)
    Game.init(gameBoard, gamePlayer1Move, gameLastBeat, player1Plays);
@else
    Game.initReplay(gameBoard, gamePlayer1Move, gameLastBeat);
@endif
    Game.initMoves(gameMoves);
    Game.displayBoard();
    Game.displayMoves();
    Game.handleState();

// do give like code
@can('giveOpinion', $data)
    $('#checkers_like_button').click(function() {
        checkersAxiosPost("{{ route('game.like', $data->id) }}",null,
            function(response) {
                $('#checkers_gamelikes').text(response.data.likes);
            });
    });
@endcan
});
@endsection

@section('main')
    <div id='checkers_game_title'>{{ $data->getName() }}</div>
    @if (!$replay)
    <div id='checkers_game_side'>
        @lang($player1Plays ? 'game.youPlayWhites' : 'game.youPlayBlacks')</div>
    @endif
    <div id='checkers_board'>
        <div id='checkers_board_main'>
        @for ($i = 0; $i < $boardDim; $i++)
            <div class='checkers_board_row' id="checkers_board_row{{$i}}">
            @for ($j = 0; $j < $boardDim; $j++)
                <div class="checkers_board_cell
                        checkers_board_cell{{$j}}_in_row
                        checkers_board_cell_{{ ($i&1)^($j&1) ? 'white' : 'black' }}"
                        id="checkers_board_cell{{$i}}{{$j}}">
                </div>
            @endfor
            </div>
        @endfor
        </div>
        <div id='checkers_yaxis_left' class='checkers_axis'>
            @for ($i = 0; $i < $boardDim; $i++)
            <div class='checkers_yaxis_ypos checkers_yaxis_ypos{{($i)}}'>{{ $i+1 }}</div>
            @endfor
        </div>
        <div id='checkers_yaxis_right' class='checkers_axis'>
            @for ($i = 0; $i < $boardDim; $i++)
            <div class='checkers_yaxis_ypos checkers_yaxis_ypos{{$i}}'>{{ $i+1 }}</div>
            @endfor
        </div>

        <div id='checkers_xaxis_top' class='checkers_axis'>
            @for ($i = 0; $i < $boardDim; $i++)
            <div class='checkers_xaxis_xpos checkers_xaxis_xpos{{$i}}'>{{ chr(65+$i) }}</div>
            @endfor
        </div>
        <div id='checkers_xaxis_bottom' class='checkers_axis'>
            @for ($i = 0; $i < $boardDim; $i++)
            <div class='checkers_xaxis_xpos checkers_xaxis_xpos{{$i}}'>{{ chr(65+$i) }}</div>
            @endfor
        </div>
    </div>
    <div id='checkers_movelist'>
    </div>
    <div id='checkers_gamestatus'>
    </div>
    <div id='checkers_replay_control'>
        @if ($replay)
        <div id='checkers_replay_replay' class='checkers_button'>
            @lang('game.replay')
        </div>
        <div id='checkers_replay_continue' class='checkers_button'>
            @lang('game.continue')
        </div>
        <div id='checkers_replay_stop' class='checkers_button'>
            @lang('game.stop')
        </div>
        @endif
        @can('giveOpinion',$data)
        <td>@lang('user.likes'):</td>
        <span id='checkers_gamelikes'>{{ $data->likes }}</span>
            @can('giveOpinion', $data)
            <div class='checkers_button' id='checkers_like_button'>
                @lang('main.doLike')</div>
            @endcan
        </td>
        <div class='checkers_button'>
            <a href="{{ route('game.comments', $data->id) }}">@lang('main.comments')</a>
        </div>
        @endcan
    </div>
    
@endsection
