<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@lang('layout.webTitle')</title>
        <link rel="stylesheet" type="text/css" ref="css/app.css"/>
        <script type="text/javascript" src="js/app.js"></script>
    </head>
    <body>
        @section('top')
            <div class='checkers_top'>
                <div class='checkers_title'>@lang('layout.topTitle')</div>
                @yield('top-pageinfo')
            </div>
        @show
        @yield('main')
        @section('footer')
            <div class='checkers_footer'>
                <span>
                @if ($username != NULL)
                    {{ __('auth.logged', ['user' => $username ]) }}
                @else
                    {{ __('auth.notLogged') }}
                @endif
                </span>
            </div>
        @show
    </body>
</html>
