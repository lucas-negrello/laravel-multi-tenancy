@extends('layouts.admin')

@section('title', 'User Management')

@section('header')
    @include('components.navbar')
@endsection

@section('page')
    <h1 class="mb-4">Users Management Page</h1>

    <table
        id="users-table"
        class="min-w-full border-collapse display w-full"
        data-url="{{ route('users.data') }}">
        <thead>
        <tr>
            <th class="text-left p-2 border-b">ID</th>
            <th class="text-left p-2 border-b">Name</th>
            <th class="text-left p-2 border-b">Email</th>
            <th class="text-left p-2 border-b">Created at</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

@push('scripts')
    @vite('resources/ts/pages/users.ts')
@endpush

@section('footer')
    @include('components.footer')
@endsection
