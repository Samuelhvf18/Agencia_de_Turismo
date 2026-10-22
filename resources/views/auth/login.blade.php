@extends('layouts.autenticacion')
@section('title', 'Iniciar sesión | Pipocas')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush
@section('content')
    @include('auth.partials.login-form')
@endsection
@push('scripts')
    <script src="{{ asset('js/login.js') }}" defer></script>
@endpush