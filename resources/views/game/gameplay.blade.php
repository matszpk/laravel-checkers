@extends('layout')

@section('top-pageinfo')
    @if ($replay)
        @lang('main.gameReplayTitle')
    @else
        @lang('main.gamePlayTitle')
    @endif
@endsection

@section('script')
@endsection

@section('main')
    <div id='checkers_board'>
        @for ($i = 0; $i < 10*10; $i++)
            <div class='checkers_board_row' id="checkers_board_row{{$i}}">
            @for ($j = 0; $j < 10*10; $j++)
                <div class='checkers_board_cell' id="checkers_board_cell{{$i}}{{$j}}">
                </div>
            @endfor
            </div>
        @endfor
    </div>
    <div id='checkers_movelist'>
    </div>
    <div id='checkers_gamestatus'>
    </div>

    @include('components.comments')
@endsection
