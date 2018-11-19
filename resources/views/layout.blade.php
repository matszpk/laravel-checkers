<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@lang('layout.webTitle')</title>
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/app.css"/>
        <link rel="shortcut icon" href="{{ url('/') }}/favicon.ico" />
        <script type="text/javascript" src="{{ url('/') }}/js/app.js"></script>
        <script type="text/javascript" src="{{ url('/') }}/messages.js?n=49494034"></script>
        <script type="text/javascript">
            @yield('script')
        </script>
    </head>
    <body>
        <div id='checkers_main'>
            @section('top')
                <div id='checkers_top'>
                    <div id='checkers_title'>
                        <a href="{{ route('home') }}">@lang('layout.topTitle')</a>
                    </div>
                    <div id='checkers_toppageinfo'>
                        @yield('top-pageinfo')
                    </div>
                </div>
                @auth
                    <div id='checkers_logout'>
                        <a href="{{ route('logout') }}">@lang('auth.logout')</a>
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
                        @lang('auth.role' . $userrole).
                        @if (!$emailVerified)
                            @lang('auth.emailNotVerified')
                        @endif
                    @else
                        @lang('auth.notLogged')
                    @endauth
                    </span>
                </div>
            @show
        </div>
        <div id="checkers_message" style="display: none;"></div>
    </body>
</html>
