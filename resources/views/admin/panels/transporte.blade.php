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

    @if (auth()->user()->esAdmin())
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

    @endif
    <livewire:flota-listado />
</div>
@endsection
