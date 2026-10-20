@extends('layouts.admin')

@section('header-title', 'Salida · ' . $salida->tour->nom)

@section('content')
@php
    $campo = 'mt-1 w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm';
    $editable = $salida->est === 'programada';
    $activas = $salida->reservas->whereIn('est', ['pendiente', 'confirmada']);
@endphp
<div class="space-y-6">
    <a href="{{ route('admin.operaciones') }}" class="text-sm font-semibold text-[#1F5F8B]">← Volver a operaciones</a>
    @include('admin.partials.mensajes')

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <h3 class="font-bold text-[#343A40]">{{ $salida->fec_sal->locale('es')->translatedFormat('l j \d\e F Y') }} · {{ substr($salida->hra_sal, 0, 5) }}</h3>
                <span class="rounded-full bg-[#1F5F8B]/10 px-3 py-1 text-xs font-bold text-[#1F5F8B]">{{ str_replace('_', ' ', $salida->est) }}</span>
            </div>

            <form method="POST" action="{{ route('admin.salidas.update', $salida) }}" class="grid gap-3 md:grid-cols-2">
                @csrf @method('PUT')
                <label class="text-xs text-[#6B6F72]">Capacidad<input type="number" name="cap_max" min="1" max="60" value="{{ old('cap_max', $salida->cap_max) }}" class="{{ $campo }}" @disabled(! $editable)></label>
                <label class="text-xs text-[#6B6F72]">Guía
                    <select name="prf_gui_id" class="{{ $campo }}" @disabled(! $editable)>
                        <option value="">Sin asignar</option>
                        @foreach ($guias as $g)<option value="{{ $g->id }}" @selected((int) old('prf_gui_id', $salida->prf_gui_id) === $g->id)>{{ $g->usuario->nom }} · {{ $g->idiomas->pluck('cod_iso')->implode('/') }} · {{ $g->ani_exp }} años</option>@endforeach
                    </select>
                </label>
                <label class="text-xs text-[#6B6F72]">Chofer
                    <select name="prf_cho_id" class="{{ $campo }}" @disabled(! $editable)>
                        <option value="">Sin asignar</option>
                        @foreach ($choferes as $c)<option value="{{ $c->id }}" @selected((int) old('prf_cho_id', $salida->prf_cho_id) === $c->id)>{{ $c->usuario->nom }} (lic. {{ $c->fec_ven_lic->format('d/m/Y') }})</option>@endforeach
                    </select>
                </label>
                <label class="text-xs text-[#6B6F72]">Vehículo
                    <select name="veh_id" class="{{ $campo }}" @disabled(! $editable)>
                        <option value="">Sin asignar</option>
                        @foreach ($vehiculos as $v)<option value="{{ $v->id }}" @selected((int) old('veh_id', $salida->veh_id) === $v->id)>{{ $v->pla }} · {{ $v->cap_asi }} asientos</option>@endforeach
                    </select>
                </label>
                @if ($editable)
                    <div class="md:col-span-2"><button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Guardar asignación</button>
                        <span class="ml-2 text-xs text-[#6B6F72]">Se valida choque de horario, horas semanales del guía, licencia vigente, vehículo activo y asientos.</span></div>
                @endif
            </form>
        </div>

        @if ($editable)
            <div class="space-y-4">
                <form method="POST" action="{{ route('admin.salidas.reprogramar', $salida) }}" class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-2">
                    @csrf
                    <h3 class="font-bold text-[#343A40] text-sm">Reprogramar</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" name="fec_sal" min="{{ today()->toDateString() }}" value="{{ $salida->fec_sal->toDateString() }}" class="{{ $campo }}">
                        <input type="time" name="hra_sal" value="{{ substr($salida->hra_sal, 0, 5) }}" class="{{ $campo }}">
                    </div>
                    <input name="motivo" required maxlength="200" placeholder="Motivo (se envía a los clientes)" class="{{ $campo }}">
                    <button class="w-full rounded-xl bg-[#C96F4A] py-2 text-sm font-semibold text-white">Reprogramar y avisar ({{ $activas->count() }})</button>
                </form>
                <form method="POST" action="{{ route('admin.salidas.cancelar', $salida) }}" class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-2" onsubmit="return confirm('¿Cancelar la salida? Se cancelarán {{ $activas->count() }} reserva(s).')">
                    @csrf
                    <h3 class="font-bold text-[#343A40] text-sm">Cancelar salida</h3>
                    <input name="motivo" required maxlength="200" placeholder="Motivo (clima, cupo mínimo…)" class="{{ $campo }}">
                    <button class="w-full rounded-xl bg-red-600 py-2 text-sm font-semibold text-white">Cancelar salida</button>
                </form>
            </div>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <div class="flex items-center justify-between p-4">
            <h3 class="font-bold text-[#343A40]">Pasajeros ({{ $activas->sum('cnt_ptc') }} / {{ $salida->cap_max }})</h3>
            <a href="{{ route('guias.salida', $salida) }}" class="text-xs font-bold text-[#1F5F8B]">Abrir check-in</a>
        </div>
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-gray-50 border-y border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr><th class="p-3">Reserva</th><th class="p-3">Pasajero</th><th class="p-3">Equipo</th><th class="p-3">Boleto</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($activas as $r)
                    @foreach ($r->participantes as $p)
                        <tr>
                            <td class="p-3"><a href="{{ route('admin.reservas.show', $r) }}" class="font-mono text-[#1F5F8B]">{{ $r->cod }}</a><br><span class="text-xs text-[#6B6F72]">{{ $r->est }} · {{ $r->est_pag }}</span></td>
                            <td class="p-3">{{ $p->nom_cpl }}</td>
                            <td class="p-3 text-xs">{{ $p->alquileres->map(fn ($a) => $a->item->tipo->nom.' ('.$a->item->tal.')')->implode(', ') ?: '—' }}</td>
                            <td class="p-3 text-xs font-mono">{{ $p->boleto ? $p->boleto->cod.' · '.$p->boleto->est : '—' }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-[#6B6F72]">Sin reservas activas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
