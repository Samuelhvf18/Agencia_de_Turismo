{{--
    Panel «Ciclo de vida del cliente» — Módulo 07, Cadenas de Markov.
    Variables (las arma CicloVidaClienteController o IoDemoData en la vista previa):
        $estados      lista ordenada: cod, nom, col, es_abs, clientes
        $P            matriz de transición [cod_origen][cod_destino] => probabilidad
        $fecCal       fecha de calibración de la matriz vigente
        $absorcion    filas por estado transitorio: cod, nom, p_fid, p_per, meses
        $N            matriz fundamental [cod][cod] (solo estados transitorios)
        $proyeccion   inicio (cod), meses [0..n], series [cod => [probabilidades]]
        $cohortes     lista: nom, per_cap, tot, filas [per_rel => [activos, fidelizados, perdidos]] (proporciones)
        $totalClientes total de clientes con estado registrado
        $formAction   URL del formulario (GET)
--}}
@extends('layouts.admin-io')

@section('titulo', 'Ciclo de vida del cliente')
@section('subtitulo', 'Cadena de Markov absorbente · período mensual · fidelizado y perdido son estados absorbentes')

@php
    $fmt = fn ($v, $d = 2) => number_format((float) $v, $d, ',', '.');
    $pct = fn ($v, $d = 1) => number_format((float) $v * 100, $d, ',', '.') . ' %';
    $porCod = collect($estados)->keyBy('cod');
    $transitorios = collect($estados)->where('es_abs', false)->values();
    $fidProspecto = collect($absorcion)->firstWhere('cod', 'prospecto');
@endphp

@section('acciones')
    <span class="inline-flex items-center gap-2 rounded-xl bg-pipocas-fondo px-3 py-2 text-xs font-semibold text-pipocas-texto-2">
        <i class="fa-regular fa-calendar"></i> Matriz calibrada el {{ $fecCal }}
    </span>
@endsection

@section('contenido')

    {{-- ===================== Clientes por estado ===================== --}}
    <section class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
        @foreach ($estados as $e)
            <div class="rounded-2xl bg-white p-4 shadow-tarjeta">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full" style="background: {{ $e['col'] }}"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-pipocas-texto-2">{{ $e['nom'] }}</span>
                </div>
                <p class="mt-2 font-titulo text-3xl font-semibold text-pipocas-principal">{{ $e['clientes'] }}</p>
                <p class="text-xs text-pipocas-texto-2">{{ $totalClientes ? $pct($e['clientes'] / $totalClientes, 0) : '0 %' }} de los clientes{{ $e['es_abs'] ? ' · absorbente' : '' }}</p>
            </div>
        @endforeach
        <div class="rounded-2xl bg-pipocas-principal p-4 text-white shadow-tarjeta">
            <span class="text-xs font-bold uppercase tracking-wider text-white/70">Prospecto → fidelizado</span>
            <p class="mt-2 font-titulo text-3xl font-semibold">{{ $pct($fidProspecto['p_fid'] ?? 0) }}</p>
            <p class="text-xs text-white/70">en {{ $fmt($fidProspecto['meses'] ?? 0, 1) }} meses en promedio</p>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-5">
        {{-- ===================== Diagrama de estados ===================== --}}
        <section class="rsv-tarjeta xl:col-span-3">
            <h3 class="rsv-titulo">Diagrama de transición</h3>
            <p class="mt-1 text-sm text-pipocas-texto-2">Probabilidad mensual de pasar de un estado a otro (pᵢⱼ). Los bucles indican la probabilidad de permanecer.</p>
            @php
                // Posición fija de cada estado en el lienzo (viewBox 760 x 360)
                $pos = ['prospecto' => [110, 90], 'reservado' => [380, 90], 'completado' => [650, 90], 'perdido' => [380, 290], 'fidelizado' => [650, 290]];
                $arcos = [];
                foreach ($P as $o => $fila) {
                    foreach ($fila as $d => $p) {
                        if ($p > 0 && $o !== $d && isset($pos[$o], $pos[$d])) $arcos[] = [$o, $d, $p];
                    }
                }
            @endphp
            <div class="mt-4 overflow-x-auto">
                <svg viewBox="0 0 760 390" class="w-full min-w-[620px]" role="img" aria-label="Diagrama de estados de la cadena de Markov">
                    <defs>
                        <marker id="flecha" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                            <path d="M0,0 L10,5 L0,10 z" fill="#457699"/>
                        </marker>
                    </defs>
                    @foreach ($arcos as [$o, $d, $p])
                        @php
                            [$x1, $y1] = $pos[$o]; [$x2, $y2] = $pos[$d];
                            $dx = $x2 - $x1; $dy = $y2 - $y1; $len = sqrt($dx * $dx + $dy * $dy);
                            // recorta en el borde de las cajas (110 x 46) y separa los arcos de ida y vuelta
                            $ux = $dx / $len; $uy = $dy / $len;
                            $t = min(abs($dx) > 0 ? 60 / abs($ux) : INF, abs($dy) > 0 ? 28 / abs($uy) : INF);
                            $off = isset($P[$d][$o]) && $P[$d][$o] > 0 ? 9 : 0;
                            $nx = -$uy * $off; $ny = $ux * $off;
                            $ax = $x1 + $ux * $t + $nx; $ay = $y1 + $uy * $t + $ny;
                            $bx = $x2 - $ux * $t + $nx; $by = $y2 - $uy * $t + $ny;
                            $mx = ($ax + $bx) / 2 + $nx * 1.6; $my = ($ay + $by) / 2 + $ny * 1.6;
                        @endphp
                        <line x1="{{ $ax }}" y1="{{ $ay }}" x2="{{ $bx }}" y2="{{ $by }}" stroke="#457699" stroke-width="{{ 1 + $p * 4 }}" marker-end="url(#flecha)" opacity=".85"/>
                        <rect x="{{ $mx - 20 }}" y="{{ $my - 10 }}" width="40" height="18" rx="6" fill="#fff" stroke="#e5e7eb"/>
                        <text x="{{ $mx }}" y="{{ $my + 3 }}" text-anchor="middle" font-size="11" font-weight="700" fill="#1F5F8B">{{ $fmt($p, 2) }}</text>
                    @endforeach
                    @foreach ($pos as $cod => [$x, $y])
                        @php $e = $porCod[$cod] ?? null; $pp = $P[$cod][$cod] ?? 0; @endphp
                        @if ($e)
                            @php $sg = $e['es_abs'] ? 1 : -1; @endphp {{-- bucle arriba (transitorios) o abajo (absorbentes) --}}
                            <path d="M {{ $x - 14 }} {{ $y + 23 * $sg }} C {{ $x - 22 }} {{ $y + 62 * $sg }}, {{ $x + 22 }} {{ $y + 62 * $sg }}, {{ $x + 14 }} {{ $y + 23 * $sg }}" fill="none" stroke="#9ca3af" stroke-width="1.5" marker-end="url(#flecha)"/>
                            <text x="{{ $x }}" y="{{ $y + ($sg > 0 ? 66 : -54) }}" text-anchor="middle" font-size="11" font-weight="700" fill="#6B6F72">{{ $fmt($pp, 2) }}</text>
                            <rect x="{{ $x - 60 }}" y="{{ $y - 23 }}" width="120" height="46" rx="14" fill="{{ $e['col'] }}" fill-opacity="{{ $e['es_abs'] ? '1' : '.92' }}" stroke="{{ $e['es_abs'] ? '#343A40' : 'none' }}" stroke-width="{{ $e['es_abs'] ? 2 : 0 }}"/>
                            <text x="{{ $x }}" y="{{ $y - 2 }}" text-anchor="middle" font-size="13" font-weight="800" fill="{{ $cod === 'reservado' ? '#6B4F3A' : '#fff' }}">{{ $e['nom'] }}</text>
                            <text x="{{ $x }}" y="{{ $y + 14 }}" text-anchor="middle" font-size="10" fill="{{ $cod === 'reservado' ? '#6B4F3A' : '#ffffffcc' }}">{{ $e['es_abs'] ? 'absorbente' : $e['clientes'] . ' clientes' }}</text>
                        @endif
                    @endforeach
                </svg>
            </div>
        </section>

        {{-- ===================== Absorción ===================== --}}
        <section class="rsv-tarjeta xl:col-span-2">
            <h3 class="rsv-titulo">Probabilidades de absorción</h3>
            <p class="mt-1 text-sm text-pipocas-texto-2">B = N·R y t = N·1, con N = (I − Q)⁻¹ la matriz fundamental.</p>
            <div class="mt-4 space-y-4">
                @foreach ($absorcion as $a)
                    <div>
                        <div class="flex items-baseline justify-between text-sm">
                            <span class="font-semibold">{{ $a['nom'] }}</span>
                            <span class="text-xs text-pipocas-texto-2">{{ $fmt($a['meses'], 2) }} meses hasta absorción</span>
                        </div>
                        <div class="mt-1.5 flex h-6 overflow-hidden rounded-full text-[11px] font-bold">
                            <div class="flex items-center justify-center text-white" style="width: {{ $a['p_fid'] * 100 }}%; background: {{ $porCod['fidelizado']['col'] ?? '#C96F4A' }}">{{ $pct($a['p_fid'], 0) }}</div>
                            <div class="flex items-center justify-center text-white" style="width: {{ $a['p_per'] * 100 }}%; background: {{ $porCod['perdido']['col'] ?? '#174870' }}">{{ $pct($a['p_per'], 0) }}</div>
                        </div>
                    </div>
                @endforeach
                <div class="flex gap-4 text-xs text-pipocas-texto-2">
                    <span><span class="mr-1 inline-block h-2.5 w-2.5 rounded-full" style="background: {{ $porCod['fidelizado']['col'] ?? '#C96F4A' }}"></span>Fidelizado</span>
                    <span><span class="mr-1 inline-block h-2.5 w-2.5 rounded-full" style="background: {{ $porCod['perdido']['col'] ?? '#174870' }}"></span>Perdido</span>
                </div>
            </div>

            <h4 class="mt-6 text-sm font-bold">Matriz fundamental N (meses esperados en cada estado)</h4>
            <table class="mt-2 w-full text-center text-xs">
                <thead class="text-pipocas-texto-2"><tr><th class="p-1.5 text-left">Desde \ en</th>@foreach ($transitorios as $t)<th class="p-1.5">{{ $t['nom'] }}</th>@endforeach</tr></thead>
                <tbody>
                    @foreach ($transitorios as $o)
                        <tr class="border-t border-black/5"><td class="p-1.5 text-left font-semibold">{{ $o['nom'] }}</td>
                            @foreach ($transitorios as $d)<td class="p-1.5">{{ $fmt($N[$o['cod']][$d['cod']] ?? 0, 2) }}</td>@endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>

    <div class="grid gap-6 xl:grid-cols-5">
        {{-- ===================== Matriz P ===================== --}}
        <section class="rsv-tarjeta xl:col-span-2">
            <h3 class="rsv-titulo">Matriz de transición P</h3>
            <p class="mt-1 text-sm text-pipocas-texto-2">Cada fila suma 1. Intensidad del color = probabilidad. PRO = prospecto, RES = reservado, COM = completado, FID = fidelizado, PER = perdido.</p>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-center text-xs">
                    <thead class="text-pipocas-texto-2"><tr><th class="p-2 text-left">De \ A</th>@foreach ($estados as $e)<th class="p-2" title="{{ $e['nom'] }}">{{ mb_strtoupper(mb_substr($e['cod'], 0, 3)) }}</th>@endforeach</tr></thead>
                    <tbody>
                        @foreach ($estados as $o)
                            <tr>
                                <td class="p-2 text-left font-semibold">{{ $o['nom'] }}</td>
                                @foreach ($estados as $d)
                                    @php $p = $P[$o['cod']][$d['cod']] ?? 0; @endphp
                                    <td class="p-0.5"><span class="block rounded-lg px-1 py-2 font-semibold {{ $p >= .5 ? 'text-white' : 'text-pipocas-texto' }}" style="background: rgba(31,95,139,{{ max(0.04, $p) }})">{{ $p > 0 ? $fmt($p, 2) : '—' }}</span></td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- ===================== Proyección ===================== --}}
        <section class="rsv-tarjeta xl:col-span-3">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 class="rsv-titulo">Proyección v(n) = v(0)·Pⁿ</h3>
                    <p class="mt-1 text-sm text-pipocas-texto-2">Distribución esperada de una cohorte que parte del estado elegido.</p>
                </div>
                <form method="GET" action="{{ $formAction }}" class="flex items-center gap-2 text-sm">
                    <label for="inicio" class="font-semibold">Estado inicial</label>
                    <select id="inicio" name="inicio" onchange="this.form.submit()" class="rounded-xl border-black/10 py-2 text-sm focus:border-pipocas-principal focus:ring-pipocas-principal">
                        @foreach ($transitorios as $t)
                            <option value="{{ $t['cod'] }}" @selected($t['cod'] === $proyeccion['inicio'])>{{ $t['nom'] }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="mt-4 h-72"><canvas id="graficoProyeccion" aria-label="Proyección de la cadena de Markov"></canvas></div>
            @php $ult = count($proyeccion['meses']) - 1; @endphp
            <div class="mt-3 grid grid-cols-2 gap-2 text-xs sm:grid-cols-5">
                @foreach ($estados as $e)
                    <p class="rounded-lg bg-pipocas-fondo px-2 py-1.5">{{ $e['nom'] }} a {{ $ult }} meses: <strong>{{ $pct($proyeccion['series'][$e['cod']][$ult] ?? 0) }}</strong></p>
                @endforeach
            </div>
        </section>
    </div>

    {{-- ===================== Cohortes ===================== --}}
    <section class="rsv-tarjeta">
        <h3 class="rsv-titulo">Cohortes de retención</h3>
        <p class="mt-1 text-sm text-pipocas-texto-2">Clientes captados en cada mes y su situación en los meses siguientes (mes 0 = captación). Activos = prospecto, reservado o completado.</p>
        @if (count($cohortes))
            @php $maxRel = collect($cohortes)->map(fn ($c) => count($c['filas']))->max(); @endphp
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-center text-xs">
                    <thead class="bg-pipocas-fondo text-pipocas-texto-2">
                        <tr><th class="rounded-l-xl p-2 text-left">Cohorte</th><th class="p-2">Clientes</th>
                            @for ($r = 0; $r < $maxRel; $r++)<th class="p-2 {{ $r === $maxRel - 1 ? 'rounded-r-xl' : '' }}">Mes {{ $r }}</th>@endfor</tr>
                    </thead>
                    <tbody>
                        @foreach ($cohortes as $co)
                            <tr class="border-t border-black/5">
                                <td class="p-2 text-left font-semibold">{{ $co['nom'] }}</td>
                                <td class="p-2">{{ $co['tot'] }}</td>
                                @for ($r = 0; $r < $maxRel; $r++)
                                    @php $f = $co['filas'][$r] ?? null; @endphp
                                    <td class="p-1">
                                        @if ($f)
                                            <span class="block rounded-lg px-1 py-1.5" style="background: rgba(31,95,139,{{ max(0.05, $f['activos'] + $f['fidelizados']) * .55 }})" title="Activos {{ $pct($f['activos']) }} · Fidelizados {{ $pct($f['fidelizados']) }} · Perdidos {{ $pct($f['perdidos']) }}">
                                                <strong class="block">{{ $pct($f['activos'] + $f['fidelizados'], 0) }}</strong>
                                                <span class="text-[10px]">fid. {{ $pct($f['fidelizados'], 0) }}</span>
                                            </span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="mt-4 rounded-2xl border border-dashed border-black/10 p-6 text-center text-sm text-pipocas-texto-2">Aún no hay cohortes. Se generan con <code class="rounded bg-pipocas-fondo px-1">php artisan io:recalibrar-markov</code>.</p>
        @endif
    </section>
@endsection

@push('scripts')
<script>
    Chart.defaults.font.family = '"Plus Jakarta Sans", system-ui, sans-serif';
    Chart.defaults.color = '#6B6F72';
    const estados = @json(array_values($estados));
    const proy = @json($proyeccion);
    new Chart(document.getElementById('graficoProyeccion'), {
        type: 'line',
        data: {
            labels: proy.meses.map(m => 'Mes ' + m),
            datasets: estados.map(e => ({ label: e.nom, data: proy.series[e.cod], borderColor: e.col, backgroundColor: e.col + '55', fill: true, tension: .3, pointRadius: 2 }))
        },
        options: { maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: c => c.dataset.label + ': ' + (c.raw * 100).toFixed(1) + ' %' } } },
            scales: { y: { stacked: true, max: 1, ticks: { callback: v => (v * 100) + ' %' } } } }
    });
</script>
@endpush
