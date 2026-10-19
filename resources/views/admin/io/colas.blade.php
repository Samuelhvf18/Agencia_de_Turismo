{{--
    Panel «Análisis de colas de reservas» — Módulo 07, Teoría de colas M/M/c/K.
    Variables (las arma AnalisisColasController o IoDemoData en la vista previa):
        $tours       lista de tours con sus métricas: id, nom, lambda, mu, c, k, rho, lq, wq_hrs, p_rec, estado
        $sel         tour seleccionado: id, nom, dur_hrs, lambda, mu, c, k, cos_esp, cos_srv, origen_lambda
        $m           métricas del tour seleccionado (ColasService::metricas)
        $escenarios  filas c = 1..n con rho, lq, wq_hrs, p_rec, ct, optimo
        $sim         métricas del escenario simulado (c y K elegidos) o null
        $simC, $simK valores del simulador
        $historial   lista de métricas diarias: fec, lmb_obs, rho, wq_hrs, p_rec
        $formAction  URL a la que envía el formulario (GET)
--}}
@extends('layouts.admin-io')

@section('titulo', 'Análisis de colas de reservas')
@section('subtitulo', 'Teoría de colas M/M/c/K aplicada al motor de reservas · c = guías, K = cupos por salida')

@php
    $fmt = fn ($v, $d = 2) => number_format((float) $v, $d, ',', '.');
    $pct = fn ($v, $d = 1) => number_format((float) $v * 100, $d, ',', '.') . ' %';
    $badge = [
        'saturado' => ['Saturado', 'bg-red-100 text-red-700', 'fa-circle-exclamation'],
        'atencion' => ['En atención', 'bg-pipocas-acento/30 text-pipocas-calido', 'fa-triangle-exclamation'],
        'estable'  => ['Estable', 'bg-emerald-100 text-emerald-700', 'fa-circle-check'],
    ];
    $estadoSel = $m['estado'] ?? 'estable';
    $rhoPct = min(100, round(($m['rho'] ?? 0) * 100));
@endphp

@section('acciones')
    <form method="GET" action="{{ $formAction }}" class="flex flex-wrap items-center gap-2">
        <label for="tour" class="sr-only">Tour</label>
        <select id="tour" name="tour" onchange="this.form.submit()"
                class="rounded-xl border-black/10 bg-white py-2 pl-3 pr-9 text-sm font-semibold text-pipocas-principal focus:border-pipocas-principal focus:ring-pipocas-principal">
            @foreach ($tours as $t)
                <option value="{{ $t['id'] }}" @selected($t['id'] == $sel['id'])>{{ $t['nom'] }}</option>
            @endforeach
        </select>
        <span class="inline-flex items-center gap-2 rounded-xl bg-pipocas-principal px-3 py-2 text-xs font-bold text-white">
            <i class="fa-solid fa-square-root-variable"></i> M/M/{{ $sel['c'] }}/{{ $sel['k'] }}
        </span>
    </form>
@endsection

@section('contenido')

    {{-- ===================== Resumen del tour seleccionado ===================== --}}
    <section class="rsv-tarjeta">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-pipocas-texto-2">Tour seleccionado</p>
                <h2 class="font-titulo text-2xl font-semibold text-pipocas-principal">{{ $sel['nom'] }}</h2>
                <p class="mt-1 text-sm text-pipocas-texto-2">
                    Duración {{ $fmt($sel['dur_hrs'], 1) }} h ·
                    λ = {{ $fmt($sel['lambda']) }} reservas/día <span class="text-xs">({{ $sel['origen_lambda'] }})</span> ·
                    μ = 24 / {{ $fmt($sel['dur_hrs'], 1) }} = {{ $fmt($sel['mu']) }} tours/guía/día ·
                    c = {{ $sel['c'] }} guía(s) · K = {{ $sel['k'] }} cupos
                </p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-bold {{ $badge[$estadoSel][1] }}">
                <i class="fa-solid {{ $badge[$estadoSel][2] }}"></i> {{ $badge[$estadoSel][0] }}
            </span>
        </div>

        {{-- Utilización --}}
        <div class="mt-6">
            <div class="flex items-end justify-between text-sm">
                <span class="font-semibold">Utilización de los guías (ρ = λ / c·μ)</span>
                <span class="font-titulo text-2xl font-semibold text-pipocas-principal">{{ $pct($m['rho']) }}</span>
            </div>
            <div class="mt-2 h-3 overflow-hidden rounded-full bg-pipocas-fondo">
                <div class="h-full rounded-full {{ $m['rho'] >= 1 ? 'bg-red-500' : ($m['rho'] >= 0.8 ? 'bg-pipocas-acento' : 'bg-pipocas-principal') }}" style="width: {{ $rhoPct }}%"></div>
            </div>
            <div class="mt-1 flex justify-between text-[11px] text-pipocas-texto-2"><span>0 %</span><span>80 % (atención)</span><span>100 %</span></div>
        </div>

        {{-- Métricas --}}
        @php
            $kpis = [
                ['P₀', 'Sistema vacío', $pct($m['p0']), 'fa-circle'],
                ['Lq', 'Clientes en cola', $fmt($m['lq']), 'fa-people-line'],
                ['L', 'Clientes en el sistema', $fmt($m['l']), 'fa-users'],
                ['Wq', 'Espera en cola', $fmt($m['wq_hrs']) . ' h', 'fa-hourglass-half'],
                ['W', 'Tiempo en el sistema', $fmt($m['w_hrs']) . ' h', 'fa-clock'],
                ['Pq', 'Probabilidad de esperar', $pct($m['pq']), 'fa-user-clock'],
                ['P(rechazo)', 'Sistema lleno (K)', $pct($m['p_rec'], 2), 'fa-ban'],
                ['λₑ', 'Llegadas efectivas', $fmt($m['lambda_e']) . '/día', 'fa-right-to-bracket'],
            ];
        @endphp
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ($kpis as [$simbolo, $txt, $val, $ico])
                <div class="rounded-2xl border border-black/5 bg-pipocas-fondo/60 p-4">
                    <div class="flex items-center justify-between text-pipocas-texto-2">
                        <span class="text-xs font-bold">{{ $simbolo }}</span>
                        <i class="fa-solid {{ $ico }} text-xs"></i>
                    </div>
                    <p class="mt-1 font-titulo text-2xl font-semibold text-pipocas-principal">{{ $val }}</p>
                    <p class="text-xs text-pipocas-texto-2">{{ $txt }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-5">
        {{-- ===================== Distribución Pn ===================== --}}
        <section class="rsv-tarjeta xl:col-span-3">
            <h3 class="rsv-titulo">Distribución de clientes en el sistema</h3>
            <p class="mt-1 text-sm text-pipocas-texto-2">Probabilidad de estado estable Pₙ de tener n reservas en el sistema (n = 0 … K). Las barras a partir de c corresponden a clientes en espera.</p>
            <div class="mt-4 h-64"><canvas id="graficoPn" aria-label="Distribución de probabilidad Pn"></canvas></div>
        </section>

        {{-- ===================== Fórmulas ===================== --}}
        <section class="rsv-tarjeta xl:col-span-2">
            <h3 class="rsv-titulo">Cómo se calcula</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="font-semibold">Probabilidades de estado</dt>
                    <dd class="text-pipocas-texto-2">Pₙ = (rⁿ/n!)·P₀ si n ≤ c; Pₙ = rⁿ/(c!·cⁿ⁻ᶜ)·P₀ si c &lt; n ≤ K, con r = λ/μ.</dd></div>
                <div><dt class="font-semibold">Clientes en cola y en el sistema</dt>
                    <dd class="text-pipocas-texto-2">Lq = Σ (n − c)·Pₙ · L = Σ n·Pₙ</dd></div>
                <div><dt class="font-semibold">Tiempos (Little)</dt>
                    <dd class="text-pipocas-texto-2">λₑ = λ(1 − P_K) · Wq = Lq/λₑ · W = L/λₑ = Wq + 1/μ</dd></div>
                <div><dt class="font-semibold">Costo total esperado</dt>
                    <dd class="text-pipocas-texto-2">CT = Cₑ·Lq + Cₛ·(c − (L − Lq)) con Cₑ = Bs {{ $fmt($sel['cos_esp']) }}/h y Cₛ = Bs {{ $fmt($sel['cos_srv']) }}/h</dd></div>
            </dl>
            <p class="mt-4 rounded-xl bg-pipocas-fondo p-3 text-xs text-pipocas-texto-2">
                Supuestos: llegadas de Poisson, servicio exponencial y capacidad finita K. Con capacidad finita el modelo es estable aun si ρ ≥ 1: el exceso de demanda aparece como rechazos.
            </p>
        </section>
    </div>

    {{-- ===================== Simulación de escenarios ===================== --}}
    <section class="rsv-tarjeta">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h3 class="rsv-titulo">Simulación de escenarios de guías y cupos</h3>
                <p class="mt-1 text-sm text-pipocas-texto-2">Compara el costo total esperado al variar el número de guías. La simulación no modifica los parámetros guardados.</p>
            </div>
            <form method="GET" action="{{ $formAction }}" class="flex flex-wrap items-end gap-3">
                <input type="hidden" name="tour" value="{{ $sel['id'] }}">
                <label class="text-xs font-semibold">Guías (c)
                    <input type="number" name="c" min="1" max="{{ $simK }}" value="{{ $simC }}" class="mt-1 block w-24 rounded-xl border-black/10 text-sm focus:border-pipocas-principal focus:ring-pipocas-principal">
                </label>
                <label class="text-xs font-semibold">Cupos (K)
                    <input type="number" name="k" min="1" max="60" value="{{ $simK }}" class="mt-1 block w-24 rounded-xl border-black/10 text-sm focus:border-pipocas-principal focus:ring-pipocas-principal">
                </label>
                <label class="text-xs font-semibold">λ (reservas/día)
                    <input type="number" name="lambda" step="0.1" min="0" value="{{ $sel['lambda'] }}" class="mt-1 block w-28 rounded-xl border-black/10 text-sm focus:border-pipocas-principal focus:ring-pipocas-principal">
                </label>
                <button class="inline-flex items-center gap-2 rounded-xl bg-pipocas-secundario px-4 py-2.5 text-sm font-bold text-white shadow transition hover:brightness-95">
                    <i class="fa-solid fa-flask-vial"></i> Simular
                </button>
            </form>
        </div>

        @if ($sim)
            <div class="mt-5 grid gap-3 rounded-2xl border border-pipocas-secundario/30 bg-pipocas-secundario/5 p-4 text-sm sm:grid-cols-5">
                <p class="sm:col-span-5 font-semibold text-pipocas-calido"><i class="fa-solid fa-wand-magic-sparkles"></i> Escenario simulado: c = {{ $simC }}, K = {{ $simK }}, λ = {{ $fmt($sel['lambda']) }}</p>
                <p>ρ <strong class="block text-lg">{{ $pct($sim['rho']) }}</strong></p>
                <p>Lq <strong class="block text-lg">{{ $fmt($sim['lq']) }}</strong></p>
                <p>Wq <strong class="block text-lg">{{ $fmt($sim['wq_hrs']) }} h</strong></p>
                <p>P(rechazo) <strong class="block text-lg">{{ $pct($sim['p_rec'], 2) }}</strong></p>
                <p>CT <strong class="block text-lg">Bs {{ $fmt($sim['ct']) }}/h</strong></p>
            </div>
        @endif

        <div class="mt-5 grid gap-6 lg:grid-cols-5">
            <div class="overflow-x-auto lg:col-span-3">
                <table class="w-full text-left text-sm">
                    <thead class="bg-pipocas-fondo text-xs uppercase text-pipocas-texto-2">
                        <tr>
                            <th class="rounded-l-xl p-3">Guías (c)</th><th class="p-3 normal-case">ρ</th><th class="p-3">Lq</th>
                            <th class="p-3">Wq</th><th class="p-3">P(rechazo)</th><th class="rounded-r-xl p-3">CT (Bs/h)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @foreach ($escenarios as $e)
                            <tr class="{{ $e['optimo'] ? 'bg-emerald-50 font-semibold' : '' }}">
                                <td class="p-3">{{ $e['c'] }} @if ($e['c'] == $sel['c'])<span class="ml-1 rounded bg-pipocas-principal/10 px-1.5 py-0.5 text-[10px] font-bold text-pipocas-principal">actual</span>@endif</td>
                                <td class="p-3">{{ $pct($e['rho']) }}</td>
                                <td class="p-3">{{ $fmt($e['lq']) }}</td>
                                <td class="p-3">{{ $fmt($e['wq_hrs']) }} h</td>
                                <td class="p-3">{{ $pct($e['p_rec'], 2) }}</td>
                                <td class="p-3">{{ $fmt($e['ct']) }} @if ($e['optimo'])<i class="fa-solid fa-star ml-1 text-emerald-600" title="Menor costo"></i>@endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="h-64 lg:col-span-2"><canvas id="graficoCosto" aria-label="Costo total esperado por número de guías"></canvas></div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-2">
        {{-- ===================== Historial ===================== --}}
        <section class="rsv-tarjeta">
            <h3 class="rsv-titulo">Historial de métricas diarias</h3>
            <p class="mt-1 text-sm text-pipocas-texto-2">λ observado y utilización registrados por el cálculo diario (cola_metricas_diarias).</p>
            @if (count($historial))
                <div class="mt-4 h-64"><canvas id="graficoHistorial" aria-label="Historial de lambda y utilización"></canvas></div>
            @else
                <div class="mt-4 grid h-64 place-items-center rounded-2xl border border-dashed border-black/10 text-center text-sm text-pipocas-texto-2">
                    <p><i class="fa-regular fa-calendar mb-2 block text-2xl"></i>Aún no hay métricas diarias para este tour.<br>Se generan con <code class="rounded bg-pipocas-fondo px-1">php artisan io:metricas-colas</code>.</p>
                </div>
            @endif
        </section>

        {{-- ===================== Todos los tours ===================== --}}
        <section class="rsv-tarjeta">
            <h3 class="rsv-titulo">Estado de las colas por tour</h3>
            <div class="mt-4 max-h-[320px] overflow-y-auto">
                <table class="w-full whitespace-nowrap text-left text-sm">
                    <thead class="sticky top-0 bg-pipocas-fondo text-xs uppercase text-pipocas-texto-2">
                        <tr><th class="p-3">Tour</th><th class="p-3">M/M/c/K</th><th class="p-3 normal-case">ρ</th><th class="p-3">Wq</th><th class="p-3">Estado</th></tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @foreach ($tours as $t)
                            <tr class="{{ $t['id'] == $sel['id'] ? 'bg-pipocas-principal/5' : '' }}">
                                <td class="p-3"><a href="{{ $formAction }}?tour={{ $t['id'] }}" class="font-semibold text-pipocas-principal hover:underline">{{ $t['nom'] }}</a></td>
                                <td class="p-3 text-xs text-pipocas-texto-2">{{ $t['c'] }} / {{ $t['k'] }} · λ {{ $fmt($t['lambda'], 1) }}</td>
                                <td class="p-3">{{ $pct($t['rho'], 0) }}</td>
                                <td class="p-3">{{ $fmt($t['wq_hrs'], 1) }} h</td>
                                <td class="p-3"><span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-bold {{ $badge[$t['estado']][1] }}">{{ $badge[$t['estado']][0] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    const pipocas = { principal: '#1F5F8B', secundario: '#C96F4A', acento: '#F4C95D', complementario: '#457699', texto2: '#6B6F72' };
    Chart.defaults.font.family = '"Plus Jakarta Sans", system-ui, sans-serif';
    Chart.defaults.color = pipocas.texto2;

    const pn = @json(array_values($m['pn']));
    const c = {{ (int) $sel['c'] }};
    new Chart(document.getElementById('graficoPn'), {
        type: 'bar',
        data: {
            labels: pn.map((_, n) => n),
            datasets: [{ label: 'Pₙ', data: pn, borderRadius: 6,
                backgroundColor: pn.map((_, n) => n < c ? pipocas.principal : (n === pn.length - 1 ? '#dc2626' : pipocas.acento)) }]
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false },
            tooltip: { callbacks: { label: ctx => 'P' + ctx.label + ' = ' + (ctx.raw * 100).toFixed(2) + ' %' } } },
            scales: { x: { title: { display: true, text: 'n (reservas en el sistema)' } }, y: { ticks: { callback: v => (v * 100).toFixed(0) + ' %' } } } }
    });

    const esc = @json($escenarios);
    new Chart(document.getElementById('graficoCosto'), {
        type: 'line',
        data: { labels: esc.map(e => 'c = ' + e.c),
            datasets: [{ label: 'Costo total esperado (Bs/h)', data: esc.map(e => e.ct), borderColor: pipocas.secundario, backgroundColor: pipocas.secundario,
                pointRadius: esc.map(e => e.optimo ? 7 : 4), tension: .25 }] },
        options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    const hist = @json($historial);
    if (hist.length && document.getElementById('graficoHistorial')) {
        new Chart(document.getElementById('graficoHistorial'), {
            data: { labels: hist.map(h => h.fec),
                datasets: [
                    { type: 'bar', label: 'λ observado (reservas/día)', data: hist.map(h => h.lmb_obs), backgroundColor: pipocas.complementario + '99', borderRadius: 4, yAxisID: 'y' },
                    { type: 'line', label: 'ρ (utilización)', data: hist.map(h => h.rho), borderColor: pipocas.secundario, tension: .3, yAxisID: 'y1' }
                ] },
            options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } },
                scales: { y: { position: 'left', beginAtZero: true }, y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false },
                    ticks: { callback: v => (v * 100).toFixed(0) + ' %' } } } }
        });
    }
</script>
@endpush
