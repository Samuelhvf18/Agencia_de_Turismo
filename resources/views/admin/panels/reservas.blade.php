@extends('layouts.admin')

@section('header-title', 'Gestión de Reservas y Pasajeros')

@section('content')
@php
    $colores = [
        'pendiente' => 'bg-[#F4C95D]/25 text-[#6B4F3A]',
        'confirmada' => 'bg-emerald-100 text-emerald-800',
        'cancelada' => 'bg-red-100 text-red-700',
        'completada' => 'bg-[#1F5F8B]/10 text-[#1F5F8B]',
    ];
@endphp
<div class="space-y-6">
    @if (session('estado'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('estado') }}</div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach (['pendiente' => 'Pendientes de pago', 'confirmada' => 'Confirmadas', 'completada' => 'Completadas', 'cancelada' => 'Canceladas'] as $est => $txt)
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <p class="text-xs text-[#6B6F72]">{{ $txt }}</p>
                <p class="text-2xl font-bold text-[#1F5F8B] mt-1">{{ $resumen[$est]->n ?? 0 }}</p>
                <p class="text-xs text-[#6B6F72]">{{ $resumen[$est]->ptc ?? 0 }} pasajeros</p>
            </div>
        @endforeach
    </div>

    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
        <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Código, contacto, pasajero o documento" class="lg:col-span-2 bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 text-sm">
        <select name="est" class="bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-sm">
            <option value="">Todos los estados</option>
            @foreach (array_keys($colores) as $est)
                <option value="{{ $est }}" @selected(($filtros['est'] ?? '') === $est)>{{ ucfirst($est) }}</option>
            @endforeach
        </select>
        <select name="tur_id" class="bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-sm">
            <option value="">Todos los tours</option>
            @foreach ($tours as $id => $nom)
                <option value="{{ $id }}" @selected((int) ($filtros['tur_id'] ?? 0) === $id)>{{ $nom }}</option>
            @endforeach
        </select>
        <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-sm" aria-label="Salida desde">
        <div class="flex gap-2">
            <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="min-w-0 flex-1 bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-sm" aria-label="Salida hasta">
            <button class="rounded-xl bg-[#1F5F8B] px-4 text-sm font-semibold text-white">Filtrar</button>
        </div>
    </form>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr>
                    <th class="p-4">Reserva / Tour</th>
                    <th class="p-4">Salida</th>
                    <th class="p-4">Contacto</th>
                    <th class="p-4">Pax</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-right">Total</th>
                    <th class="p-4 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($reservas as $r)
                    <tr>
                        <td class="p-4 font-medium text-[#343A40]"><span class="font-mono">{{ $r->cod }}</span><br><span class="text-xs text-[#6B6F72]">{{ $r->salida->tour->nom }}</span></td>
                        <td class="p-4">{{ $r->salida->fec_sal->format('d/m/Y') }} {{ substr($r->salida->hra_sal, 0, 5) }}</td>
                        <td class="p-4">{{ $r->nom_ctc }}<br><span class="text-xs text-[#6B6F72]">{{ $r->cor_ctc }}</span></td>
                        <td class="p-4">{{ $r->cnt_ptc }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $colores[$r->est] ?? '' }}">{{ ucfirst($r->est) }}</span>
                            <span class="block mt-1 text-xs text-[#6B6F72]">Pago: {{ $r->est_pag }}</span>
                        </td>
                        <td class="p-4 text-right font-semibold">Bs. {{ number_format($r->pre_tot, 2, ',', '.') }}</td>
                        <td class="p-4 text-right"><a href="{{ route('admin.reservas.show', $r) }}" class="text-xs font-bold text-[#1F5F8B]">Ver detalle</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-[#6B6F72]">No hay reservas con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $reservas->links() }}
</div>
@endsection
