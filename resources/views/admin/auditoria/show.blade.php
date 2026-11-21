@extends('layouts.admin')

@use('App\Support\PresentadorAuditoria')

@section('header-title', PresentadorAuditoria::recurso($r->nom_tbl) . ' · Registro #' . $r->id_reg)
@section('page-description', 'Detalle de la actividad y otros movimientos del mismo registro.')

@section('content')
@php $busqueda = request()->only('tabla', 'acc', 'usuario', 'id_reg', 'ip', 'desde', 'hasta', 'page'); @endphp
<div class="space-y-6">
    <a href="{{ route('admin.auditoria', $busqueda) }}" class="text-sm font-semibold text-[#1F5F8B]">← Volver al registro de actividad</a>

    <section class="space-y-4 rounded-2xl border border-gray-200 bg-white p-5">
        <dl class="grid gap-4 text-sm sm:grid-cols-3">
            <div><dt class="text-xs text-[#6B6F72]">Acción</dt><dd class="font-semibold">{{ PresentadorAuditoria::ACCIONES[$r->acc] ?? $r->acc }}</dd></div>
            <div><dt class="text-xs text-[#6B6F72]">Fecha y hora</dt><dd>{{ $r->cre_en->format('d/m/Y H:i:s') }}</dd></div>
            <div><dt class="text-xs text-[#6B6F72]">Quién</dt><dd>{{ $r->usuario?->nom ?? 'Sin usuario identificado' }}<br><span class="break-all text-xs text-[#6B6F72]">{{ $r->usuario?->cor }}</span></dd></div>
        </dl>
    </section>

    @php
        $mostrarAntes = $r->acc !== 'CREATE';
        $mostrarDespues = $r->acc !== 'DELETE';
        $titulo = match ($r->acc) {
            'CREATE' => 'Datos registrados',
            'DELETE' => 'Datos eliminados',
            'READ' => 'Datos consultados',
            default => 'Datos modificados',
        };
    @endphp
    <section aria-labelledby="datos-auditoria" class="rounded-2xl border border-gray-200 bg-white">
        <h2 id="datos-auditoria" class="p-4 text-sm font-bold text-[#343A40]">{{ $titulo }}</h2>
        @if ($cambios)
            <div class="overflow-x-auto">
                <table class="w-full table-fixed text-left text-sm {{ $mostrarAntes && $mostrarDespues ? 'min-w-[440px]' : 'min-w-[320px]' }}">
                    <thead class="border-y border-gray-200 bg-gray-50 text-xs uppercase text-[#6B6F72]">
                        <tr>
                            <th class="w-1/3 p-3">Dato</th>
                            @if ($mostrarAntes)<th class="p-3">Antes</th>@endif
                            @if ($mostrarDespues)<th class="p-3">{{ $mostrarAntes ? 'Después' : 'Valor registrado' }}</th>@endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($cambios as $campo => $cambio)
                            <tr class="align-top">
                                <th scope="row" class="break-words p-3 font-semibold">{{ PresentadorAuditoria::campo($campo) }}</th>
                                @if ($mostrarAntes)<td class="break-words bg-gray-50 p-3">{{ PresentadorAuditoria::valor($cambio['antes']) }}</td>@endif
                                @if ($mostrarDespues)<td class="break-words p-3">{{ PresentadorAuditoria::valor($cambio['despues']) }}</td>@endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="px-4 pb-4 text-sm text-[#6B6F72]">{{ $r->acc === 'READ' ? 'Se registró una consulta de datos sensibles.' : 'Este evento no tiene cambios de valores registrados.' }}</p>
        @endif
    </section>

    <section class="rounded-2xl border border-gray-200 bg-white p-5">
        <h2 class="mb-3 text-sm font-bold text-[#343A40]">Historial del registro</h2>
        <ul class="divide-y divide-gray-100 text-sm">
            @foreach ($historial as $h)
                <li class="flex flex-wrap justify-between gap-2 py-2">
                    <span>{{ $h->cre_en->format('d/m/Y H:i:s') }} · {{ PresentadorAuditoria::ACCIONES[$h->acc] ?? $h->acc }} · {{ $h->usuario?->nom ?? 'Sin usuario identificado' }}</span>
                    @if ($h->id === $r->id)
                        <span class="text-xs font-semibold text-[#6B6F72]">Evento actual</span>
                    @else
                        <a href="{{ route('admin.auditoria.show', ['registro' => $h] + $busqueda) }}" class="text-xs font-bold text-[#1F5F8B]">Ver detalle</a>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>
</div>
@endsection
