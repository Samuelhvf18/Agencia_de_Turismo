@extends('layouts.reserva')
@section('titulo', $nombre)
@section('contenido')
<article class="rsv-tarjeta p-6">
<h1 class="text-3xl font-bold mb-4">{{ $nombre }}</h1>
<p>{{ $bio }}</p><p class="mt-4">{{ __('Años de experiencia') }}: {{ $experiencia }}</p>
<p>{{ __('Idiomas') }}: {{ $idiomas->implode(', ') }}</p>
</article>
@endsection
