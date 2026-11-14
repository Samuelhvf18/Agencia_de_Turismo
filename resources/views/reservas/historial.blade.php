@extends('layouts.reserva')
@section('titulo', __('Mis reservas'))
@section('contenido')
<h1 class="text-3xl font-bold mb-6">{{ __('Mis reservas') }}</h1>
<div class="space-y-4">
@forelse ($reservas as $reserva)
<a class="block rsv-tarjeta p-5" href="{{ route('reservas.estado', $reserva) }}">
<strong>{{ $reserva->cod }} · {{ $reserva->salida->tour->nom }}</strong>
<p>{{ $reserva->salida->fec_sal->format('d/m/Y') }} · {{ __($reserva->est) }} · {{ __($reserva->est_pag) }}</p>
</a>
@empty
<p>{{ __('Todavía no tienes reservas asociadas a tu cuenta.') }}</p>
@endforelse
</div>
<div class="mt-6">{{ $reservas->links() }}</div>
@endsection
