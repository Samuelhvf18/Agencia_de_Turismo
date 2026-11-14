{{-- RF-25 a RF-27: EOQ y punto de reorden. Variables: $filas (EoqService::analizar), $z, $lead --}}
@extends('layouts.admin-io')

@section('titulo', 'Inventario de equipo crítico (EOQ)')
@section('subtitulo', 'Q* = √(2DS/H) · R = d·L + SS · SS = z·σd·√L')

@section('acciones')
    <form method="POST" action="{{ route('admin.io.eoq.alertas') }}">
        @csrf
        <button class="rsv-btn px-4 py-2 text-sm"><i class="fa-solid fa-bell"></i> Revisar y emitir alertas</button>
    </form>
@endsection

@section('contenido')
@php $fmt = fn ($v, $d = 2) => number_format((float) $v, $d, ',', '.'); @endphp
<p class="text-sm text-pipocas-texto-2">D = unidades alquiladas en reservas confirmadas o completadas del año calendario actual (vista_demanda_equipo_anual) · S = costo por pedido · H = costo anual de mantener una unidad · L = {{ $lead }} días de reposición · z = {{ $z }} (95 % de nivel de servicio).</p>

<p class="my-4 rounded-xl bg-amber-50 p-4 text-sm">Modelo académico basado en usos/alquileres. D no mide desgaste ni compras de reemplazo: Q y R requieren validar una tasa de reposición antes de usarse como orden de compra. La desviación diaria es una aproximación a partir de medias mensuales.</p>
<div class="grid gap-6 xl:grid-cols-2">
    @foreach ($filas as $f)
        @php $t = $f['tipo']; $e = $f['eoq']; @endphp
        <section class="rsv-tarjeta {{ $f['reponer'] ? 'ring-2 ring-red-300' : '' }}">
            <div class="mb-3 flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-titulo text-xl font-semibold text-pipocas-principal">{{ $t->nom }}
                        @if ($t->eqp_cri)<span class="ml-1 rounded-full bg-pipocas-secundario/15 px-2 py-0.5 align-middle text-[10px] font-extrabold uppercase text-pipocas-secundario">Crítico</span>@endif
                    </h2>
                    <p class="text-xs text-pipocas-texto-2">Stock disponible {{ $f['stock_disponible'] }} de {{ $f['stock_total'] }} · S = Bs {{ $t->cos_ord ?? '—' }} · H = Bs {{ $t->cos_mnt_anu ?? '—' }}</p>
                </div>
                @if ($f['reponer'])
                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700"><i class="fa-solid fa-triangle-exclamation"></i> Reponer</span>
                @elseif ($e)
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Stock suficiente</span>
                @endif
            </div>

            @if ($f['aviso'])
                <p class="rounded-xl bg-pipocas-acento/15 px-3 py-2 text-sm text-pipocas-calido"><i class="fa-solid fa-circle-info"></i> {{ $f['aviso'] }}</p>
            @endif

            @if ($e)
                <dl class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                    <div class="rounded-xl bg-pipocas-fondo p-3"><dt class="text-xs text-pipocas-texto-2">D anual</dt><dd class="text-lg font-bold">{{ $f['demanda_anual'] }}</dd></div>
                    <div class="rounded-xl bg-pipocas-fondo p-3"><dt class="text-xs text-pipocas-texto-2">Q*</dt><dd class="text-lg font-bold text-pipocas-principal">{{ $fmt($e['q'], 1) }} → {{ $e['q_redondeado'] }}</dd></div>
                    <div class="rounded-xl bg-pipocas-fondo p-3"><dt class="text-xs text-pipocas-texto-2">R (reorden)</dt><dd class="text-lg font-bold">{{ $fmt($e['r'], 1) }} → {{ $e['r_redondeado'] }}</dd></div>
                    <div class="rounded-xl bg-pipocas-fondo p-3"><dt class="text-xs text-pipocas-texto-2">SS</dt><dd class="text-lg font-bold">{{ $fmt($e['ss'], 1) }}</dd></div>
                    <div class="rounded-xl bg-pipocas-fondo p-3"><dt class="text-xs text-pipocas-texto-2">d diaria</dt><dd class="font-semibold">{{ $fmt($e['d_diaria'], 3) }}</dd></div>
                    <div class="rounded-xl bg-pipocas-fondo p-3"><dt class="text-xs text-pipocas-texto-2">Pedidos/año</dt><dd class="font-semibold">{{ $fmt($e['pedidos_anuales'], 1) }}</dd></div>
                    <div class="rounded-xl bg-pipocas-fondo p-3"><dt class="text-xs text-pipocas-texto-2">Ciclo</dt><dd class="font-semibold">{{ $fmt($e['ciclo_dias'], 0) }} días</dd></div>
                    <div class="rounded-xl bg-pipocas-fondo p-3"><dt class="text-xs text-pipocas-texto-2">Costo total</dt><dd class="font-semibold">Bs {{ $fmt($e['costo_total']) }}</dd></div>
                </dl>
            @endif

            <div class="mt-4 flex h-16 items-end gap-1" title="Unidades alquiladas por mes (12 meses)">
                @php $max = max(1, max($f['historial'])); @endphp
                @foreach ($f['historial'] as $mes => $n)
                    <div class="flex-1 rounded-t bg-pipocas-complementario/70" style="height: {{ max(3, round($n * 100 / $max)) }}%" title="{{ $mes }}: {{ $n }}"></div>
                @endforeach
            </div>
            <p class="mt-1 text-[11px] text-pipocas-texto-2">Demanda mensual del año calendario actual ({{ $f['meses_con_datos'] }} meses con alquileres).</p>
        </section>
    @endforeach
</div>
@endsection
