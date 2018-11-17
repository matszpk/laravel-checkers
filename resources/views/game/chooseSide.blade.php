@extends('layout')

@section('top-pageinfo')
    @lang('main.chooseSideTitle')
@endsection

@section('main')
    <div class='checkers_mainpage'>
        <div class='checkers_mainbutton'>
            <a href="{{ route('game.play', $gameId) }}?player=0">
                    @lang('game.playWhites')</a>
        </div>
        <div class='checkers_mainbutton'>
            <a href="{{ route('game.play', $gameId) }}?player=1">
                    @lang('game.playBlacks')</a>
        </div>
    </div>
@endsection
