<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@lang('layout.webTitle')</title>
        <link rel="stylesheet" type="text/css" href="css/app.css"/>
        <script type="text/javascript" src="js/app.js"></script>
    </head>
    <body>
        <div id='checkers_main'>
            @section('top')
                <div id='checkers_top'>
                    <div id='checkers_title'>@lang('layout.topTitle')</div>
                    <div id='checkers_toppageinfo'>
                        @yield('top-pageinfo')
                    </div>
                </div>
            @show
            @yield('main')
            @section('footer')
                <div id='checkers_footer'>
                    <span>
                    @if ($username != NULL)
                        {{ __('auth.logged', ['user' => $username ]) }}
                    @else
                        {{ __('auth.notLogged') }}
                    @endif
                    </span>
                </div>
            @show
        </div>
    </body>
</html>
