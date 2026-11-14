@extends('layouts.admin')

@section('header-title', 'Usuarios, roles y auditoría de seguridad')

@section('page-description', 'Consulta cambios y accesos para mantener el control de tu agencia.')

@section('content')
@php
    $campo = 'rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm';
    $colores = ['CREATE' => 'bg-emerald-100 text-emerald-800', 'UPDATE' => 'bg-[#1F5F8B]/10 text-[#1F5F8B]', 'DELETE' => 'bg-red-100 text-red-700', 'READ' => 'bg-gray-100 text-gray-700'];
@endphp
<div class="space-y-6">
    @include('admin.auditoria._tabs')
    @include('admin.partials.mensajes')

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($colores as $acc => $cls)
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <p class="text-xs text-[#6B6F72]">{{ $acc }} · últimos 7 días</p>
                <p class="text-2xl font-bold text-[#1F5F8B]">{{ $resumen[$acc] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 grid gap-3 sm:grid-cols-3 lg:grid-cols-7">
        <select name="tabla" class="{{ $campo }}"><option value="">Todas las tablas</option>@foreach ($tablas as $t)<option value="{{ $t }}" @selected(($filtros['tabla'] ?? '') === $t)>{{ $t }}</option>@endforeach</select>
        <select name="acc" class="{{ $campo }}"><option value="">Toda acción</option>@foreach (array_keys($colores) as $a)<option value="{{ $a }}" @selected(($filtros['acc'] ?? '') === $a)>{{ $a }}</option>@endforeach</select>
        <input name="usuario" value="{{ $filtros['usuario'] ?? '' }}" placeholder="Usuario (nombre o correo)" class="{{ $campo }}">
        <input name="id_reg" type="number" value="{{ $filtros['id_reg'] ?? '' }}" placeholder="ID del registro" class="{{ $campo }}">
        <input name="ip" value="{{ $filtros['ip'] ?? '' }}" placeholder="IP" class="{{ $campo }}">
        <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="{{ $campo }}" aria-label="Desde">
        <div class="flex gap-2"><input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="min-w-0 flex-1 {{ $campo }}" aria-label="Hasta"><button class="rounded-xl bg-[#1F5F8B] px-3 text-sm font-semibold text-white">Filtrar</button></div>
    </form>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr><th class="p-3">Fecha</th><th class="p-3">Usuario</th><th class="p-3">Acción</th><th class="p-3">Tabla / registro</th><th class="p-3">Campos</th><th class="p-3">IP</th><th class="p-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                @forelse ($registros as $r)
                    <tr>
                        <td class="p-3 whitespace-nowrap">{{ $r->cre_en->format('d/m/Y H:i:s') }}</td>
                        <td class="p-3">{{ $r->usuario?->nom ?? 'Sistema / invitado' }}<br><span class="text-[#6B6F72]">{{ $r->usuario?->cor }}</span></td>
                        <td class="p-3"><span class="rounded-lg px-2 py-1 font-semibold {{ $colores[$r->acc] ?? '' }}">{{ $r->acc }}</span></td>
                        <td class="p-3 font-mono">{{ $r->nom_tbl }} #{{ $r->id_reg }}</td>
                        <td class="p-3 text-[#6B6F72]">{{ \Illuminate\Support\Str::limit(implode(', ', array_keys($r->val_nvo ?? $r->val_ant ?? [])), 60) ?: '—' }}</td>
                        <td class="p-3 font-mono text-[#1F5F8B]">{{ $r->dir_ip }}</td>
                        <td class="p-3 text-right"><a href="{{ route('admin.auditoria.show', $r) }}" class="font-bold text-[#1F5F8B]">Detalle</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-[#6B6F72]">Sin registros con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $registros->links() }}
</div>
@endsection
