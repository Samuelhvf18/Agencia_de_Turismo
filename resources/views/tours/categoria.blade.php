@extends('layouts.reserva')
@section('titulo', $categoria->nom)
@section('contenido')
<h1 class="text-3xl font-bold mb-4">{{ $categoria->nom }}</h1>
<p>{{ $categoria->des }}</p>
<div class="grid sm:grid-cols-2 gap-4 mt-6">
@forelse ($tours as $tour)
<article class="rsv-tarjeta overflow-hidden"><img data-imagen-tour src="{{ $tour->img ?: asset('images/tour-sin-imagen.svg') }}" alt="{{ $tour->img_alt }}" class="mb-4 h-44 w-full rounded-xl object-cover" loading="lazy"><h2 class="font-bold text-andinist-principal">{{ $tour->nom }}</h2><p class="my-3 text-sm text-slate-500">{{ $tour->dur_txt }} h · {{ $tour->dif_txt }} · Bs {{ number_format($tour->pre_bas, 2, ',', '.') }}</p><a href="{{ route('tours.show', $tour) }}" class="rsv-btn-sec">Ver tour →</a></article>
@empty <p>{{ __('No hay tours disponibles.') }}</p> @endforelse
</div>
@endsection
