@extends('layouts.app')

@section('body_class', 'tenant')

@section('header')
    @include('components.navbar')
@endsection

@section('content')
    <main class="tenant-main">
        @yield('page')
    </main>
@endsection

@section('footer')
    @include('partials.footer')
@endsection
