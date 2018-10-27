@extends('layout')

@section('top-pageinfo')
    @lang('main.welcome')
@endsection

@section('main')
    <div class='checkers_mainpage'>
        @auth
            @if ($emailVerified)
                @lang('main.welcomeIfLogged', [ 'user' => $username ])
            @else
                @lang('main.welcomeIfNotVerified')
            @endif
        @else
            @lang('main.welcomeIfNotLogged')<br/>
            @lang('main.welcomeIfNotRegistered')<br/>
        @endauth
    </div>
    @auth
        @if (!$emailVerified)
            <div class='checkers_mainbutton'>
                <a href='email/verify'>@lang('main.verifyEmail')</a>
            </div>
        @else
            <div class='checkers_mainbutton'>
                <a href='begin'>@lang('main.beginGame')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href='continue'>@lang('main.contGame')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href='yourGames'>@lang('main.yourGames')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href='yourComments'>@lang('main.yourComments')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href='yourProfile'>@lang('main.yourProfile')</a>
            </div>
        @endif
    @else
        <div class='checkers_mainbutton'>
            <a href="{{ route('register') }}">@lang('auth.register')</a>
        </div>
        <div class='checkers_mainbutton'>
            <a href="{{ route('login') }}">@lang('auth.login')</a>
        </div>
        <div class='checkers_mainbutton'>
            <a href="{{ route('password.request') }}">@lang('main.resetPassword')</a>
        </div>
    @endauth
@endsection
