@extends('layouts.admin')

@use('App\Support\PresentadorAuditoria')

@section('header-title', 'Seguridad y auditoría')
@section('page-description', 'Revisa quién hizo un cambio, cuándo ocurrió y qué datos afectó.')

@section('content')
@php
    $campo = 'block w-full min-w-0 rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-[#343A40]';
    $colores = ['CREATE' => 'bg-emerald-100 text-emerald-800', 'UPDATE' => 'bg-[#1F5F8B]/10 text-[#1F5F8B]', 'DELETE' => 'bg-red-100 text-red-700', 'READ' => 'bg-gray-100 text-gray-700'];
@endphp
<div class="space-y-6">
    @include('admin.auditoria._tabs')
    @include('admin.partials.mensajes')

    <section aria-labelledby="actividad-reciente" class="space-y-3">
        <h2 id="actividad-reciente" class="text-sm font-semibold text-[#343A40]">Actividad de los últimos 7 días</h2>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            @foreach (PresentadorAuditoria::ACCIONES as $accion => $etiqueta)
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-sm text-[#6B6F72]">{{ $etiqueta }}</p>
                    <p class="text-2xl font-bold text-[#1F5F8B]">{{ number_format($resumen[$accion] ?? 0, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <form method="GET" action="{{ route('admin.auditoria') }}" class="space-y-4 rounded-2xl border border-gray-200 bg-white p-5">
        <h2 class="text-sm font-bold text-[#343A40]">Buscar actividad</h2>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <label class="space-y-1 text-sm font-medium">Área o dato
                <select name="tabla" class="{{ $campo }}">
                    <option value="">Todos</option>
                    @foreach ($tablas as $tabla)
                        <option value="{{ $tabla }}" @selected(($filtros['tabla'] ?? '') === $tabla)>{{ PresentadorAuditoria::recurso($tabla) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="space-y-1 text-sm font-medium">Acción
                <select name="acc" class="{{ $campo }}">
                    <option value="">Todas</option>
                    @foreach (PresentadorAuditoria::ACCIONES as $accion => $etiqueta)
                        <option value="{{ $accion }}" @selected(($filtros['acc'] ?? '') === $accion)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </label>
            <label class="space-y-1 text-sm font-medium">Usuario
                <input name="usuario" maxlength="150" value="{{ $filtros['usuario'] ?? '' }}" placeholder="Nombre o correo" class="{{ $campo }}">
            </label>
            <label class="space-y-1 text-sm font-medium">Desde
                <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="{{ $campo }}">
            </label>
            <label class="space-y-1 text-sm font-medium">Hasta
                <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="{{ $campo }}">
            </label>
        </div>
        <details @if (filled($filtros['ip'] ?? null) || filled($filtros['id_reg'] ?? null)) open @endif>
            <summary class="cursor-pointer text-sm font-semibold text-[#1F5F8B]">Filtros adicionales</summary>
            <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <label class="space-y-1 text-sm font-medium">Número de registro
                    <input name="id_reg" type="number" value="{{ $filtros['id_reg'] ?? '' }}" class="{{ $campo }}">
                </label>
                <label class="space-y-1 text-sm font-medium">Dirección IP
                    <input name="ip" maxlength="45" value="{{ $filtros['ip'] ?? '' }}" class="{{ $campo }}">
                </label>
            </div>
        </details>
        <div class="flex flex-wrap items-center gap-4">
            <button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Aplicar filtros</button>
            <a href="{{ route('admin.auditoria') }}" class="text-sm font-semibold text-[#1F5F8B]">Limpiar filtros</a>
        </div>
    </form>

    <section aria-labelledby="registro-actividad" class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
        <div class="flex flex-wrap items-center justify-between gap-2 p-4">
            <h2 id="registro-actividad" class="text-sm font-bold text-[#343A40]">Registro de actividad</h2>
            <p class="text-sm text-[#6B6F72]">{{ number_format($registros->total(), 0, ',', '.') }} resultados</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="border-y border-gray-200 bg-gray-50 text-xs uppercase text-[#6B6F72]">
                    <tr><th class="p-3">Fecha y hora</th><th class="p-3">Quién</th><th class="p-3">Acción</th><th class="p-3">Registro</th><th class="p-3">Qué cambió</th><th class="p-3"><span class="sr-only">Detalle</span></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($registros as $r)
                        @php
                            $campos = $r->acc === 'UPDATE' ? array_keys(PresentadorAuditoria::cambios($r)) : [];
                            if (array_diff($campos, ['mod_en', 'updated_at'])) {
                                $campos = array_values(array_diff($campos, ['mod_en', 'updated_at']));
                            }
                            $etiquetas = array_map(PresentadorAuditoria::campo(...), array_slice($campos, 0, 2));
                        @endphp
                        <tr class="align-top">
                            <td class="whitespace-nowrap p-3">{{ $r->cre_en->format('d/m/Y') }}<br><span class="text-xs text-[#6B6F72]">{{ $r->cre_en->format('H:i:s') }}</span></td>
                            <td class="p-3">{{ $r->usuario?->nom ?? 'Sin usuario identificado' }}<br><span class="break-all text-xs text-[#6B6F72]">{{ $r->usuario?->cor }}</span></td>
                            <td class="p-3"><span class="inline-block rounded-lg px-2 py-1 text-xs font-semibold {{ $colores[$r->acc] ?? '' }}">{{ PresentadorAuditoria::ACCIONES[$r->acc] ?? $r->acc }}</span></td>
                            <td class="p-3 font-medium">{{ PresentadorAuditoria::recurso($r->nom_tbl) }}<br><span class="text-xs font-normal text-[#6B6F72]">#{{ $r->id_reg }}</span></td>
                            <td class="p-3 text-[#6B6F72]">
                                {{ match ($r->acc) {
                                    'CREATE' => 'Se agregó un registro',
                                    'DELETE' => 'Se eliminó un registro',
                                    'READ' => 'Se consultaron datos',
                                    default => $etiquetas ? implode(', ', $etiquetas) : 'Sin valores modificados',
                                } }}
                                @if (count($campos) > 2)<span class="whitespace-nowrap">+{{ count($campos) - 2 }} más</span>@endif
                            </td>
                            <td class="p-3 text-right"><a href="{{ route('admin.auditoria.show', ['registro' => $r] + array_filter($filtros, fn ($valor) => filled($valor)) + ['page' => $registros->currentPage()]) }}" class="whitespace-nowrap font-bold text-[#1F5F8B]">Ver detalle</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-[#6B6F72]">Sin registros para esta búsqueda. Puedes limpiar los filtros para ver toda la actividad.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    {{ $registros->links() }}
</div>
@endsection
