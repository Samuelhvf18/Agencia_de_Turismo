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
        <div class="inline-flex rounded-xl bg-white border border-gray-200 p-1 text-sm font-semibold">
            <button class="rounded-lg px-4 py-2" :class="tab === 'guias' ? 'bg-[#1F5F8B] text-white' : 'text-[#343A40]'" @click="tab = 'guias'">Guías ({{ $guias->count() }})</button>
            <button class="rounded-lg px-4 py-2" :class="tab === 'choferes' ? 'bg-[#1F5F8B] text-white' : 'text-[#343A40]'" @click="tab = 'choferes'">Choferes ({{ $choferes->count() }})</button>
        </div>
        <div class="flex gap-2">
            <button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-xs font-semibold text-white" @click="nuevo = 'guia'">+ Nuevo guía</button>
            <button class="rounded-xl bg-[#C96F4A] px-4 py-2 text-xs font-semibold text-white" @click="nuevo = 'chofer'">+ Nuevo chofer</button>
        </div>
    </div>

    {{-- Alta de guía --}}
    <form method="POST" action="{{ route('admin.personal.store', 'guia') }}" x-show="nuevo === 'guia'" x-cloak class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm grid gap-3 md:grid-cols-3">
        @csrf
        <h3 class="md:col-span-3 font-bold text-[#343A40]">Nuevo guía</h3>
        <input name="nom" required placeholder="Nombre completo" value="{{ old('nom') }}" class="{{ $campo }}">
        <input name="cor" type="email" required placeholder="Correo (usuario de acceso)" value="{{ old('cor') }}" class="{{ $campo }}">
        <input name="password" type="password" required placeholder="Contraseña inicial" class="{{ $campo }}">
        <input name="tel" placeholder="Teléfono" value="{{ old('tel') }}" class="{{ $campo }}">
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
        <input name="nom" required placeholder="Nombre completo" class="{{ $campo }}">
        <input name="cor" type="email" required placeholder="Correo (usuario de acceso)" class="{{ $campo }}">
        <input name="password" type="password" required placeholder="Contraseña inicial" class="{{ $campo }}">
        <input name="tel" placeholder="Teléfono" class="{{ $campo }}">
        <input name="num_lic" required placeholder="N.º de licencia" class="{{ $campo }}">
        <select name="tip_lic" class="{{ $campo }}">
            @foreach (['A', 'B', 'C', 'P', 'M', 'T'] as $t)<option value="{{ $t }}">Categoría {{ $t }}</option>@endforeach
        </select>
        <label class="text-xs text-[#6B6F72]">Vencimiento de licencia<input name="fec_ven_lic" type="date" required class="{{ $campo }}"></label>
        <div class="md:col-span-3 flex gap-2"><button class="rounded-xl bg-[#C96F4A] px-4 py-2 text-sm font-semibold text-white">Guardar chofer</button><button type="button" class="text-sm text-[#6B6F72]" @click="nuevo = null">Cancelar</button></div>
    </form>

    <div x-show="tab === 'guias'" class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr><th class="p-4">Guía</th><th class="p-4">Idiomas</th><th class="p-4">Experiencia</th><th class="p-4">Límite semanal</th><th class="p-4">Salidas futuras</th><th class="p-4">Estado</th><th class="p-4 text-right">Acciones</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($guias as $g)
                    <tr>
                        <td class="p-4 font-medium">{{ $g->usuario->nom }}<br><span class="text-xs text-[#6B6F72]">{{ $g->usuario->cor }}</span></td>
                        <td class="p-4">{{ $g->idiomas->pluck('nom')->implode(', ') ?: '—' }}</td>
                        <td class="p-4">{{ $g->ani_exp }} años</td>
                        <td class="p-4">{{ $g->lim_hrs_sem }} h</td>
                        <td class="p-4">{{ $g->futuras }}</td>
                        <td class="p-4"><span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $g->act ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">{{ $g->act ? 'Activo' : 'Inactivo' }}</span></td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.personal.edit', ['guia', $g->id]) }}" class="text-xs font-bold text-[#1F5F8B]">Editar</a>
                            <form method="POST" action="{{ route('admin.personal.estado', ['guia', $g->id]) }}" class="inline">@csrf<button class="text-xs font-bold {{ $g->act ? 'text-red-600' : 'text-emerald-700' }}">{{ $g->act ? 'Desactivar' : 'Activar' }}</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-[#6B6F72]">No hay guías registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="tab === 'choferes'" x-cloak class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr><th class="p-4">Chofer</th><th class="p-4">Licencia</th><th class="p-4">Vencimiento</th><th class="p-4">Salidas futuras</th><th class="p-4">Estado</th><th class="p-4 text-right">Acciones</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($choferes as $c)
                    @php $vence = $c->fec_ven_lic->diffInDays(today(), false); @endphp
                    <tr>
                        <td class="p-4 font-medium">{{ $c->usuario->nom }}<br><span class="text-xs text-[#6B6F72]">{{ $c->usuario->cor }}</span></td>
                        <td class="p-4">{{ $c->num_lic }} (cat. {{ $c->tip_lic }})</td>
                        <td class="p-4 {{ $c->fec_ven_lic->lt(today()->addDays(30)) ? 'font-bold text-[#C96F4A]' : '' }}">{{ $c->fec_ven_lic->format('d/m/Y') }}</td>
                        <td class="p-4">{{ $c->futuras }}</td>
                        <td class="p-4"><span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $c->act ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">{{ $c->act ? 'Activo' : 'Inactivo' }}</span></td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.personal.edit', ['chofer', $c->id]) }}" class="text-xs font-bold text-[#1F5F8B]">Editar</a>
                            <form method="POST" action="{{ route('admin.personal.estado', ['chofer', $c->id]) }}" class="inline">@csrf<button class="text-xs font-bold {{ $c->act ? 'text-red-600' : 'text-emerald-700' }}">{{ $c->act ? 'Desactivar' : 'Activar' }}</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-[#6B6F72]">No hay choferes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
