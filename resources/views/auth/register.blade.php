@extends('layouts.autenticacion')
@section('title', 'Crear cuenta | Pipocas')
@push('styles')
    @vite('resources/css/register.css')
@endpush
@section('content')
    @include('auth.partials.register-form')
@endsection
@push('scripts')
    @vite('resources/js/register.js')
@endpush