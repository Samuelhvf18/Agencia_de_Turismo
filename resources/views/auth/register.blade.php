@extends('layouts.autenticacion')
@section('title', 'Crear cuenta | Pipocas')
@push('styles')
    @vite('resources/css/register.css')
@endpush
@section('content')
    @include('auth.partials.register-form')
@endsection
@push('scripts')
    <script
        src="https://www.google.com/recaptcha/api.js"
        async
        defer
    ></script>
    @vite('resources/js/register.js')
@endpush