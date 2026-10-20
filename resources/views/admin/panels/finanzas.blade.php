@extends('layouts.admin')

@section('header-title', 'Finanzas, Facturación y Pagos')

@section('content')
@php $bs = fn ($n) => 'Bs. ' . number_format((float) $n, 2, ',', '.'); @endphp
<div class="space-y-6">
    @if (session('estado'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('estado') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-wrap items-end gap-3">
        <label class="text-xs text-[#6B6F72]">Desde<input type="date" name="desde" value="{{ $filtros['desde'] }}" class="mt-1 block rounded-xl border border-gray-300 px-3 py-2 text-sm"></label>
        <label class="text-xs text-[#6B6F72]">Hasta<input type="date" name="hasta" value="{{ $filtros['hasta'] }}" class="mt-1 block rounded-xl border border-gray-300 px-3 py-2 text-sm"></label>
        <label class="text-xs text-[#6B6F72]">Comprobantes
            <select name="est" class="mt-1 block rounded-xl border border-gray-300 px-3 py-2 text-sm">
                <option value="">Todos</option>
                @foreach (['pendiente', 'pagado', 'anulado'] as $e)
                    <option value="{{ $e }}" @selected(($filtros['est'] ?? '') === $e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </label>
        <button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Aplicar</button>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach ($metodos as $mtd => $txt)
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <span class="text-xs text-[#6B6F72]">{{ $txt }}</span>
                <h4 class="text-xl font-bold text-[#1F5F8B] mt-1">{{ $bs($cobros[$mtd]->total ?? 0) }}</h4>
                <span class="text-xs text-[#6B6F72]">{{ $cobros[$mtd]->n ?? 0 }} cobro(s)</span>
            </div>
        @endforeach
        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
            <span class="text-xs text-[#6B6F72]">Por cobrar (saldos)</span>
            <h4 class="text-xl font-bold text-[#947412] mt-1">{{ $bs($porCobrar) }}</h4>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr><th class="p-4">Comprobante</th><th class="p-4">Reserva</th><th class="p-4">Emitido</th><th class="p-4 text-right">Total</th><th class="p-4 text-right">Pagado</th><th class="p-4">Estado</th><th class="p-4 text-right">Acciones</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($comprobantes as $c)
                    <tr class="align-top">
                        <td class="p-4 font-mono">{{ $c->cod }}</td>
                        <td class="p-4"><a href="{{ route('admin.reservas.show', $c->reserva) }}" class="font-mono text-[#1F5F8B]">{{ $c->reserva->cod }}</a><br><span class="text-xs text-[#6B6F72]">{{ $c->reserva->salida->tour->nom }}</span></td>
                        <td class="p-4">{{ $c->fec_emi->format('d/m/Y') }}</td>
                        <td class="p-4 text-right">{{ $bs($c->mto_tot) }}</td>
                        <td class="p-4 text-right">{{ $bs($c->pagos->where('est', 'completado')->sum('mto')) }}</td>
                        <td class="p-4">{{ ucfirst($c->est) }}</td>
                        <td class="p-4 text-right" x-data>
                            <a href="{{ route('admin.finanzas.comprobante', $c) }}" class="text-xs font-bold text-[#1F5F8B]">PDF</a>
                            @if ($c->est !== 'anulado' && auth()->user()->esAdmin())
                                <form method="POST" action="{{ route('admin.finanzas.anular', $c) }}" class="mt-2 flex justify-end gap-1" onsubmit="return confirm('¿Anular el comprobante {{ $c->cod }}? La reserva se cancelará.')">
                                    @csrf
                                    <input type="text" name="motivo" required maxlength="200" placeholder="Motivo" class="w-32 rounded-lg border border-gray-300 px-2 py-1 text-xs">
                                    <button class="rounded-lg bg-red-600 px-2 py-1 text-xs font-semibold text-white">Anular</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-[#6B6F72]">Sin comprobantes en el periodo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $comprobantes->links() }}

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h3 class="font-bold text-[#343A40] mb-3">Últimos intentos de pago</h3>
        <ul class="divide-y divide-gray-100 text-sm">
            @forelse ($pagosRecientes as $p)
                <li class="flex flex-wrap justify-between gap-2 py-2">
                    <span>{{ $p->pag_en->format('d/m/Y H:i') }} · {{ $metodos[$p->mtd] ?? $p->mtd }} · <span class="font-mono text-xs">{{ $p->ref_trx }}</span> · reserva {{ $p->comprobante->reserva->cod }}</span>
                    <span><b class="{{ $p->est === 'completado' ? 'text-emerald-700' : ($p->est === 'fallido' ? 'text-red-600' : 'text-[#947412]') }}">{{ $p->est }}</b> · {{ $bs($p->mto) }}</span>
                </li>
            @empty
                <li class="py-2 text-[#6B6F72]">Sin pagos todavía.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
