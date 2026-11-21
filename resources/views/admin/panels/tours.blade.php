@extends('layouts.admin')

@section('header-title', 'Catálogo de tours y rutas')

@section('page-description', 'Da forma a tus experiencias: catálogo, categorías, fotografías y rutas.')

@section('content')
@php $campo = 'rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm'; @endphp
<div class="space-y-6">
    @include('admin.partials.mensajes')

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex flex-wrap justify-between items-center gap-3">
        <div>
            <h3 class="text-base font-bold text-[#343A40]">Tours publicados</h3>
            <p class="text-xs text-[#6B6F72]">Cada tour tiene su ruta amigable /tours/… generada automáticamente y única.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="#categorias" class="rounded-xl border border-[#1F5F8B] px-4 py-2 text-xs font-semibold text-[#1F5F8B]">Gestionar categorías</a>
            <a href="{{ route('admin.catalogo.create') }}" class="px-4 py-2 bg-[#1F5F8B] text-white text-xs font-semibold rounded-xl">+ Nuevo tour</a>
        </div>
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

    <section id="categorias" aria-labelledby="titulo-categorias" class="scroll-mt-24 space-y-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <h2 id="titulo-categorias" class="font-bold text-[#343A40]">Categorías de tours</h2>

        <form method="POST" action="{{ route('admin.catalogo.categoria') }}" class="flex flex-wrap items-end gap-3 rounded-xl bg-gray-50 p-3">
            @csrf
            <label class="grid gap-1 text-xs font-semibold">Nombre
                <input name="nom" data-letras maxlength="80" required placeholder="Nueva categoría" class="{{ $campo }}">
            </label>
            <label class="grid gap-1 text-xs font-semibold">Ícono (opcional)
                <input name="ico" maxlength="255" placeholder="fa-solid fa-route" class="{{ $campo }}">
            </label>
            <label class="grid flex-1 gap-1 text-xs font-semibold">Descripción (opcional)
                <input name="des" maxlength="500" class="{{ $campo }}">
            </label>
            <button type="submit" class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-xs font-semibold text-white">Agregar categoría</button>
        </form>

        <ul class="grid gap-3 md:grid-cols-2">
            @forelse ($categorias as $categoria)
                <li class="space-y-2 rounded-xl border border-gray-200 p-3">
                    <h3 class="text-sm font-semibold text-[#343A40]">
                        <i aria-hidden="true" class="{{ $categoria->ico ?: 'fa-solid fa-route' }} text-[#C96F4A]"></i>
                        {{ $categoria->nom }} <span class="text-xs text-[#6B6F72]">· {{ $categoria->tours_count }} {{ $categoria->tours_count === 1 ? 'tour' : 'tours' }}</span>
                    </h3>
                    <details>
                        <summary class="cursor-pointer text-xs font-bold text-[#1F5F8B]" aria-label="Editar categoría {{ $categoria->nom }}">Editar categoría</summary>
                        <form method="POST" action="{{ route('admin.catalogo.categoria', $categoria) }}" class="mt-3 grid gap-2">
                            @csrf
                            <label class="grid gap-1 text-xs font-semibold">Nombre
                                <input name="nom" data-letras maxlength="80" value="{{ $categoria->nom }}" required class="{{ $campo }}">
                            </label>
                            <label class="grid gap-1 text-xs font-semibold">Ícono (opcional)
                                <input name="ico" maxlength="255" value="{{ $categoria->ico }}" placeholder="fa-solid fa-route" class="{{ $campo }}">
                            </label>
                            <label class="grid gap-1 text-xs font-semibold">Descripción (opcional)
                                <input name="des" maxlength="500" value="{{ $categoria->des }}" class="{{ $campo }}">
                            </label>
                            <button type="submit" class="justify-self-start rounded-xl bg-[#1F5F8B] px-3 py-2 text-xs font-semibold text-white">Guardar cambios</button>
                        </form>
                    </details>
                    @if ($categoria->tours_count === 0)
                        <form method="POST" action="{{ route('admin.catalogo.categoria.eliminar', $categoria) }}" onsubmit="return confirm('¿Eliminar esta categoría?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-700" aria-label="Eliminar categoría {{ $categoria->nom }}">Eliminar categoría</button>
                        </form>
                    @endif
                </li>
            @empty
                <li class="text-sm text-[#6B6F72]">Todavía no hay categorías.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
