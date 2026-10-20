@extends('layouts.admin')

@section('header-title', 'Inventario y equipo de alquiler')

@section('content')
@php $campo = 'rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm'; @endphp
<div class="space-y-6" x-data="{ nuevoTipo: false, tipo: null }">
    @include('admin.partials.mensajes')

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-[#6B6F72]">El stock disponible se descuenta al confirmar una reserva y se repone al cancelarla o al terminar el tour.</p>
        <button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-xs font-semibold text-white" @click="nuevoTipo = !nuevoTipo">+ Tipo de equipo</button>
    </div>

    <form method="POST" action="{{ route('admin.inventario.tipo') }}" x-show="nuevoTipo" x-cloak class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm grid gap-3 md:grid-cols-6">
        @csrf
        <input name="nom" required placeholder="Nombre" class="md:col-span-2 {{ $campo }}">
        <input name="pre_alq_bas" type="number" step="0.01" min="0" required placeholder="Precio alquiler (Bs.)" class="{{ $campo }}">
        <input name="cos_ord" type="number" step="0.01" min="0.01" placeholder="Costo de pedido S (Bs.)" class="{{ $campo }}">
        <input name="cos_mnt_anu" type="number" step="0.01" min="0.01" placeholder="Costo anual H (Bs./u)" class="{{ $campo }}">
        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="eqp_cri" value="1"> Crítico</label>
        <input name="des" placeholder="Descripción" class="md:col-span-6 {{ $campo }}">
        <div class="md:col-span-6"><button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Guardar</button></div>
    </form>

    <div class="grid gap-6 lg:grid-cols-2">
        @forelse ($tipos as $t)
            @php
                $tot = $t->items->sum('cnt_tot');
                $dis = $t->items->sum('cnt_dis');
            @endphp
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-[#343A40]">{{ $t->nom }}
                            @if ($t->eqp_cri)<span class="ml-1 rounded-full bg-[#C96F4A]/15 px-2 py-0.5 text-[10px] font-bold uppercase text-[#C96F4A]">Crítico</span>@endif
                        </h3>
                        <p class="text-xs text-[#6B6F72]">{{ $t->des }}</p>
                        <p class="text-xs text-[#6B6F72] mt-1">Alquiler Bs. {{ number_format($t->pre_alq_bas, 2, ',', '.') }} · S = {{ $t->cos_ord ?? '—' }} · H = {{ $t->cos_mnt_anu ?? '—' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold {{ $tot && $dis / max(1, $tot) < 0.25 ? 'text-[#C96F4A]' : 'text-[#1F5F8B]' }}">{{ $dis }}/{{ $tot }}</p>
                        <p class="text-xs text-[#6B6F72]">disponibles</p>
                    </div>
                </div>

                <table class="w-full text-sm">
                    <thead class="text-xs text-[#6B6F72]"><tr><th class="text-left py-1">Talla</th><th class="text-right">Total</th><th class="text-right">Disponibles</th><th class="text-right">Alquiladas</th><th class="text-left pl-3">Condición</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($t->items as $i)
                            <tr><td class="py-1.5 font-semibold">{{ $i->tal }}</td><td class="text-right">{{ $i->cnt_tot }}</td><td class="text-right">{{ $i->cnt_dis }}</td><td class="text-right">{{ $i->cnt_tot - $i->cnt_dis }}</td><td class="pl-3">{{ $i->con }}</td></tr>
                        @endforeach
                    </tbody>
                </table>

                <form method="POST" action="{{ route('admin.inventario.item', $t) }}" class="flex flex-wrap gap-2">
                    @csrf
                    <input name="tal" required placeholder="Talla" class="w-24 {{ $campo }}">
                    <input name="cnt_tot" type="number" min="0" required placeholder="Total" class="w-24 {{ $campo }}">
                    <select name="con" class="{{ $campo }}"><option value="bueno">Bueno</option><option value="regular">Regular</option><option value="malo">Malo</option></select>
                    <button class="rounded-xl bg-[#1F5F8B] px-3 py-2 text-xs font-semibold text-white">Agregar / ajustar talla</button>
                </form>

                <details>
                    <summary class="cursor-pointer text-xs font-bold text-[#1F5F8B]">Editar tipo</summary>
                    <form method="POST" action="{{ route('admin.inventario.tipo', $t) }}" class="mt-3 grid gap-2 md:grid-cols-2">
                        @csrf
                        <input name="nom" value="{{ $t->nom }}" required class="{{ $campo }}">
                        <input name="pre_alq_bas" type="number" step="0.01" min="0" value="{{ $t->pre_alq_bas }}" class="{{ $campo }}">
                        <input name="cos_ord" type="number" step="0.01" min="0.01" value="{{ $t->cos_ord }}" placeholder="Costo de pedido S" class="{{ $campo }}">
                        <input name="cos_mnt_anu" type="number" step="0.01" min="0.01" value="{{ $t->cos_mnt_anu }}" placeholder="Costo anual H" class="{{ $campo }}">
                        <input name="des" value="{{ $t->des }}" class="md:col-span-2 {{ $campo }}">
                        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="eqp_cri" value="1" @checked($t->eqp_cri)> Equipo crítico</label>
                        <button class="rounded-xl bg-[#1F5F8B] px-3 py-2 text-xs font-semibold text-white">Guardar</button>
                    </form>
                </details>
            </div>
        @empty
            <p class="text-sm text-[#6B6F72]">No hay tipos de equipo.</p>
        @endforelse
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <h3 class="font-bold text-[#343A40] mb-1">Equipo ofrecido por tour</h3>
        <p class="text-xs text-[#6B6F72] mb-4">Marca qué equipo se alquila en cada tour y, si quieres, un precio especial.</p>
        <div class="space-y-4">
            @foreach ($tours as $tour)
                <div class="rounded-xl border border-gray-100 p-3">
                    <p class="font-semibold text-sm mb-2">{{ $tour->nom }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tipos as $t)
                            @php $pivot = $tour->equipos->firstWhere('id', $t->id)?->pivot; $activo = $pivot && $pivot->act; @endphp
                            <form method="POST" action="{{ route('admin.inventario.tour', $tour) }}" class="flex items-center gap-1 rounded-lg border {{ $activo ? 'border-[#1F5F8B] bg-[#1F5F8B]/5' : 'border-gray-200' }} px-2 py-1 text-xs">
                                @csrf
                                <input type="hidden" name="tip_eqp_id" value="{{ $t->id }}">
                                <input type="hidden" name="act" value="{{ $activo ? 0 : 1 }}">
                                <span>{{ $t->nom }}</span>
                                @if ($activo)
                                    <span class="text-[#6B6F72]">· Bs. {{ number_format($pivot->pre_alq_esp ?? $t->pre_alq_bas, 2, ',', '.') }}</span>
                                @endif
                                <button class="ml-1 font-bold {{ $activo ? 'text-red-600' : 'text-[#1F5F8B]' }}">{{ $activo ? 'Quitar' : 'Ofrecer' }}</button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
