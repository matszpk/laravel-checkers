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
    <div class='checkers_centered'>
    @auth
        @if (!$emailVerified)
            <div class='checkers_mainbutton'>
                <a href='email/verify'>@lang('main.verifyEmail')</a>
            </div>
        @else
            <div class='checkers_mainbutton'>
                <a href="{{ route('game.new') }}">@lang('main.beginGame')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href="{{ route('game.listToContinue') }}">@lang('main.contGame')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href="{{ route('game.listToJoin') }}">@lang('main.joinToGame')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href="{{ route('game.listToReplay') }}">@lang('main.replayGames')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href="{{ route('game.list', $userid) }}">@lang('main.yourGames')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href="{{ route('game.list') }}">@lang('main.allGames')</a>
            </div>
            <div class='checkers_mainbutton'>
                <a href="{{ route('user.wcomments', $userid) }}">
                    @lang('main.yourComments')</a>
            </div>
        @endif
        <br/>
        <div class='checkers_mainbutton'>
            <a href="{{ route('user.list') }}">@lang('main.users')</a>
        </div>
        <div class='checkers_mainbutton'>
            <a href="{{ route('user.user', $userid) }}">@lang('main.yourAccount')</a>
        </div>
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
    <div id='checkers_welcome_footer'>Laravel Checkers by Mateusz Szpakowski<br/>
    @lang('main.contact'): <a href="mailto:matszpk@interia.pl">matszpk@interia.pl</a><br/>
    @lang('main.sourceCodes'):
    <a href="https://github.com/matszpk/laravel-checkers">
            https://github.com/matszpk/laravel-checkers</a></div>
    </div>
@endsection
