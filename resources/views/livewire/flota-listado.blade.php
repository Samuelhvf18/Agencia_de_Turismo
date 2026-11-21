<section x-data="{ editar: null }" class="space-y-4" aria-label="Listado de vehículos">
@php
$campo = 'w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm';
$colores = ['activo' => 'bg-emerald-100 text-emerald-800', 'mantenimiento' => 'bg-amber-100 text-amber-800', 'inactivo' => 'bg-gray-200 text-gray-600'];
@endphp
<div class="grid gap-3 rounded-2xl bg-white p-4 sm:grid-cols-2"><div><label for="flota-buscar" class="rsv-etiqueta">Buscar vehículo</label><input id="flota-buscar" type="search" maxlength="120" wire:model.live.debounce.350ms="buscar" class="rsv-campo" placeholder="Placa, marca o modelo"></div><div><label for="flota-estado" class="rsv-etiqueta">Estado</label><select id="flota-estado" wire:model.live="estado" class="rsv-campo"><option value="todos">Todos</option>@foreach ($colores as $valor => $clase)<option value="{{ $valor }}">{{ ucfirst($valor) }}</option>@endforeach</select></div></div>
<p wire:loading class="text-sm text-slate-500" role="status">Actualizando vehículos…</p>
    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr><th class="p-4">Placa</th><th class="p-4">Vehículo</th><th class="p-4">Asientos</th><th class="p-4">Combustible</th><th class="p-4">Recorrido / gasto</th><th class="p-4">Salidas futuras</th><th class="p-4">Estado</th><th class="p-4"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($vehiculos as $v)
                    <tr>
                        <td class="p-4 font-mono font-semibold">{{ $v->pla }}</td>
                        <td class="p-4">{{ $v->mar }} {{ $v->mdl }}<br><span class="text-xs text-[#6B6F72]">{{ ucfirst($v->tip) }}</span></td>
                        <td class="p-4">{{ $v->cap_asi }}</td>
                        <td class="p-4">{{ ucfirst($v->tip_cmb ?? '—') }}</td>
                        <td class="p-4">{{ $v->futuras }}</td>
                        <td class="p-4"><span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $colores[$v->est] ?? '' }}">{{ ucfirst($v->est) }}</span></td>
                        <td class="p-4 text-right"><button class="text-xs font-bold text-[#1F5F8B]" @click="editar = editar === {{ $v->id }} ? null : {{ $v->id }}">Editar</button></td>
                    </tr>
                    <tr x-show="editar === {{ $v->id }}" x-cloak class="bg-gray-50">
                        <td colspan="8" class="p-4">
                            <form method="POST" action="{{ route('admin.transporte.update', $v) }}" class="grid gap-2 md:grid-cols-7">
                                @csrf @method('PUT')
                                @if (auth()->user()->esAdmin())
                                <input name="pla" value="{{ $v->pla }}" class="{{ $campo }}">
                                <input name="mar" value="{{ $v->mar }}" class="{{ $campo }}">
                                <input name="mdl" value="{{ $v->mdl }}" class="{{ $campo }}">
                                <select name="tip" class="{{ $campo }}">@foreach (['van', 'minibus', 'otro'] as $t)<option value="{{ $t }}" @selected($v->tip === $t)>{{ $t }}</option>@endforeach</select>
                                <select name="tip_cmb" class="{{ $campo }}">@foreach (['diesel', 'gasolina', 'gnv', 'electrico'] as $t)<option value="{{ $t }}" @selected($v->tip_cmb === $t)>{{ $t }}</option>@endforeach</select>
                                <select name="est" class="{{ $campo }}">@foreach (array_keys($colores) as $e)<option value="{{ $e }}" @selected($v->est === $e)>{{ $e }}</option>@endforeach</select>
                                @else
                                <p class="md:col-span-7 text-sm text-slate-600">Puedes ajustar los asientos. La identidad y el estado del vehículo los administra el administrador.</p>
                                @endif
                                <label class="md:col-span-2 text-xs">Número de asientos<input name="cap_asi" type="number" min="1" max="60" value="{{ $v->cap_asi }}" required class="{{ $campo }}"></label>
                                <div class="md:col-span-7"><button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-xs font-semibold text-white">Guardar</button></div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-6 text-center text-[#6B6F72]">No hay vehículos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
