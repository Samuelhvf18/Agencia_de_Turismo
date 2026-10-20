@extends('layouts.admin')

@section('header-title', 'Auditoría · ' . $r->nom_tbl . ' #' . $r->id_reg)

@section('content')
@php $fmt = fn ($v) => is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : (is_bool($v) ? ($v ? 'true' : 'false') : ($v ?? '∅')); @endphp
<div class="space-y-6">
    <a href="{{ route('admin.auditoria') }}" class="text-sm font-semibold text-[#1F5F8B]">← Volver a la bitácora</a>

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <dl class="grid gap-4 text-sm sm:grid-cols-3 lg:grid-cols-6">
            <div><dt class="text-xs text-[#6B6F72]">Acción</dt><dd class="font-bold">{{ $r->acc }}</dd></div>
            <div><dt class="text-xs text-[#6B6F72]">Fecha</dt><dd>{{ $r->cre_en->format('d/m/Y H:i:s') }}</dd></div>
            <div><dt class="text-xs text-[#6B6F72]">Usuario</dt><dd>{{ $r->usuario?->nom ?? 'Sistema / invitado' }}</dd></div>
            <div><dt class="text-xs text-[#6B6F72]">IP</dt><dd class="font-mono">{{ $r->dir_ip ?? '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-xs text-[#6B6F72]">Agente</dt><dd class="text-xs break-all">{{ $r->age_usr ?? '—' }}</dd></div>
        </dl>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase"><tr><th class="p-3">Campo</th><th class="p-3">Valor anterior</th><th class="p-3">Valor nuevo</th></tr></thead>
            <tbody class="divide-y divide-gray-100 font-mono text-xs">
                @forelse ($cambios as $c)
                    <tr class="{{ $c['cambio'] ? 'bg-[#F4C95D]/10' : '' }}">
                        <td class="p-3 font-semibold">{{ $c['campo'] }}</td>
                        <td class="p-3 text-red-700 break-all">{{ $fmt($c['antes']) }}</td>
                        <td class="p-3 text-emerald-700 break-all">{{ $fmt($c['despues']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-6 text-center font-sans text-[#6B6F72]">Lectura de datos sensibles: no hay cambios que mostrar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h3 class="font-bold text-[#343A40] mb-3">Historial del registro</h3>
        <ul class="divide-y divide-gray-100 text-sm">
            @foreach ($historial as $h)
                <li class="flex flex-wrap justify-between gap-2 py-2 {{ $h->id === $r->id ? 'font-semibold' : '' }}">
                    <span>{{ $h->cre_en->format('d/m/Y H:i:s') }} · {{ $h->acc }} · {{ $h->usuario?->nom ?? 'Sistema / invitado' }}</span>
                    <a href="{{ route('admin.auditoria.show', $h) }}" class="text-xs font-bold text-[#1F5F8B]">Ver</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
