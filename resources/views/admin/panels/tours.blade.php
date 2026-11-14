@extends('layouts.admin')

@section('header-title', 'Catálogo de tours y rutas')

@section('page-description', 'Da forma a tus experiencias: catálogo, categorías, fotografías y rutas.')

@section('content')
@php $campo = 'rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm'; @endphp
<div class="space-y-6" x-data="{ cat: null }">
    @include('admin.partials.mensajes')

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex flex-wrap justify-between items-center gap-3">
        <div>
            <h3 class="text-base font-bold text-[#343A40]">Tours publicados</h3>
            <p class="text-xs text-[#6B6F72]">Cada tour tiene su ruta amigable /tours/… generada automáticamente y única.</p>
        </div>
        <a href="{{ route('admin.catalogo.create') }}" class="px-4 py-2 bg-[#1F5F8B] text-white text-xs font-semibold rounded-xl">+ Nuevo tour</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr><th class="p-4">Tour</th><th class="p-4">Categoría</th><th class="p-4">Dificultad</th><th class="p-4">Precio</th><th class="p-4">Fotos / puntos</th><th class="p-4">Salidas</th><th class="p-4">Estado</th><th class="p-4"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($tours as $t)
                    <tr>
                        <td class="p-4"><span class="font-semibold">{{ $t->nom }}</span><br><span class="text-xs font-mono text-[#6B6F72]">{{ $t->cod }} · /tours/{{ $t->rta_amg }}</span></td>
                        <td class="p-4">{{ $t->categoria->nom }}</td>
                        <td class="p-4">{{ ucfirst($t->niv_dif) }}@if ($t->esAltoRiesgo())<span class="ml-1 rounded bg-red-100 px-1.5 text-[10px] font-bold text-red-700">ALTO RIESGO</span>@endif</td>
                        <td class="p-4">Bs. {{ number_format($t->pre_bas, 2, ',', '.') }}</td>
                        <td class="p-4">{{ $t->fotos_count }} / {{ $t->puntos_ruta_count }}</td>
                        <td class="p-4">{{ $t->salidas_count }}</td>
                        <td class="p-4"><span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $t->act ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">{{ $t->act ? 'Publicado' : 'Oculto' }}</span></td>
                        <td class="p-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.catalogo.edit', $t) }}" class="text-xs font-bold text-[#1F5F8B]">Editar</a>
                            @if ($t->act)<a href="{{ route('tours.show', $t) }}" target="_blank" class="ml-2 text-xs font-bold text-[#6B6F72]">Ver</a>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-6 text-center text-[#6B6F72]">No hay tours.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <h3 class="font-bold text-[#343A40] mb-3">Categorías</h3>
        <div class="grid gap-3 md:grid-cols-2">
            @foreach ($categorias as $c)
                <div class="rounded-xl border border-gray-100 p-3">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-semibold text-sm"><i class="{{ $c->ico ?: 'fa-solid fa-route' }} text-[#C96F4A]"></i> {{ $c->nom }} <span class="text-xs text-[#6B6F72]">· {{ $c->tours_count }} tour(s)</span></p>
                        <div class="flex gap-2 text-xs font-bold">
                            <button class="text-[#1F5F8B]" @click="cat = cat === {{ $c->id }} ? null : {{ $c->id }}">Editar</button>
                            @if (! $c->tours_count)
                                <form method="POST" action="{{ route('admin.catalogo.categoria.eliminar', $c) }}">@csrf @method('DELETE')<button class="text-red-600">Eliminar</button></form>
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.catalogo.categoria', $c) }}" x-show="cat === {{ $c->id }}" x-cloak class="mt-3 grid gap-2">
                        @csrf
                        <input name="nom" value="{{ $c->nom }}" required class="{{ $campo }}">
                        <input name="ico" value="{{ $c->ico }}" placeholder="fa-solid fa-person-hiking" class="{{ $campo }}">
                        <input name="des" value="{{ $c->des }}" placeholder="Descripción" class="{{ $campo }}">
                        <button class="rounded-xl bg-[#1F5F8B] px-3 py-2 text-xs font-semibold text-white">Guardar</button>
                    </form>
                </div>
            @endforeach
        </div>
        <form method="POST" action="{{ route('admin.catalogo.categoria') }}" class="mt-4 flex flex-wrap gap-2">
            @csrf
            <input name="nom" required placeholder="Nueva categoría" class="{{ $campo }}">
            <input name="ico" placeholder="fa-solid fa-route" class="{{ $campo }}">
            <input name="des" placeholder="Descripción" class="flex-1 {{ $campo }}">
            <button class="rounded-xl bg-[#C96F4A] px-4 py-2 text-xs font-semibold text-white">Agregar categoría</button>
        </form>
    </div>
</div>
@endsection
