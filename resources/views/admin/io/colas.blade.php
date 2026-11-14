@extends('layouts.admin-io')
@section('titulo', 'Demanda y capacidad')
@section('subtitulo', 'Anticipa la presión sobre tus tours y compara cuántos guías podrías necesitar.')
@php
    $fmt = fn($v, $d = 1) => number_format((float) $v, $d, ',', '.');
    $pct = fn($v, $d = 1) => number_format((float) $v * 100, $d, ',', '.') . ' %';
    $tiempo = fn($h) => $h < 1 ? $fmt($h * 60, 0) . ' min' : $fmt($h) . ' h';
    $estado = $m['estado'];
    $etiquetas = [
        'saturado' => 'Revisar capacidad',
        'atencion' => 'Vigilar demanda',
        'estable' => 'Sin saturación estimada',
    ];
    $mejor = collect($escenarios)->firstWhere('optimo', true);
    $sinLlegadas = $sel['lambda'] == 0;
@endphp
@section('acciones')
    <form method="GET" action="{{ $formAction }}" class="flex items-center gap-2"><label for="tour" class="sr-only">Tour
            a analizar</label><select name="tour" id="tour" class="max-w-full rounded-xl border-slate-200 text-xs"
            onchange="this.form.submit()">
            @foreach ($tours as $t)
                <option value="{{ $t['id'] }}" @selected($t['id'] == $sel['id'])>{{ $t['nom'] }}</option>
            @endforeach
        </select>
        <button class="rsv-btn-sec !p-2" aria-label="Consultar tour"><i aria-hidden="true" class="fa-solid fa-arrow-right"></i></button>
    </form>
@endsection
@section('contenido')
    <section class="admin-insight {{ $estado === 'saturado' && !$sinLlegadas ? '!bg-amber-50 !border-amber-200' : '' }}">
        <span class="insight-icon"><i aria-hidden="true" class="fa-solid {{ $sinLlegadas ? 'fa-magnifying-glass-chart' : ($estado === 'estable' ? 'fa-circle-check' : 'fa-triangle-exclamation') }}"></i></span>
        <div class="flex-1"><span class="admin-eyebrow">LECTURA RÁPIDA · {{ $sel['nom'] }}</span>
            <h2>{{ $sinLlegadas ? 'Sin llegadas registradas en el período' : $etiquetas[$estado] }}</h2>
            <p>
                @if ($sinLlegadas)
                    Con demanda observada de cero, el modelo no estima esperas. Esto no garantiza que tus próximas salidas
                    estén cubiertas: prueba una demanda mayor en el simulador.
                @elseif ($estado === 'saturado')
                    El modelo estima presión elevada o más de 5 % de solicitudes sin atención. Compara más guías o una
                    capacidad distinta y revisa la disponibilidad antes de asignar recursos.
                @elseif ($estado === 'atencion')
                    La demanda se acerca a la capacidad de atención. Comprueba horarios y prueba alternativas para
                    prepararte ante un aumento de reservas.
                @else
                    El modelo no detecta saturación con estos valores. Revisa también la espera estimada: tener capacidad no
                    garantiza un tiempo de atención aceptable para tus clientes.
                @endif
            </p>
            <p class="mt-3 !text-[10px]">Estimación del modelo, no medición de personas esperando. Las reservas grupales y
                la duración real pueden cambiar el resultado.</p>
        </div>
    </section>
    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4" aria-label="Situación actual">
        @foreach ([['Solicitudes por día', $fmt($sel['lambda']), $sel['origen_lambda'], 'fa-ticket'], ['Guías del modelo', $sel['c'], 'Configurados para este análisis', 'fa-user-group'], ['Espera estimada', $tiempo($m['wq_hrs']), 'Promedio antes de recibir atención', 'fa-hourglass-half'], ['Riesgo de rechazo', $pct($m['p_rec']), 'Probabilidad de encontrar el sistema lleno', 'fa-circle-exclamation']] as [$titulo, $valor, $nota, $icono])
            <div class="admin-metric">
                <div class="flex items-start justify-between gap-2">
                    <p class="admin-metric-label">{{ $titulo }}</p><span class="admin-metric-icon"><i aria-hidden="true" class="fa-solid {{ $icono }}"></i></span>
                </div>
                <p class="admin-metric-value">{{ $valor }}</p>
                <p class="admin-metric-note">{{ $nota }}</p>
            </div>
        @endforeach
    </section>
    <section class="rsv-tarjeta" id="simulador">
        <div class="admin-section-head">
            <div>
                <h2>¿Qué pasaría si cambias tu capacidad?</h2>
                <p>Prueba una alternativa. Tu configuración y tus asignaciones se mantienen intactas.</p>
            </div><span class="admin-pill"><i aria-hidden="true" class="fa-solid fa-flask"></i> Simulador</span>
        </div>
        <form method="GET" action="{{ $formAction }}"
            class="grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2 xl:grid-cols-4">
            <input type="hidden" name="tour" value="{{ $sel['id'] }}">
            <label class="rsv-etiqueta !mb-0">Guías disponibles<input name="c" type="number" min="1"
                    max="60" required value="{{ $simC }}" class="rsv-campo mt-2"><span
                    class="mt-1 block text-[10px] font-normal text-slate-500">Personas que atenderían el
                    tour.</span></label>
            <label class="rsv-etiqueta !mb-0">Capacidad del modelo<input name="k" type="number" min="1"
                    max="200" required value="{{ $simK }}" class="rsv-campo mt-2"><span
                    class="mt-1 block text-[10px] font-normal text-slate-500">Límite del sistema; debe ser ≥
                    guías.</span></label>
            <label class="rsv-etiqueta !mb-0">Solicitudes esperadas por día<input name="lambda" type="number"
                    step="0.0001" min="0" max="500" required value="{{ $simLambda }}"
                    class="rsv-campo mt-2"><span class="mt-1 block text-[10px] font-normal text-slate-500">Prueba también un
                    día de alta demanda.</span></label>
            <div class="flex flex-col justify-center gap-2"><button class="rsv-btn"><i aria-hidden="true" class="fa-solid fa-play"></i>
                    Comparar escenario</button>
                @if ($sim)
                    <a href="{{ $formAction }}?tour={{ $sel['id'] }}"
                        class="text-center text-[11px] font-semibold text-slate-500">Restablecer valores actuales</a>
                @endif
            </div>
        </form>
        @if ($sim)
            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[530px] text-left">
                    <thead>
                        <tr>
                            <th>Qué comparamos</th>
                            <th>Situación actual</th>
                            <th>Tu escenario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Guías / capacidad</td>
                            <td>{{ $sel['c'] }} / {{ $sel['k'] }}</td>
                            <td class="font-bold text-pipocas-principal">{{ $simC }} / {{ $simK }}</td>
                        </tr>
                        <tr>
                            <td>Solicitudes por día</td>
                            <td>{{ $fmt($sel['lambda']) }}</td>
                            <td>{{ $fmt($simLambda) }}</td>
                        </tr>
                        <tr>
                            <td>Espera estimada</td>
                            <td>{{ $tiempo($m['wq_hrs']) }}</td>
                            <td class="font-bold">{{ $tiempo($sim['wq_hrs']) }} <span
                                    class="ml-1 text-[10px] font-normal text-slate-500">{{ $sim['wq_hrs'] < $m['wq_hrs'] ? '↓ Menor espera' : ($sim['wq_hrs'] > $m['wq_hrs'] ? '↑ Mayor espera' : 'Sin cambio') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Riesgo de rechazo</td>
                            <td>{{ $pct($m['p_rec']) }}</td>
                            <td class="font-bold">{{ $pct($sim['p_rec']) }}</td>
                        </tr>
                        <tr>
                            <td>Costo estimado del modelo</td>
                            <td>Bs {{ $fmt($costoActual, 2) }}/h</td>
                            <td>Bs {{ $fmt($sim['ct'], 2) }}/h</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-4 text-xs text-slate-500"><i aria-hidden="true" class="fa-solid fa-circle-info mr-1 text-pipocas-principal"></i> Estos
                cambios son hipotéticos. Para organizar salidas reales, revisa las propuestas de asignación y la
                disponibilidad de personal.</p>
        @else<p class="mt-4 text-xs text-slate-500">Empieza con una pregunta sencilla: si llega más gente, ¿mantener los
                mismos guías sería suficiente?</p>
        @endif
    </section>
    <div class="grid gap-6 xl:grid-cols-5">
        <section class="rsv-tarjeta xl:col-span-3">
            <div class="admin-section-head">
                <div>
                    <h2>Compara distintas cantidades de guías</h2>
                    <p>La opción destacada tiene el menor costo entre las alternativas mostradas, no necesariamente la menor
                        espera.</p>
                </div>
            </div>
            <p class="mb-2 text-[10px] text-slate-500 sm:hidden">Desliza la tabla para ver todas las columnas →</p>
            <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Tabla de resultados">
                <table class="w-full min-w-[490px] text-left">
                    <thead>
                        <tr>
                            <th>Guías</th>
                            <th>Espera estimada</th>
                            <th>Riesgo de rechazo</th>
                            <th>Costo estimado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($escenarios as $e)
                            <tr class="{{ $e['optimo'] ? 'bg-emerald-50/70' : '' }}">
                                <td class="font-bold">{{ $e['c'] }} @if ($e['optimo'])
                                        <span class="ml-1 text-[9px] font-semibold text-emerald-700">Menor costo</span>
                                    @endif
                                </td>
                                <td>{{ $tiempo($e['wq_hrs']) }}</td>
                                <td>{{ $pct($e['p_rec']) }}</td>
                                <td>Bs {{ $fmt($e['ct'], 2) }}/h</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-3 text-[10px] text-slate-500">Comparamos de 1 a {{ count($escenarios) }} guías con
                {{ $simK }} de capacidad y {{ $fmt($simLambda) }} solicitudes/día. Costos de espera y personal
                ocioso configurados; no representan una cotización.</p>
        </section>
        <section class="rsv-tarjeta xl:col-span-2">
            <div class="admin-section-head">
                <div>
                    <h2>Tu siguiente paso</h2>
                    <p>Del escenario a una decisión revisada</p>
                </div>
            </div>
            <div class="space-y-5">
                @foreach ([['Confirma los datos', 'La demanda actual usa ' . $sel['origen_lambda'] . '. La atención por guía está configurada en ' . $fmt($sel['mu']) . ' tours/día.'], ['Valida la alternativa', 'Contrasta la espera y el costo con tus horarios, la seguridad del tour y los guías realmente disponibles.'], ['Organiza tus recursos', 'Genera propuestas de asignación y revisa cada salida antes de confirmarla.']] as [$titulo, $detalle])
                    <div class="admin-step"><span class="admin-step-number">{{ $loop->iteration }}</span>
                        <div>
                            <h3 class="text-xs font-bold">{{ $titulo }}</h3>
                            <p class="mt-1 text-[11px] leading-relaxed text-slate-500">{{ $detalle }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.io.propuestas') }}" class="rsv-btn mt-6 w-full">Revisar asignaciones <i aria-hidden="true" class="fa-solid fa-arrow-right"></i></a>
        </section>
    </div>
    <section class="rsv-tarjeta">
        <div class="admin-section-head">
            <div>
                <h2>¿Qué tours conviene revisar?</h2>
                <p>La misma lectura para todos los tours con configuración.</p>
            </div>
        </div>
        <p class="mb-2 text-[10px] text-slate-500 sm:hidden">Desliza la tabla para ver todas las columnas →</p>
        <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Tabla de resultados">
            <table class="w-full min-w-[570px] text-left">
                <thead>
                    <tr>
                        <th>Tour</th>
                        <th>Guías / capacidad</th>
                        <th>Solicitudes/día</th>
                        <th>Espera estimada</th>
                        <th>Lectura</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tours as $t)
                        <tr>
                            <td><a href="{{ $formAction }}?tour={{ $t['id'] }}"
                                    class="font-bold text-pipocas-principal">{{ $t['nom'] }} <i aria-hidden="true" class="fa-solid fa-arrow-up-right-from-square ml-1 text-[9px]"></i></a></td>
                            <td>{{ $t['c'] }} / {{ $t['k'] }}</td>
                            <td>{{ $fmt($t['lambda']) }}</td>
                            <td>{{ $tiempo($t['wq_hrs']) }}</td>
                            <td><span
                                    class="admin-pill {{ $t['estado'] === 'saturado' ? '!bg-amber-50 !text-amber-800' : '' }}">{{ $t['lambda'] == 0 ? 'Sin llegadas' : $etiquetas[$t['estado']] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <details class="admin-details">
        <summary>Ver evolución, fórmulas y supuestos del análisis</summary>
        <div class="technical-grid mb-6">
            <div><strong>Demanda (λ)</strong>
                <p>{{ $fmt($sel['lambda']) }} solicitudes por día · {{ $sel['origen_lambda'] }}. Una solicitud puede
                    contener varios pasajeros.</p>
            </div>
            <div><strong>Atención (μ), guías (c), capacidad (K)</strong>
                <p>{{ $fmt($sel['mu']) }} tours por guía/día; {{ $sel['c'] }} guías y {{ $sel['k'] }} de
                    capacidad. Son parámetros del modelo, no un conteo en vivo.</p>
            </div>
            <div><strong>Presión de demanda (ρ)</strong>
                <p>{{ $pct($m['rho']) }} = λ / (c × μ). Puede superar 100 %; no es la ocupación real de los guías.</p>
            </div>
            <div><strong>Teoría de colas M/M/c/K</strong>
                <p>Supone llegadas aleatorias, tiempos de servicio exponenciales y capacidad finita. Wq = 24 × Lq / λ
                    efectiva, expresado en horas.</p>
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <h3 class="text-xs font-bold mb-2">Probabilidad de solicitudes dentro del sistema</h3>
                <div class="h-56"><canvas id="graficoPn" aria-label="Distribución de solicitudes"
                        role="img"></canvas></div>
            </div>
            <div>
                <h3 class="text-xs font-bold mb-2">Evolución registrada · últimos 30 días</h3>
                @if (count($historial))
                    <div class="h-56"><canvas id="graficoHistorial" aria-label="Demanda diaria observada"
                        role="img"></canvas></div>@else<div class="admin-empty">Aún no hay historial de cálculos
                        para este tour. Se mostrará cuando se ejecute el proceso diario.</div>
                @endif
            </div>
        </div>
        <p class="mt-5 text-[11px] text-slate-500">El costo compara espera y guías ociosos: Costo = costo de espera ×
            solicitudes en cola + costo de personal ocioso × guías libres. La comparación no incluye todos los costos reales
            de la agencia.</p>
    </details>
@endsection
@push('scripts')
    <script>
        if (typeof Chart !== 'undefined') {
            const pn = @json(array_values($m['pn']));
            new Chart(document.getElementById('graficoPn'), {
                type: 'bar',
                data: {
                    labels: pn.map((_, n) => n),
                    datasets: [{
                        label: 'Probabilidad',
                        data: pn,
                        backgroundColor: '#407b9f',
                        borderRadius: 4
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => (v * 100).toFixed(0) + ' %'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Solicitudes en el sistema'
                            }
                        }
                    }
                }
            });
            const hist = @json($historial);
            if (hist.length) new Chart(document.getElementById('graficoHistorial'), {
                type: 'line',
                data: {
                    labels: hist.map(h => h.fec),
                    datasets: [{
                        label: 'Solicitudes por día',
                        data: hist.map(h => h.lmb_obs),
                        borderColor: '#1f5f8b',
                        tension: .3,
                        pointRadius: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } else {
            document.querySelectorAll('canvas').forEach(c => c.replaceWith(Object.assign(document.createElement('p'), {
                textContent: 'El gráfico no está disponible. Consulta los resultados en las tarjetas y tablas.'
            })));
        }
    </script>
@endpush
