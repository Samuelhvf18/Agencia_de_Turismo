@extends('layouts.admin')

@section('header-title', 'Reserva ' . $reserva->cod)

@section('content')
@php $bs = fn ($n) => 'Bs. ' . number_format($n, 2, ',', '.'); @endphp
<div class="space-y-6">
    <a href="{{ route('admin.reservas') }}" class="text-sm font-semibold text-[#1F5F8B]">← Volver a reservas</a>

    @if (session('estado'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('estado') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="font-bold text-[#343A40] mb-4">Datos de la reserva</h3>
            <dl class="grid gap-4 sm:grid-cols-3 text-sm">
                <div><dt class="text-[#6B6F72]">Tour</dt><dd class="font-semibold">{{ $reserva->salida->tour->nom }}</dd></div>
                <div><dt class="text-[#6B6F72]">Salida</dt><dd class="font-semibold">{{ $reserva->salida->fec_sal->format('d/m/Y') }} {{ substr($reserva->salida->hra_sal, 0, 5) }}</dd></div>
                <div><dt class="text-[#6B6F72]">Guía</dt><dd class="font-semibold">{{ $reserva->salida->guia?->usuario?->nom ?? 'Por asignar' }}</dd></div>
                <div><dt class="text-[#6B6F72]">Contacto</dt><dd class="font-semibold">{{ $reserva->nom_ctc }}</dd></div>
                <div><dt class="text-[#6B6F72]">Correo / teléfono</dt><dd>{{ $reserva->cor_ctc }}<br>{{ $reserva->tel_ctc }}</dd></div>
                <div><dt class="text-[#6B6F72]">Cuenta</dt><dd>{{ $reserva->usuario?->cor ?? 'Invitado' }}</dd></div>
                <div><dt class="text-[#6B6F72]">Estado</dt><dd class="font-semibold">{{ ucfirst($reserva->est) }} · pago {{ $reserva->est_pag }}</dd></div>
                <div><dt class="text-[#6B6F72]">Total</dt><dd class="font-semibold">{{ $bs($reserva->pre_tot) }}</dd></div>
                <div><dt class="text-[#6B6F72]">Saldo</dt><dd class="font-semibold text-[#C96F4A]">{{ $bs($saldo) }}</dd></div>
            </dl>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-[#343A40]">Acciones</h3>
            @if ($saldo > 0 && in_array($reserva->est, ['pendiente', 'confirmada']))
                <form method="POST" action="{{ route('admin.finanzas.efectivo', $reserva) }}" class="space-y-2">
                    @csrf
                    <label class="text-xs text-[#6B6F72]">Registrar cobro en efectivo (Bs.)</label>
                    <div class="flex gap-2">
                        <input type="number" step="0.01" min="0.01" max="{{ $saldo }}" name="mto" value="{{ $saldo }}" class="min-w-0 flex-1 rounded-xl border border-gray-300 px-3 py-2 text-sm">
                        <button class="rounded-xl bg-[#1F5F8B] px-4 text-sm font-semibold text-white">Cobrar</button>
                    </div>
                </form>
            @endif
            @if ($reserva->comprobante)
                <a href="{{ route('admin.finanzas.comprobante', $reserva->comprobante) }}" class="block text-sm font-semibold text-[#1F5F8B]">Descargar comprobante {{ $reserva->comprobante->cod }}</a>
            @endif
            @if (in_array($reserva->est, ['pendiente', 'confirmada']))
                <form method="POST" action="{{ route('admin.reservas.cancelar', $reserva) }}" class="space-y-2 border-t border-gray-100 pt-4">
                    @csrf
                    <label class="text-xs text-[#6B6F72]">Cancelar reserva (motivo)</label>
                    <input type="text" name="motivo" required maxlength="200" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" placeholder="Ej. solicitud del cliente">
                    <button class="w-full rounded-xl bg-red-600 py-2 text-sm font-semibold text-white">Cancelar reserva</button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr><th class="p-4">Pasajero</th><th class="p-4">Documento</th><th class="p-4">Ficha médica</th><th class="p-4">Equipo</th><th class="p-4">Boleto</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($reserva->participantes as $p)
                    <tr class="align-top">
                        <td class="p-4 font-medium">{{ $p->nom_cpl }}{{ $p->es_ctc_pri ? ' ★' : '' }}<br><span class="text-xs text-[#6B6F72]">{{ $p->eda }} años · {{ $p->pais?->nom }}</span></td>
                        <td class="p-4">{{ $p->tip_doc }} {{ $p->num_doc }}{{ $p->ci_cmp ? '-'.$p->ci_cmp : '' }} {{ $p->departamento?->cod }}</td>
                        <td class="p-4 text-xs">
                            @if ($p->fichaMedica)
                                <span class="font-semibold text-emerald-700">Firmada {{ $p->fichaMedica->fir_en->format('d/m H:i') }}</span><br>
                                {{ $p->fichaMedica->con_med ?: 'Sin condiciones' }}<br>
                                Emergencia: {{ $p->fichaMedica->nom_ctc_eme }} ({{ $p->fichaMedica->tel_ctc_eme }})
                            @else
                                <span class="text-[#C96F4A]">Sin ficha</span>
                            @endif
                        </td>
                        <td class="p-4 text-xs">
                            @forelse ($p->alquileres as $a)
                                {{ $a->item->tipo->nom }} ({{ $a->item->tal }})<br>
                            @empty
                                —
                            @endforelse
                        </td>
                        <td class="p-4 text-xs font-mono">{{ $p->boleto ? $p->boleto->cod.' · '.$p->boleto->est : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h3 class="font-bold text-[#343A40] mb-3">Historial de pagos</h3>
        <ul class="divide-y divide-gray-100 text-sm">
            @forelse ($reserva->comprobante?->pagos->sortByDesc('pag_en') ?? [] as $pago)
                <li class="flex justify-between py-2">
                    <span>{{ $pago->pag_en->format('d/m/Y H:i') }} · {{ $metodos[$pago->mtd] ?? $pago->mtd }} · <span class="font-mono text-xs">{{ $pago->ref_trx }}</span> · <b>{{ $pago->est }}</b></span>
                    <span class="font-semibold">{{ $bs($pago->mto) }}</span>
                </li>
            @empty
                <li class="py-2 text-[#6B6F72]">Sin pagos registrados.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
