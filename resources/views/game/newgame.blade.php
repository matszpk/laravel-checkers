@extends('layout')

@section('top-pageinfo')
    @lang('main.newGame')
@endsection

@section('main')
    <div class='checkers_mainpage'>
        <div class='checkers_mainbutton'>
            <a href="{{ route('newgame', ['$asPlayer1' => 'True']) }}">
                    @lang('game.playWithWhites')</a>
        </div>
        <div class='checkers_mainbutton'>
            <a href="{{ route('newgame', ['$asPlayer1' => 'False']) }}">
                    @lang('game.playWithBlacks')</a>
        </div>
    </div>
@endsection
