@extends('layouts.admin')

@section('header-title', 'Estado de las notificaciones')

@section('page-description', 'Consulta qué avisos se enviaron, cuáles están pendientes y cuáles puedes reintentar.')

@section('content')
@php
    $campo = 'rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm';
    $estados = ['pendiente' => 'Pendiente', 'enviado' => 'Enviada', 'fallido' => 'Falló el envío'];
    $colores = ['pendiente' => 'bg-[#F4C95D]/30 text-[#6B4F3A]', 'enviado' => 'bg-emerald-100 text-emerald-800', 'fallido' => 'bg-red-100 text-red-700'];
@endphp
<div class="space-y-6">
    @include('admin.auditoria._tabs')
    @include('admin.partials.mensajes')

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        @foreach ($colores as $est => $cls)
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <p class="text-xs text-[#6B6F72]">{{ $estados[$est] }}</p>
                <p class="text-2xl font-bold text-[#1F5F8B]">{{ $resumen[$est] ?? 0 }}</p>
            </div>
        @endforeach
    </div>
    <p class="text-sm text-[#6B6F72]">Modo de envío actual · {{ $n8n ? 'Servicio de automatización (n8n)' : 'Correo directo' }}</p>

    <form method="GET" class="flex flex-wrap items-end gap-3">
        <label class="space-y-1 text-sm">Estado
        <select name="est" class="block {{ $campo }}"><option value="">Todo estado</option>@foreach (array_keys($colores) as $e)<option value="{{ $e }}" @selected(($filtros['est'] ?? '') === $e)>{{ $estados[$e] }}</option>@endforeach</select>
        </label>
        <label class="space-y-1 text-sm">Tipo de aviso
        <select name="tip" class="block {{ $campo }}"><option value="">Todo tipo</option>@foreach ($tipos as $t => $txt)<option value="{{ $t }}" @selected(($filtros['tip'] ?? '') === $t)>{{ $txt }}</option>@endforeach</select>
        </label>
        <button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Aplicar filtros</button>
        <a href="{{ route('admin.auditoria.notificaciones') }}" class="self-end py-2 text-sm font-semibold text-[#1F5F8B]">Limpiar filtros</a>
    </form>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase"><tr><th class="p-3">#</th><th class="p-3">Tipo</th><th class="p-3">Destino</th><th class="p-3">Canal</th><th class="p-3">Estado</th><th class="p-3">Enviado</th><th class="p-3"></th></tr></thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                @forelse ($registros as $n)
                    <tr class="align-top">
                        <td class="p-3">{{ $n->id }}</td>
                        <td class="p-3 font-semibold">{{ $tipos[$n->tip] ?? $n->tip }}@if ($n->reserva)<br><a href="{{ route('admin.reservas.show', $n->reserva) }}" class="font-mono text-[#1F5F8B]">{{ $n->reserva->cod }}</a>@endif</td>
                        <td class="p-3">{{ $n->cnd['destino'] ?? $n->usuario?->cor ?? '—' }}<br><span class="text-[#6B6F72]">{{ \Illuminate\Support\Str::limit($n->cnd['mensaje'] ?? '', 90) }}</span>
                            @if (! empty($n->cnd['error']))<br><span class="text-red-600">{{ \Illuminate\Support\Str::limit($n->cnd['error'], 120) }}</span>@endif</td>
                        <td class="p-3">{{ $n->can }}</td>
                        <td class="p-3"><span class="rounded-lg px-2 py-1 font-semibold {{ $colores[$n->est] ?? '' }}">{{ $estados[$n->est] ?? $n->est }}</span></td>
                        <td class="p-3 whitespace-nowrap">{{ $n->env_en?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="p-3 text-right">
                            @if ($n->est === 'fallido')
                                <form method="POST" action="{{ route('admin.auditoria.reintentar', $n) }}">@csrf<button class="font-bold text-[#1F5F8B]">Reintentar envío</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-[#6B6F72]">Sin notificaciones.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $registros->links() }}
</div>
@endsection
