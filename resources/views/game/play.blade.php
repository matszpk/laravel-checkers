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
var gameBoard = [
    @for ($i = 0; $i < $boardDim*$boardDim-1; $i++)
        '{{ $data->board[$i] }}',
    @endfor
    '{{ $data->board[$boardDim*$boardDim-1] }}'
];
var gamePlayer1Move = {{ $data->player1_move ? 'true' : 'false' }};
@if ($data->last_beat !== NULL)
    var gameLastBeat = [ {{ $data->last_start }}, {{ $data->last_beat }} ];
@else
    var gameLastBeat = null;
@endif
});
@endsection


@section('main')
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
    bla bla bla<br/>blebleble
    </div>
    <div id='checkers_gamestatus'>
    bla bla bla blebleble
    </div>

    @include('components.comments')
@endsection
