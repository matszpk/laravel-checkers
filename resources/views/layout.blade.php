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
                @auth
                    <div id='checkers_logout'>
                        <a href='logout'>@lang('auth.logout')</a>
                    </div>
                @endauth
            @show
            <div id='checkers_content'>
                @yield('main')
            </div>
            @section('footer')
                <div id='checkers_footer'>
                    <span>
                    @auth
                        @lang('auth.logged', ['user' => $username ])
                        @lang('auth.youAre')
                        @lang('auth.role' . $userrole)
                    @else
                        @lang('auth.notLogged')
                    @endauth
                    </span>
                </div>
            @show
        </div>
    </body>
</html>
