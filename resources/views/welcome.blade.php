@extends('layout')

@section('main')
    @auth
        @lang('main.welcomeIfLogged', [ 'user' => $username ])
    @else
        @lang('main.welcomeIfNotLogged')<br/>
        @lang('main.welcomeIfNotRegistered')
    @endauth
@endsection

@section('top-pageinfo')
    @lang('main.welcome')
@endsection
