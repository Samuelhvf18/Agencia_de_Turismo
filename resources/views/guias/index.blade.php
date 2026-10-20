@extends('layouts.campo')

@section('titulo', 'Mis salidas')
@section('volver', url('/'))

@section('contenido')
    @forelse ($salidas as $s)
        <a href="{{ route('guias.salida', $s) }}" class="block bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-amber-400 transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">
                        {{ $s->fec_sal->isToday() ? 'Hoy' : $s->fec_sal->locale('es')->translatedFormat('D j M') }} · {{ substr($s->hra_sal, 0, 5) }}
                    </span>
                    <h2 class="text-lg font-extrabold text-slate-900">{{ $s->tour->nom }}</h2>
                    <p class="text-sm text-slate-500">{{ (int) $s->pasajeros }} pasajero(s) confirmados · {{ $s->vehiculo?->pla ?? 'sin vehículo' }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $s->est === 'en_curso' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ $s->est === 'en_curso' ? 'En curso' : 'Programada' }}</span>
            </div>
        </a>
    @empty
        <div class="bg-white rounded-2xl p-8 text-center text-slate-500 shadow-sm border border-slate-200">
            <i class="fa-regular fa-calendar text-3xl text-amber-500"></i>
            <p class="mt-2">No tienes salidas asignadas para los próximos días.</p>
        </div>
    @endforelse
@endsection
