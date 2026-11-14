@extends('layouts.admin-io')
@section('titulo', 'Retener clientes')
@section('subtitulo', 'Entiende en qué etapa están tus clientes y encuentra oportunidades para acompañarlos mejor.')
@php
    $fmt = fn($v, $d = 1) => number_format((float) $v, $d, ',', '.');
    $pct = fn($v, $d = 1) => $fmt($v * 100, $d) . ' %';
    $porCod = collect($estados)->keyBy('cod');
    $transitorios = collect($estados)->where('es_abs', false)->values();
    $etapas = [
        'prospecto' => ['Interesados', 'Aún no tienen una reserva pagada.', 'fa-user-plus'],
        'reservado' => ['Con reserva', 'Tienen una reserva pagada para viajar.', 'fa-ticket'],
        'completado' => ['Ya viajaron', 'Han completado un primer tour.', 'fa-flag-checkered'],
        'fidelizado' => ['Clientes recurrentes', 'Han completado al menos dos tours.', 'fa-heart'],
        'perdido' => ['Sin actividad', 'Clasificados como perdidos por el modelo.', 'fa-user-clock'],
    ];
    $ult = count($proyeccion['meses']) - 1;
    $fid = $proyeccion['series']['fidelizado'][$ult] ?? 0;
    $per = $proyeccion['series']['perdido'][$ult] ?? 0;
@endphp
@section('acciones')<span class="admin-pill">
    <i aria-hidden="true" class="fa-regular fa-calendar"></i> Modelo: {{ $fecCal }}</span>@endsection
@section('contenido')
    <section class="admin-insight"><span class="insight-icon"><i aria-hidden="true" class="fa-solid fa-heart-circle-check"></i></span>
        <div><span class="admin-eyebrow">RELACIÓN CON TUS CLIENTES</span>
            <h2>{{ $totalClientes ? $totalClientes . ' clientes con seguimiento de etapa' : 'Todavía no hay clientes con seguimiento' }}
            </h2>
            <p>{{ $totalClientes ? 'Revisa a quienes tienen una reserva o ya completaron un tour: son oportunidades para resolver dudas, cuidar la experiencia y facilitar un próximo viaje.' : 'Las etapas aparecerán cuando se registren clientes y se actualice su seguimiento. Las probabilidades iniciales son referencias del modelo, no resultados de tu agencia.' }}
            </p>
        </div>
    </section>
    <section class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-5" aria-label="Etapas actuales de los clientes">
        @foreach ($estados as $e)
            <div class="admin-metric"><span class="admin-metric-icon"><i aria-hidden="true" class="fa-solid {{ $etapas[$e['cod']][2] }}"></i></span>
                <h2 class="admin-metric-label mt-4">{{ $etapas[$e['cod']][0] }}</h2>
                <p class="admin-metric-value">{{ $e['clientes'] }}</p>
                <p class="admin-metric-note">{{ $totalClientes ? $pct($e['clientes'] / $totalClientes, 0) : '0 %' }} del
                    total</p>
                <p class="mt-3 border-t border-slate-100 pt-3 text-[10px] leading-relaxed text-slate-500">
                    {{ $etapas[$e['cod']][1] }}</p>
            </div>
        @endforeach
    </section>
    <div class="grid gap-6 xl:grid-cols-3">
        <section class="rsv-tarjeta xl:col-span-2">
            <div class="admin-section-head">
                <div>
                    <h2>¿Cómo podrían evolucionar 100 clientes?</h2>
                    <p>Explora un grupo hipotético durante los próximos 12 meses.</p>
                </div><span class="admin-pill">Proyección</span>
            </div>
            <form method="GET" action="{{ $formAction }}"
                class="mb-5 flex flex-wrap items-end gap-3 rounded-xl bg-slate-50 p-4"><label for="inicio"
                    class="rsv-etiqueta !mb-0">Si hoy están en la etapa<select id="inicio" name="inicio"
                        class="rsv-campo mt-2">
                        @foreach ($transitorios as $t)
                            <option value="{{ $t['cod'] }}" @selected($t['cod'] === $proyeccion['inicio'])>{{ $etapas[$t['cod']][0] }}
                            </option>
                        @endforeach
                    </select>
                </label><button class="rsv-btn">Ver proyección <i aria-hidden="true" class="fa-solid fa-arrow-right"></i></button></form>
            <div class="h-72"><canvas id="graficoProyeccion" role="img"
                    aria-label="Evolución estimada de 100 clientes durante 12 meses"></canvas></div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl bg-emerald-50 p-4">
                    <p class="text-[10px] font-semibold text-emerald-700">Recurrentes al mes {{ $ult }}</p>
                    <p class="mt-1 text-xl font-extrabold text-emerald-900">≈ {{ $fmt($fid * 100, 0) }} de cada 100</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-[10px] font-semibold text-slate-500">Sin actividad al mes {{ $ult }}</p>
                    <p class="mt-1 text-xl font-extrabold">≈ {{ $fmt($per * 100, 0) }} de cada 100</p>
                </div>
            </div>
            <p class="mt-4 text-[10px] leading-relaxed text-slate-500">Es una estimación de grupo basada en probabilidades
                mensuales, no un pronóstico de personas concretas ni una garantía de ventas. No se están contando 100
                clientes reales.</p>
        </section>
        <section class="rsv-tarjeta">
            <div class="admin-section-head">
                <div>
                    <h2>¿Qué puedes hacer con esto?</h2>
                    <p>Acciones para cada momento del viaje</p>
                </div>
            </div>
            <div class="space-y-6">
                @foreach ([['Antes del viaje', 'Revisa reservas y pendientes. Resuelve dudas sobre horarios, equipo y punto de encuentro.'], ['Después del tour', 'Comprueba la experiencia del cliente y facilita que deje su reseña verificada.'], ['Para un próximo viaje', 'Identifica oportunidades de seguimiento con consentimiento y propone experiencias acordes a sus intereses.']] as [$titulo, $detalle])
                    <div class="admin-step"><span class="admin-step-number">{{ $loop->iteration }}</span>
                        <div>
                            <h3 class="text-xs font-bold">{{ $titulo }}</h3>
                            <p class="mt-1 text-[11px] leading-relaxed text-slate-500">{{ $detalle }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.reservas') }}" class="rsv-btn mt-6 w-full">Revisar reservas <i aria-hidden="true" class="fa-solid fa-arrow-right"></i></a>
            <p class="mt-3 text-[10px] text-slate-500">Esta pantalla analiza tendencias; no envía campañas ni mensajes.</p>
        </section>
    </div>
    <section class="rsv-tarjeta">
        <div class="admin-section-head">
            <div>
                <h2>Seguimiento según el mes en que llegaron</h2>
                <p>Porcentaje que sigue activo o es recurrente. “Mes 0” es el mes de incorporación de cada grupo.</p>
            </div>
        </div>
        @if (count($cohortes))
            @php $maxRel = collect($cohortes)->map(fn ($c) => count($c['filas']))->max(); @endphp
            <p class="mb-2 text-[10px] text-slate-500 sm:hidden">Desliza la tabla para ver todas las columnas →</p>
            <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Tabla de resultados">
                <table class="w-full text-center">
                    <thead>
                        <tr>
                            <th class="text-left">Grupo de clientes</th>
                            <th>Clientes</th>
                            @for ($r = 0; $r < $maxRel; $r++)
                                <th>Mes {{ $r }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cohortes as $co)
                            <tr>
                                <td class="text-left font-semibold">{{ $co['nom'] }}</td>
                                <td>{{ $co['tot'] }}</td>
                                @for ($r = 0; $r < $maxRel; $r++)
                                    @php $f = $co['filas'][$r] ?? null; @endphp
                                    <td>
                                        @if ($f)
                                            <span class="block rounded-lg p-2 text-pipocas-principal"
                                                style="background: rgba(31,95,139,{{ max(0.04, ($f['activos'] + $f['fidelizados']) * 0.16) }})"><strong>{{ $pct($f['activos'] + $f['fidelizados'], 0) }}</strong><small
                                                    class="block text-[9px]">{{ $pct($f['fidelizados'], 0) }}
                                                recurrentes</small></span>@else<span class="text-slate-400"
                                                aria-label="Sin datos para este mes">—</span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else<div class="admin-empty"><i aria-hidden="true" class="fa-solid fa-users-viewfinder"></i><strong>Aún no hay grupos mensuales
                    para comparar</strong>
                <p class="mt-2">Se mostrarán cuando se actualice el análisis de clientes. Por ahora puedes explorar la
                    proyección de referencia.</p>
            </div>
        @endif
    </section>
    <details class="admin-details">
        <summary>Cómo interpretar el modelo y sus límites</summary>
        <div class="technical-grid">
            <div><strong>Cadena de Markov</strong>
                <p>Usa la etapa actual y probabilidades de cambio para estimar el mes siguiente. Repite el cálculo para
                    proyectar el grupo.</p>
            </div>
            <div><strong>Etapas finales del modelo</strong>
                <p>“Fidelizado” y “perdido” no tienen regreso en esta versión matemática. Eso no significa que una persona
                    sin actividad no pueda volver a viajar.</p>
            </div>
            <div><strong>Fuente de las probabilidades</strong>
                <p>{{ $fecCal === 'matriz inicial' ? 'Se usa la matriz inicial de referencia. Hace falta historial para una calibración propia.' : 'Última versión del modelo: ' . $fecCal . '. Una calibración no garantiza que el comportamiento futuro sea igual.' }}
                </p>
            </div>
        </div>
    </details>
    <details class="admin-details">
        <summary>Ver detalle técnico: probabilidades mensuales y resultados de Markov</summary>
        <h3 class="mb-2 text-sm font-bold">Probabilidad de cambiar de etapa el próximo mes</h3>
        <p class="mb-4 text-xs text-slate-500">Lee cada fila desde la etapa actual hacia la etapa del mes siguiente. Cada
            fila suma 100 %.</p>
        <p class="mb-2 text-[10px] text-slate-500 sm:hidden">Desliza la tabla para ver todas las columnas →</p>
        <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Tabla de resultados">
            <table class="w-full min-w-[650px] text-center">
                <thead>
                    <tr>
                        <th>Etapa actual → siguiente</th>
                        @foreach ($estados as $e)
                            <th>{{ $etapas[$e['cod']][0] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($estados as $o)
                        <tr>
                            <td class="text-left font-bold">{{ $etapas[$o['cod']][0] }}</td>
                            @foreach ($estados as $d)
                                <td>{{ $pct($P[$o['cod']][$d['cod']] ?? 0) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <h3 class="mb-2 mt-6 text-sm font-bold">Resultados a largo plazo (absorción)</h3>
        <p class="mb-3 text-xs text-slate-500">El plazo es el promedio hasta cualquiera de los dos resultados finales, no
            solo hasta fidelizarse.</p>
        <p class="mb-2 text-[10px] text-slate-500 sm:hidden">Desliza la tabla para ver todas las columnas →</p>
        <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Tabla de resultados">
            <table class="w-full min-w-[540px] text-left">
                <thead>
                    <tr>
                        <th>Desde la etapa</th>
                        <th>Fidelizado</th>
                        <th>Perdido</th>
                        <th>Plazo estimado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absorcion as $a)
                        <tr>
                            <td>{{ $etapas[$a['cod']][0] }}</td>
                            <td>{{ $pct($a['p_fid']) }}</td>
                            <td>{{ $pct($a['p_per']) }}</td>
                            <td>{{ $fmt($a['meses']) }} meses</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-[11px] text-slate-500">Matriz fundamental N = (I − Q)⁻¹; probabilidades B = N × R; tiempo
            esperado t = N × 1. Proyección mensual v(n) = v(0) × Pⁿ.</p>
    </details>
@endsection
@push('scripts')
    <script>
        if (typeof Chart !== 'undefined') {
            const projection = @json($proyeccion);
            const stages = @json($estados);
            const names = @json(array_map(fn($e) => $etapas[$e['cod']][0], $estados));
            Chart.defaults.font.family = '"Plus Jakarta Sans", system-ui, sans-serif';
            new Chart(document.getElementById('graficoProyeccion'), {
                type: 'line',
                data: {
                    labels: projection.meses.map(m => 'Mes ' + m),
                    datasets: stages.map((s, i) => ({
                        label: names[i],
                        data: projection.series[s.cod].map(v => v * 100),
                        borderColor: s.col,
                        backgroundColor: s.col,
                        tension: .3,
                        pointRadius: 0,
                        borderWidth: 2
                    }))
                },
                options: {
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 7,
                                boxHeight: 7,
                                font: {
                                    size: 10
                                },
                                padding: 14
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': ' + ctx.raw.toFixed(1) + ' de cada 100'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 7,
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            min: 0,
                            max: 100,
                            grid: {
                                color: '#edf1f5'
                            },
                            title: {
                                display: true,
                                text: 'De cada 100 clientes'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('graficoProyeccion').replaceWith(Object.assign(document.createElement('p'), {
                textContent: 'No se pudo cargar el gráfico. Los resultados al mes 12 están disponibles debajo.'
            }));
        }
    </script>
@endpush
