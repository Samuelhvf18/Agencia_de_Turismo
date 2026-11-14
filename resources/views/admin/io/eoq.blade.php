@extends('layouts.admin-io')
@section('titulo', 'Planificar equipo')
@section('subtitulo', 'Revisa la disponibilidad y detecta qué equipos necesitan atención antes de las próximas
    salidas.')
    @php
        $fmt = fn($v, $d = 1) => number_format((float) $v, $d, ',', '.');
        $revisar = collect($filas)->where('reponer', true)->count();
        $sinDatos = collect($filas)->filter(fn($f) => !$f['eoq'])->count();
    @endphp
@section('acciones')<a href="{{ route('admin.inventario') }}" class="rsv-btn-sec">
    <i aria-hidden="true" class="fa-solid fa-box-open"></i> Gestionar inventario</a>@endsection
@section('contenido')
    <div class="grid gap-4 sm:grid-cols-3">
        @foreach ([['Tipos de equipo', count($filas), 'Incluidos en la revisión', 'fa-boxes-stacked'], ['Conviene revisar', $revisar, 'Disponibilidad en el umbral del modelo', 'fa-triangle-exclamation'], ['Análisis pendiente', $sinDatos, 'Faltan costos o historial suficiente', 'fa-chart-column']] as [$titulo, $valor, $nota, $icono])
            <div class="admin-metric">
                <div class="flex items-center justify-between"><span
                        class="admin-metric-label">{{ $titulo }}</span><span class="admin-metric-icon"><i aria-hidden="true" class="fa-solid {{ $icono }}"></i></span></div>
                <p class="admin-metric-value">{{ $valor }}</p>
                <p class="admin-metric-note">{{ $nota }}</p>
            </div>
        @endforeach
    </div>
    <div class="admin-notice admin-notice-amber"><i aria-hidden="true" class="fa-regular fa-lightbulb"></i>
        <div><strong>Una ayuda para revisar tu equipo, no una orden de compra.</strong>
            <p>El análisis cuenta alquileres. Como el equipo se reutiliza, confirma su estado físico, las devoluciones
                próximas y el desgaste antes de comprar unidades nuevas.</p>
        </div>
    </div>
    <div x-data="{ filtro: 'todos' }" class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-extrabold">Disponibilidad por equipo</h2>
                <p class="mt-1 text-xs text-slate-500">Empieza por los equipos marcados para revisión.</p>
            </div><label class="flex items-center gap-2 text-xs text-slate-500">Mostrar<select x-model="filtro"
                    class="rounded-xl border-slate-200 text-xs">
                    <option value="todos">Todos los equipos</option>
                    <option value="revisar">Conviene revisar ({{ $revisar }})</option>
                    <option value="pendiente">Análisis pendiente ({{ $sinDatos }})</option>
                </select></label>
        </div>
        <div class="grid gap-5 xl:grid-cols-2">
            @forelse ($filas as $f)
                @php
                    $t = $f['tipo'];
                    $e = $f['eoq'];
                    $estadoEquipo = $f['reponer'] ? 'revisar' : (!$e ? 'pendiente' : 'normal');
                @endphp
                <article class="rsv-tarjeta flex flex-col" x-show="filtro === 'todos' || filtro === '{{ $estadoEquipo }}'">
                    <div class="admin-section-head">
                        <div class="flex items-start gap-3"><span class="admin-metric-icon !h-10 !w-10"><i aria-hidden="true" class="fa-solid fa-box-open"></i></span>
                            <div>
                                <h2>{{ $t->nom }}</h2>
                                <p>{{ $t->eqp_cri ? 'Equipo crítico para la operación' : 'Equipo de alquiler' }}</p>
                            </div>
                        </div><span
                            class="admin-pill {{ $f['reponer'] ? '!bg-amber-50 !text-amber-800' : '' }}">{{ $f['reponer'] ? 'Revisar reposición' : (!$e ? 'Análisis pendiente' : 'Sobre el umbral') }}</span>
                    </div>
                    <div class="mb-5 rounded-xl bg-slate-50 p-4">
                        <div class="flex items-end justify-between">
                            <p class="text-xs text-slate-500">Unidades disponibles</p>
                            <p class="text-2xl font-extrabold tracking-tight">{{ $f['stock_disponible'] }} <span
                                    class="text-sm font-normal text-slate-400">/ {{ $f['stock_total'] }}</span></p>
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-slate-200">
                            <div class="h-full rounded-full {{ $f['reponer'] ? 'bg-amber-500' : 'bg-pipocas-principal' }}"
                                style="width: {{ min(100, max(0, ($f['stock_disponible'] * 100) / max(1, $f['stock_total']))) }}%">
                            </div>
                        </div>
                    </div>
                    @if ($f['aviso'])
                        <div class="admin-notice !text-[11px]"><i aria-hidden="true" class="fa-solid fa-circle-info"></i>
                            <div><strong>No hay una estimación suficiente todavía</strong>
                                <p>{{ !$t->cos_ord || !$t->cos_mnt_anu ? 'Completa el costo por pedido y el mantenimiento anual por unidad en Inventario.' : str_replace(' para estimar D.', ' para calcular una referencia.', $f['aviso']) }}</p><a href="{{ route('admin.inventario') }}"
                                    class="mt-2 inline-block font-bold underline underline-offset-2">Revisar datos del
                                    equipo</a>
                            </div>
                        </div>
                    @else
                        <p class="mb-4 text-xs leading-relaxed text-slate-500">
                            @if ($f['reponer'])
                                Hay {{ $f['stock_disponible'] }} unidades disponibles y el umbral estimado es
                                {{ $e['r_redondeado'] }}. Revisa el estado del equipo y los próximos retornos antes de
                                decidir una reposición.
                            @else
                                La disponibilidad está por encima del umbral estimado de {{ $e['r_redondeado'] }} unidades.
                                Mantén seguimiento de reservas, devoluciones y estado físico.
                            @endif
                        </p>
                        <dl class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-slate-100 p-3">
                                <dt class="text-[10px] text-slate-500">Umbral para revisar existencias</dt>
                                <dd class="mt-1 text-xl font-extrabold">{{ $e['r_redondeado'] }} <span
                                        class="text-[10px] font-normal">unidades</span></dd>
                            </div>
                            <div class="rounded-xl border border-slate-100 p-3">
                                <dt class="text-[10px] text-slate-500">Cantidad orientativa del modelo</dt>
                                <dd class="mt-1 text-xl font-extrabold text-pipocas-principal">{{ $e['q_redondeado'] }}
                                    <span class="text-[10px] font-normal">unidades</span></dd>
                            </div>
                        </dl>
                    @endif
                    <details class="admin-details mt-5 !p-3">
                        <summary>Ver uso registrado y detalle de la estimación</summary>
                        <p class="mb-3 text-[11px] text-slate-500">{{ $f['demanda_anual'] }} alquileres en
                            {{ $f['meses_con_datos'] }} meses con actividad · año {{ now()->year }}.</p>
                        <div class="flex h-24 items-end gap-1" role="img"
                            aria-label="Uso mensual del equipo en {{ now()->year }}">
                            @php $maximo = max(1, ...array_values($f['historial'])); @endphp
                            @foreach ($f['historial'] as $mes => $n)
                                <div class="flex h-full flex-1 flex-col justify-end text-center"><span
                                        class="text-[9px] text-slate-400">{{ $n }}</span>
                                    <div class="mx-auto w-full rounded-t bg-pipocas-principal/70"
                                        style="height: {{ max(2, round(($n * 65) / $maximo)) }}px"
                                        title="{{ $mes }}: {{ $n }} alquileres"></div><span
                                        class="mt-1 text-[8px] text-slate-500">{{ substr($mes, 5) }}</span>
                                </div>
                            @endforeach
                        </div>
                        @if ($e)
                            <dl class="mt-5 grid grid-cols-2 gap-3 text-[11px]">
                                <div>
                                    <dt class="text-slate-500">Reserva de seguridad estimada</dt>
                                    <dd class="font-bold">{{ $fmt($e['ss']) }} unidades</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Costo anual estimado del modelo</dt>
                                    <dd class="font-bold">Bs {{ $fmt($e['costo_total'], 2) }}</dd>
                                </div>
                            </dl>
                        @endif
                    </details>
                </article>
            @empty<div class="admin-empty xl:col-span-2"><i aria-hidden="true" class="fa-solid fa-box-open"></i><strong>No hay equipos
                        registrados</strong>
                    <p>Agrega tipos de equipo y sus unidades para comenzar.</p><a href="{{ route('admin.inventario') }}"
                        class="mt-3 inline-block font-bold text-pipocas-principal">Ir al inventario →</a>
                </div>
            @endforelse
        </div>
        <div x-cloak
            x-show="(filtro === 'revisar' && {{ $revisar }} === 0) || (filtro === 'pendiente' && {{ $sinDatos }} === 0)"
            class="admin-empty">No hay equipos en este grupo. Selecciona “Todos los equipos” para ver el inventario
            completo.</div>
    </div>
    <section class="rsv-tarjeta flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-bold">Deja una alerta para seguimiento</h2>
            <p class="mt-1 max-w-xl text-xs text-slate-500">Registra avisos de los equipos críticos que alcanzaron el umbral
                del modelo. No crea pedidos ni modifica el stock.</p>
        </div>
        <form method="POST" action="{{ route('admin.io.eoq.alertas') }}">@csrf<button class="rsv-btn"><i aria-hidden="true" class="fa-regular fa-bell"></i> Revisar y registrar alertas</button></form>
    </section>
    <details class="admin-details">
        <summary>¿Qué significa EOQ y cómo se calcula?</summary>
        <p class="mb-4 text-xs leading-relaxed text-slate-500">EOQ significa “cantidad económica de pedido”. Equilibra el
            costo de hacer pedidos con el de mantener existencias. Aquí se usa como referencia académica: cada alquiler no
            equivale a equipo consumido.</p>
        <div class="technical-grid">
            <div><strong>Demanda (D)</strong>
                <p>Alquileres del año calendario actual, desde la vista de demanda anual.</p>
            </div>
            <div><strong>Costos de pedido y mantenimiento</strong>
                <p>Son los valores registrados por equipo en Inventario. Si faltan, no se muestra un cálculo.</p>
            </div>
            <div><strong>Plazo de reposición</strong>
                <p>{{ $lead }} días, con factor de seguridad {{ $z }}. La variación diaria se aproxima
                    desde medias mensuales.</p>
            </div>
            <div><strong>Fórmulas del modelo</strong>
                <p>Q = √(2 × D × S / H). Umbral R = demanda diaria × plazo + reserva de seguridad.</p>
            </div>
        </div>
    </details>
@endsection
