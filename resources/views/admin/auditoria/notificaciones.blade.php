@extends('layouts.admin')

@section('header-title', 'Notificaciones enviadas')

@section('content')
@php
    $campo = 'rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm';
    $colores = ['pendiente' => 'bg-[#F4C95D]/30 text-[#6B4F3A]', 'enviado' => 'bg-emerald-100 text-emerald-800', 'fallido' => 'bg-red-100 text-red-700'];
@endphp
<div class="space-y-6">
    @include('admin.auditoria._tabs')
    @include('admin.partials.mensajes')

    <div class="grid grid-cols-3 gap-4">
        @foreach ($colores as $est => $cls)
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <p class="text-xs text-[#6B6F72]">{{ ucfirst($est) }}</p>
                <p class="text-2xl font-bold text-[#1F5F8B]">{{ $resumen[$est] ?? 0 }}</p>
            </div>
        @endforeach
    </div>
    <p class="text-xs text-[#6B6F72]">Canal de envío: {{ $n8n ? 'webhook de n8n (N8N_WEBHOOK_NOTIFICACIONES)' : 'correo directo desde Laravel (n8n no configurado)' }}.</p>

    <form method="GET" class="flex flex-wrap gap-3">
        <select name="est" class="{{ $campo }}"><option value="">Todo estado</option>@foreach (array_keys($colores) as $e)<option value="{{ $e }}" @selected(($filtros['est'] ?? '') === $e)>{{ $e }}</option>@endforeach</select>
        <select name="tip" class="{{ $campo }}"><option value="">Todo tipo</option>@foreach ($tipos as $t => $txt)<option value="{{ $t }}" @selected(($filtros['tip'] ?? '') === $t)>{{ $txt }}</option>@endforeach</select>
        <button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Filtrar</button>
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
                        <td class="p-3"><span class="rounded-lg px-2 py-1 font-semibold {{ $colores[$n->est] ?? '' }}">{{ $n->est }}</span></td>
                        <td class="p-3 whitespace-nowrap">{{ $n->env_en?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="p-3 text-right">
                            @if ($n->est === 'fallido')
                                <form method="POST" action="{{ route('admin.auditoria.reintentar', $n) }}">@csrf<button class="font-bold text-[#1F5F8B]">Reintentar</button></form>
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
