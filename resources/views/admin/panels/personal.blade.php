@extends('layouts.admin')

@section('header-title', 'Personal: guías y choferes')

@section('page-description', 'Administra guías, idiomas, experiencia y licencias para cada salida.')

@section('content')
@php
    $campo = 'w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm';
@endphp
<div class="space-y-6" x-data="{ tab: 'guias', nuevo: null }">
    @include('admin.partials.mensajes')

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-600">Consulta el personal disponible para tus salidas.</p>
        @if (auth()->user()->esAdmin())
        <div class="flex gap-2">
            <button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-xs font-semibold text-white" @click="nuevo = 'guia'">+ Nuevo guía</button>
            <button class="rounded-xl bg-[#C96F4A] px-4 py-2 text-xs font-semibold text-white" @click="nuevo = 'chofer'">+ Nuevo chofer</button>
        </div>
        @endif
    </div>

    @if (auth()->user()->esAdmin())
    {{-- Alta de guía --}}
    <form method="POST" action="{{ route('admin.personal.store', 'guia') }}" x-show="nuevo === 'guia'" x-cloak class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm grid gap-3 md:grid-cols-3">
        @csrf
        <h3 class="md:col-span-3 font-bold text-[#343A40]">Nuevo guía</h3>
        <input name="nom" data-letras maxlength="150" required placeholder="Nombre completo" value="{{ old('nom') }}" class="{{ $campo }}">
        <input name="cor" type="email" required placeholder="Correo (usuario de acceso)" value="{{ old('cor') }}" class="{{ $campo }}">
        <input name="password" type="password" data-clave-nueva minlength="8" maxlength="128" required placeholder="Contraseña inicial" class="{{ $campo }}">
        <x-telefono name="tel" class="md:col-span-3" />
        <label class="text-xs text-[#6B6F72]">Años de experiencia<input name="ani_exp" type="number" min="0" max="60" value="{{ old('ani_exp', 0) }}" class="{{ $campo }}"></label>
        <label class="text-xs text-[#6B6F72]">Límite de horas por semana<input name="lim_hrs_sem" type="number" min="1" max="60" value="{{ old('lim_hrs_sem', 40) }}" class="{{ $campo }}"></label>
        <fieldset class="md:col-span-3">
            <legend class="text-xs text-[#6B6F72] mb-1">Idiomas</legend>
            <div class="flex flex-wrap gap-3 text-sm">
                @foreach ($idiomas as $i)
                    <label class="inline-flex items-center gap-1.5"><input type="checkbox" name="idiomas[]" value="{{ $i->id }}" @checked(in_array($i->id, old('idiomas', []))) > {{ $i->nom }}</label>
                @endforeach
            </div>
        </fieldset>
        <textarea name="bio" rows="2" placeholder="Biografía breve" class="md:col-span-3 {{ $campo }}">{{ old('bio') }}</textarea>
        <div class="md:col-span-3 flex gap-2"><button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Guardar guía</button><button type="button" class="text-sm text-[#6B6F72]" @click="nuevo = null">Cancelar</button></div>
    </form>

    {{-- Alta de chofer --}}
    <form method="POST" action="{{ route('admin.personal.store', 'chofer') }}" x-show="nuevo === 'chofer'" x-cloak class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm grid gap-3 md:grid-cols-3">
        @csrf
        <h3 class="md:col-span-3 font-bold text-[#343A40]">Nuevo chofer</h3>
        <input name="nom" data-letras maxlength="150" required placeholder="Nombre completo" class="{{ $campo }}">
        <input name="cor" type="email" required placeholder="Correo (usuario de acceso)" class="{{ $campo }}">
        <input name="password" type="password" data-clave-nueva minlength="8" maxlength="128" required placeholder="Contraseña inicial" class="{{ $campo }}">
        <x-telefono name="tel" class="md:col-span-3" />
        <input name="num_lic" required placeholder="N.º de licencia" class="{{ $campo }}">
        <select name="tip_lic" class="{{ $campo }}">
            @foreach (['A', 'B', 'C', 'P', 'M', 'T'] as $t)<option value="{{ $t }}">Categoría {{ $t }}</option>@endforeach
        </select>
        <label class="text-xs text-[#6B6F72]">Vencimiento de licencia<input name="fec_ven_lic" type="date" required class="{{ $campo }}"></label>
        <div class="md:col-span-3 flex gap-2"><button class="rounded-xl bg-[#C96F4A] px-4 py-2 text-sm font-semibold text-white">Guardar chofer</button><button type="button" class="text-sm text-[#6B6F72]" @click="nuevo = null">Cancelar</button></div>
    </form>

    @endif
    <livewire:personal-listado />
</div>
@endsection
