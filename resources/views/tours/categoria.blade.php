@extends('layouts.reserva')
@section('titulo', $categoria->nom)
@section('contenido')
<h1 class="text-3xl font-bold mb-4">{{ $categoria->nom }}</h1>
<p>{{ $categoria->des }}</p>
<div class="grid sm:grid-cols-2 gap-4 mt-6">
@forelse ($tours as $tour)
<a class="rsv-tarjeta p-5" href="{{ route('tours.show', $tour) }}">{{ $tour->nom }}</a>
@empty <p>{{ __('No hay tours disponibles.') }}</p> @endforelse
</div>
@endsection
