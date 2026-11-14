@extends('layouts.admin')

@section('header-title', 'Flota de transporte')

@section('page-description', 'Mantén tu flota organizada y comprueba qué vehículos están disponibles.')

@section('content')
@php
    $campo = 'w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm';
    $colores = ['activo' => 'bg-emerald-100 text-emerald-800', 'mantenimiento' => 'bg-[#F4C95D]/30 text-[#6B4F3A]', 'inactivo' => 'bg-gray-200 text-gray-600'];
@endphp
<div class="space-y-6" x-data="{ nuevo: false, editar: null }">
    @include('admin.partials.mensajes')

    <div class="grid grid-cols-3 gap-4">
        @foreach ($colores as $est => $cls)
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <p class="text-xs text-[#6B6F72]">{{ ucfirst($est) }}</p>
                <p class="text-2xl font-bold text-[#1F5F8B]">{{ $vehiculos->where('est', $est)->count() }}</p>
            </div>
        @endforeach
    </div>

    <div class="flex justify-end"><button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-xs font-semibold text-white" @click="nuevo = !nuevo">+ Registrar vehículo</button></div>

    <form method="POST" action="{{ route('admin.transporte.store') }}" x-show="nuevo" x-cloak class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm grid gap-3 md:grid-cols-6">
        @csrf
        <input name="pla" required placeholder="Placa (1234-ABC)" class="{{ $campo }}">
        <input name="mar" placeholder="Marca" class="{{ $campo }}">
        <input name="mdl" placeholder="Modelo" class="{{ $campo }}">
        <select name="tip" class="{{ $campo }}"><option value="van">Van</option><option value="minibus">Minibús</option><option value="otro">Otro</option></select>
        <input name="cap_asi" type="number" min="1" max="60" required placeholder="Asientos" class="{{ $campo }}">
        <select name="tip_cmb" class="{{ $campo }}"><option value="diesel">Diésel</option><option value="gasolina">Gasolina</option><option value="gnv">GNV</option><option value="electrico">Eléctrico</option></select>
        <div class="md:col-span-6"><button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Guardar</button></div>
    </form>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr><th class="p-4">Placa</th><th class="p-4">Vehículo</th><th class="p-4">Asientos</th><th class="p-4">Combustible</th><th class="p-4">Recorrido / gasto</th><th class="p-4">Salidas futuras</th><th class="p-4">Estado</th><th class="p-4"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($vehiculos as $v)
                    <tr>
                        <td class="p-4 font-mono font-semibold">{{ $v->pla }}</td>
                        <td class="p-4">{{ $v->mar }} {{ $v->mdl }}<br><span class="text-xs text-[#6B6F72]">{{ ucfirst($v->tip) }}</span></td>
                        <td class="p-4">{{ $v->cap_asi }}</td>
                        <td class="p-4">{{ ucfirst($v->tip_cmb ?? '—') }}</td>
                        <td class="p-4 text-xs">{{ number_format((float) $v->km, 1, ',', '.') }} km<br>Bs. {{ number_format((float) $v->combustible, 2, ',', '.') }}</td>
                        <td class="p-4">{{ $v->futuras }}</td>
                        <td class="p-4"><span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $colores[$v->est] ?? '' }}">{{ ucfirst($v->est) }}</span></td>
                        <td class="p-4 text-right"><button class="text-xs font-bold text-[#1F5F8B]" @click="editar = editar === {{ $v->id }} ? null : {{ $v->id }}">Editar</button></td>
                    </tr>
                    <tr x-show="editar === {{ $v->id }}" x-cloak class="bg-gray-50">
                        <td colspan="8" class="p-4">
                            <form method="POST" action="{{ route('admin.transporte.update', $v) }}" class="grid gap-2 md:grid-cols-7">
                                @csrf @method('PUT')
                                <input name="pla" value="{{ $v->pla }}" class="{{ $campo }}">
                                <input name="mar" value="{{ $v->mar }}" class="{{ $campo }}">
                                <input name="mdl" value="{{ $v->mdl }}" class="{{ $campo }}">
                                <select name="tip" class="{{ $campo }}">@foreach (['van', 'minibus', 'otro'] as $t)<option value="{{ $t }}" @selected($v->tip === $t)>{{ $t }}</option>@endforeach</select>
                                <input name="cap_asi" type="number" min="1" max="60" value="{{ $v->cap_asi }}" class="{{ $campo }}">
                                <select name="tip_cmb" class="{{ $campo }}">@foreach (['diesel', 'gasolina', 'gnv', 'electrico'] as $t)<option value="{{ $t }}" @selected($v->tip_cmb === $t)>{{ $t }}</option>@endforeach</select>
                                <select name="est" class="{{ $campo }}">@foreach (array_keys($colores) as $e)<option value="{{ $e }}" @selected($v->est === $e)>{{ $e }}</option>@endforeach</select>
                                <div class="md:col-span-7"><button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-xs font-semibold text-white">Guardar</button></div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-6 text-center text-[#6B6F72]">No hay vehículos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
