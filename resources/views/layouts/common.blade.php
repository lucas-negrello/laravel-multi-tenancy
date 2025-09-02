@extends('layouts.app')

@section('body_class', 'common')

@section('header')
    @include('partials.header')
@endsection

@section('content')
    <main class="common-main">
        @yield('page')
    </main>
@endsection

@section('footer')
    @include('partials.footer')
@endsection
