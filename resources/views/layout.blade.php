<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>@lang('layout.webTitle')</title>
    </head>
    <body>
        @yield('top')
        @yield('main')
        @yield('footer')
    </body>
</html>
