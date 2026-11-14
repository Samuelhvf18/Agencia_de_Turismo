@extends('layouts.autenticacion')
@section('title', 'Iniciar sesión | Pipocas')
@push('styles')
    @vite('resources/css/login.css')
@endpush
@section('content')
    @include('auth.partials.login-form')
@endsection
@push('scripts')
    @vite('resources/js/login.js')
@endpush