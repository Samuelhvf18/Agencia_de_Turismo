@extends('layouts.turista')
@section('title','Mis reservas')
@section('topbar-title','Mis reservas')
@section('content')
<div class="mb-7">
    <p class="mb-2 text-xs font-bold uppercase tracking-[.18em] text-pipocas-terra">Tu actividad</p>
    <h1 class="page-title text-3xl font-semibold sm:text-4xl">Mis reservas</h1>
    <p class="mt-2 text-sm text-pipocas-muted">Consulta tus solicitudes, fechas y estados.</p>
</div>
<div class="space-y-4">
@forelse($reservas as $reserva)
<article class="tourist-card p-5 sm:p-6">
    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
        <div class="min-w-0"><div class="flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-pipocas-blue/10 px-3 py-1 text-[10px] font-extrabold text-pipocas-blue">{{ $reserva->cod }}</span>
            <span class="rounded-full px-3 py-1 text-[10px] font-extrabold uppercase {{ $reserva->est==='confirmada'?'bg-emerald-50 text-emerald-700':($reserva->est==='cancelada'?'bg-slate-100 text-slate-600':'bg-amber-50 text-amber-700') }}">{{ $reserva->est }}</span>
        </div>
        <h2 class="mt-3 text-lg font-extrabold">{{ $reserva->salidaTour->tour->nom ?? 'Tour no disponible' }}</h2>
        <p class="mt-1 text-xs text-pipocas-muted">{{ $reserva->salidaTour?->fec_sal ? \Carbon\Carbon::parse($reserva->salidaTour->fec_sal)->format('d/m/Y') : 'Fecha no disponible' }} · {{ $reserva->salidaTour?->hra_sal ? substr($reserva->salidaTour->hra_sal,0,5) : '' }}</p>
    </div>
    <div class="grid grid-cols-2 gap-4 sm:min-w-[260px]"><div>
        <p class="text-[10px] text-pipocas-muted">Participantes</p>
        <p class="mt-1 text-sm font-extrabold">{{ $reserva->cnt_ptc }}</p>
    </div>
    <div>
        <p class="text-[10px] text-pipocas-muted">Total estimado</p>
        <p class="mt-1 text-sm font-extrabold text-pipocas-blue">Bs {{ number_format((float)$reserva->pre_tot,2) }}</p>
    </div>
    <div>
        <p class="text-[10px] text-pipocas-muted">Estado de pago</p>
        <p class="mt-1 text-xs font-bold">{{ $reserva->est_pag }}</p>
    </div>
    <div>
        <p class="text-[10px] text-pipocas-muted">Solicitud</p>
        <p class="mt-1 text-xs font-bold">{{ \Carbon\Carbon::parse($reserva->cre_en)->format('d/m/Y') }}</p>
    </div>
</div>
</div>
@if($reserva->participantes->isNotEmpty())<div class="mt-5 border-t border-slate-100 pt-4">
    <p class="mb-2 text-[10px] font-extrabold uppercase tracking-wider text-pipocas-muted">Participantes registrados</p>
    <div class="flex flex-wrap gap-2">@foreach($reserva->participantes as $participante)
        <span class="rounded-lg bg-pipocas-background px-3 py-2 text-[11px] font-semibold">{{ $participante->nom_cpl }}</span>
        @endforeach
    </div>
</div>
@endif
</article>
@empty
<div class="tourist-card px-6 py-14 text-center">
    <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-pipocas-blue/10 text-2xl text-pipocas-blue">
        <i class="fa-solid fa-ticket"></i>
    </div>
    <h2 class="page-title mt-4 text-2xl font-semibold">Todavía no tienes reservas</h2>
    <p class="mx-auto mt-2 max-w-md text-sm text-pipocas-muted">Cuando solicites una experiencia, podrás consultar aquí su estado y los datos de tu reserva.</p>
    <a href="{{ route('turista.tours.index') }}" class="btn-primary mt-5 inline-flex items-center gap-2">
        <i class="fa-solid fa-compass"></i>
        Explorar tours</a>
    </div>
    @endforelse
</div>
<div class="mt-6">{{ $reservas->links() }}</div>
@endsection