@extends('layouts.autenticacion')

@section('title', 'Crear cuenta | Pipocas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush

@section('content')
    @include('auth.partials.register-form')
@endsection

@push('scripts')
    <script src="{{ asset('js/register.js') }}" defer></script>
@endpush