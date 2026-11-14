@extends('layouts.admin')

@section('header-title', 'Operaciones y salidas')

@section('page-description', 'Programa salidas, coordina responsables y revisa el estado de cada experiencia.')

@section('content')
@php
    $campo = 'rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm';
    $colores = ['programada' => 'bg-[#1F5F8B]/10 text-[#1F5F8B]', 'en_curso' => 'bg-[#F4C95D]/30 text-[#6B4F3A]', 'completada' => 'bg-emerald-100 text-emerald-800', 'cancelada' => 'bg-red-100 text-red-700'];
@endphp
<div class="space-y-6" x-data="{ nueva: {{ $errors->any() ? 'true' : 'false' }} }">
    @include('admin.partials.mensajes')

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between md:items-end gap-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <label class="text-xs text-[#6B6F72]">Desde<input type="date" name="desde" value="{{ $filtros['desde'] }}" class="mt-1 block {{ $campo }}"></label>
            <label class="text-xs text-[#6B6F72]">Hasta<input type="date" name="hasta" value="{{ $filtros['hasta'] }}" class="mt-1 block {{ $campo }}"></label>
            <label class="text-xs text-[#6B6F72]">Tour
                <select name="tur_id" class="mt-1 block {{ $campo }}">
                    <option value="">Todos</option>
                    @foreach ($tours as $t)<option value="{{ $t->id }}" @selected((int) ($filtros['tur_id'] ?? 0) === $t->id)>{{ $t->nom }}</option>@endforeach
                </select>
            </label>
            <label class="text-xs text-[#6B6F72]">Estado
                <select name="est" class="mt-1 block {{ $campo }}">
                    <option value="">Todos</option>
                    @foreach (array_keys($colores) as $e)<option value="{{ $e }}" @selected(($filtros['est'] ?? '') === $e)>{{ str_replace('_', ' ', $e) }}</option>@endforeach
                </select>
            </label>
            <button class="rounded-xl bg-[#343A40] px-4 py-2 text-sm font-semibold text-white">Filtrar</button>
        </form>
        <button @click="nueva = !nueva" class="px-4 py-2.5 bg-[#1F5F8B] hover:bg-[#174870] text-white text-xs font-semibold rounded-xl transition shadow">+ Programar salida</button>
    </div>

    <form method="POST" action="{{ route('admin.salidas.store') }}" x-show="nueva" x-cloak class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm grid gap-3 md:grid-cols-4">
        @csrf
        <h3 class="md:col-span-4 font-bold text-[#343A40]">Nueva salida</h3>
        <label class="text-xs text-[#6B6F72] md:col-span-2">Tour
            <select name="tur_id" required class="mt-1 w-full {{ $campo }}">
                @foreach ($tours as $t)<option value="{{ $t->id }}" @selected((int) old('tur_id') === $t->id)>{{ $t->nom }} ({{ (float) $t->dur_hrs }} h, cap. {{ $t->cap_max }})</option>@endforeach
            </select>
        </label>
        <label class="text-xs text-[#6B6F72]">Fecha<input type="date" name="fec_sal" required min="{{ today()->toDateString() }}" value="{{ old('fec_sal') }}" class="mt-1 w-full {{ $campo }}"></label>
        <label class="text-xs text-[#6B6F72]">Hora<input type="time" name="hra_sal" required value="{{ old('hra_sal', '08:00') }}" class="mt-1 w-full {{ $campo }}"></label>
        <label class="text-xs text-[#6B6F72]">Capacidad<input type="number" name="cap_max" min="1" max="60" required value="{{ old('cap_max', 12) }}" class="mt-1 w-full {{ $campo }}"></label>
        <label class="text-xs text-[#6B6F72]">Guía
            <select name="prf_gui_id" class="mt-1 w-full {{ $campo }}">
                <option value="">Sin asignar</option>
                @foreach ($guias as $g)<option value="{{ $g->id }}" @selected((int) old('prf_gui_id') === $g->id)>{{ $g->usuario->nom }} · {{ $g->idiomas->pluck('cod_iso')->implode('/') }}</option>@endforeach
            </select>
        </label>
        <label class="text-xs text-[#6B6F72]">Chofer
            <select name="prf_cho_id" class="mt-1 w-full {{ $campo }}">
                <option value="">Sin asignar</option>
                @foreach ($choferes as $c)<option value="{{ $c->id }}" @selected((int) old('prf_cho_id') === $c->id)>{{ $c->usuario->nom }}</option>@endforeach
            </select>
        </label>
        <label class="text-xs text-[#6B6F72]">Vehículo
            <select name="veh_id" class="mt-1 w-full {{ $campo }}">
                <option value="">Sin asignar</option>
                @foreach ($vehiculos as $v)<option value="{{ $v->id }}" @selected((int) old('veh_id') === $v->id)>{{ $v->pla }} ({{ $v->cap_asi }} asientos)</option>@endforeach
            </select>
        </label>
        <div class="md:col-span-4"><button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Programar</button></div>
    </form>

    @forelse ($salidas as $fecha => $lista)
        <section>
            <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-[#6B6F72]">{{ \Carbon\Carbon::parse($fecha)->locale('es')->translatedFormat('l j \d\e F') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($lista as $s)
                    @php $ocupados = (int) $s->ocupados; @endphp
                    <div class="bg-white border-l-4 {{ $s->est === 'cancelada' ? 'border-l-red-400' : 'border-l-[#1F5F8B]' }} border border-gray-200 rounded-2xl p-5 shadow-sm space-y-3">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <span class="px-2.5 py-1 bg-[#1F5F8B]/10 text-[#1F5F8B] text-xs font-bold rounded-lg">{{ substr($s->hra_sal, 0, 5) }}</span>
                                <h4 class="font-bold text-[#343A40] mt-2">{{ $s->tour->nom }}</h4>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $colores[$s->est] ?? '' }}">{{ str_replace('_', ' ', $s->est) }}</span>
                        </div>
                        <div class="text-xs space-y-1 text-[#6B6F72]">
                            <p>Guía: <span class="{{ $s->guia ? 'text-[#343A40]' : 'text-[#C96F4A] font-semibold' }}">{{ $s->guia?->usuario?->nom ?? 'Sin asignar' }}</span></p>
                            <p>Chofer: <span class="{{ $s->chofer ? 'text-[#343A40]' : 'text-[#C96F4A] font-semibold' }}">{{ $s->chofer?->usuario?->nom ?? 'Sin asignar' }}</span></p>
                            <p>Vehículo: {{ $s->vehiculo?->pla ?? 'Sin asignar' }}</p>
                            <p>Pasajeros: <b class="text-[#343A40]">{{ $ocupados }} / {{ $s->cap_max }}</b></p>
                        </div>
                        <div class="h-1.5 rounded-full bg-gray-100"><div class="h-1.5 rounded-full bg-[#1F5F8B]" style="width: {{ $s->cap_max ? min(100, round($ocupados * 100 / $s->cap_max)) : 0 }}%"></div></div>
                        <a href="{{ route('admin.salidas.edit', $s) }}" class="block w-full py-2 text-center bg-[#F0F0F1] hover:bg-gray-200 text-[#343A40] text-xs font-medium rounded-xl transition">Asignar, pasajeros y acciones</a>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center text-sm text-[#6B6F72]">No hay salidas en el rango elegido.</div>
    @endforelse
</div>
@endsection
