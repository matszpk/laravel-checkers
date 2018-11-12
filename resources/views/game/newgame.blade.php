@extends('layout')

@section('top-pageinfo')
    @lang('main.newGameTitle')
@endsection

@section('main')
    <div class='checkers_mainpage'>
        <div class='checkers_mainbutton'>
            <a href="{{ route('game.create', 1) }}">
                    @lang('game.playWhites')</a>
        </div>
        <div class='checkers_mainbutton'>
            <a href="{{ route('game.create', 0) }}">
                    @lang('game.playBlacks')</a>
        </div>
    </div>
@endsection
