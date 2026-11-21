@extends('layouts.reserva')
@section('titulo', __('Mis reservas'))
@section('contenido')
<div class="space-y-8">
    <header class="flex flex-wrap items-center justify-between gap-4">
        <div><p class="text-sm text-andinist-texto-2">{{ __('Hola') }}, {{ \Illuminate\Support\Str::before(auth()->user()->nom, ' ') }}</p><h1 class="mt-1 font-titulo text-3xl font-semibold text-andinist-principal">{{ __('Mis reservas') }}</h1><p class="mt-2 text-sm text-andinist-texto-2">{{ __('Tus viajes, pagos y tickets en un solo lugar.') }}</p></div>
        <div class="flex flex-wrap gap-3"><a href="{{ route('home') }}#catalogo" class="rsv-btn">{{ __('Explorar tours') }} →</a>@if (auth()->user()->tieneRol('turista'))<a href="{{ route('turista.cuenta') }}" class="rsv-btn-sec">{{ __('Ver mis datos') }}</a>@endif</div>
    </header>
    <div class="space-y-4">
        @forelse ($reservas as $reserva)
            <article class="rsv-tarjeta flex flex-wrap items-center justify-between gap-5">
                <div><p class="mb-1 font-mono text-xs text-andinist-texto-2">{{ $reserva->cod }}</p><h2 class="text-lg font-bold text-andinist-principal">{{ $reserva->salida->tour->nom }}</h2><p class="mt-2 text-sm">{{ $reserva->salida->fec_sal->format('d/m/Y') }} · {{ __($reserva->est) }} · {{ __($reserva->est_pag) }}</p></div>
                <a class="rsv-btn-sec" href="{{ route('reservas.estado', $reserva) }}">{{ __('Ver reserva y tickets') }} →</a>
            </article>
        @empty
            <div class="rsv-tarjeta py-10 text-center"><h2 class="text-xl font-bold text-andinist-principal">{{ __('Tu próxima aventura empieza aquí') }}</h2><p class="mx-auto my-3 max-w-md text-sm text-andinist-texto-2">{{ __('Todavía no tienes reservas asociadas a tu cuenta. Explora el catálogo para elegir tu primer tour.') }}</p><a href="{{ route('home') }}#catalogo" class="rsv-btn mt-3">{{ __('Encontrar un tour') }}</a><p class="mt-5 text-sm"><a class="text-andinist-principal underline" href="{{ route('reservas.consultar') }}">{{ __('¿Reservaste como invitado? Consulta con tu código y correo.') }}</a></p></div>
        @endforelse
    </div>
    {{ $reservas->links() }}
    @include('tours.partials.para-ti')
</div>
@endsection
