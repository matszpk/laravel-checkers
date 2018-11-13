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
        <div id='checkers_board_main'>
        @for ($i = 9; $i >= 0; $i--)
            <div class='checkers_board_row' id="checkers_board_row{{$i}}"
                style="top: {{(9-$i)*40 }}px;">
            @for ($j = 0; $j < 10; $j++)
                <div class="checkers_board_cell
                        checkers_board_cell_{{ ($i&1)^($j&1) ? 'white' : 'black' }}"
                        id="checkers_board_cell{{$i}}{{$j}}"
                    style="left: {{ ($j)*40 }}px;">
                </div>
            @endfor
            </div>
        @endfor
        </div>
        <div id='checkers_yaxis_left' class='checkers_axis'>
            @for ($i = 9; $i >= 0; $i--)
            <div class='checkers_yaxis_ypos'
                style="top: {{ (9-$i)*40 }}px;">{{ $i+1 }}</div>
            @endfor
        </div>
        <div id='checkers_yaxis_right' class='checkers_axis'>
            @for ($i = 9; $i >= 0; $i--)
            <div class='checkers_yaxis_ypos'
                style="top: {{ (9-$i)*40 }}px;">{{ $i+1 }}</div>
            @endfor
        </div>

        <div id='checkers_xaxis_top' class='checkers_axis'>
            @for ($i = 0; $i < 10; $i++)
            <div class='checkers_xaxis_xpos'
                style="left: {{ $i*40 }}px;">{{ chr(65+$i) }}</div>
            @endfor
        </div>
        <div id='checkers_xaxis_bottom' class='checkers_axis'>
            @for ($i = 0; $i < 10; $i++)
            <div class='checkers_xaxis_xpos'
                style="left: {{ $i*40 }}px;">{{ chr(65+$i) }}</div>
            @endfor
        </div>
    </div>
    <div id='checkers_movelist'>
    </div>
    <div id='checkers_gamestatus'>
    </div>

    @include('components.comments')
@endsection
