@extends('layouts.app')

@section('body_class', 'admin')

@section('header')
    @include('partials.header')
@endsection

@section('content')
    <main class="admin-main">
        @yield('page')
    </main>
@endsection

@section('footer')
    @include('partials.footer')
@endsection
