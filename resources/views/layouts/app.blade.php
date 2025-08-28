<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="@yield('body_class')">
        @yield('header')
        @yield('content')
        @yield('footer')

        @include('partials.body')
        @stack('scripts')
    </body>
</html>
