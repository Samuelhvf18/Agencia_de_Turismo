@extends('layouts.admin')
@section('header-title', 'Vista general')
@section('page-description', 'Un vistazo a tu operación, tus reservas y lo que necesita atención hoy.')
@section('page-actions')<a href="{{ route('admin.operaciones') }}" class="rsv-btn">
    <i aria-hidden="true" class="fa-solid fa-plus"></i> Programar salida</a>@endsection
@section('content')
    @php $bs = fn ($n) => 'Bs. ' . number_format((float) $n, 2, ',', '.'); @endphp
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Resumen de la agencia">
            @if ($ingresosHoy !== null)
                <div class="admin-metric">
                    <div class="flex items-center justify-between">
                        <p class="admin-metric-label">Ingresos de hoy</p><span class="admin-metric-icon"><i aria-hidden="true" class="fa-solid fa-wallet"></i></span>
                    </div>
                    <p class="admin-metric-value">{{ $bs($ingresosHoy) }}</p>
                    <p class="admin-metric-note">{{ $bs($ingresosMes) }} acumulados este mes</p>
                </div>
            @endif
            <div class="admin-metric">
                <div class="flex items-center justify-between">
                    <p class="admin-metric-label">Reservas de hoy</p><span
                        class="admin-metric-icon !bg-amber-50 !text-amber-700"><i aria-hidden="true" class="fa-solid fa-ticket"></i></span>
                </div>
                <p class="admin-metric-value">{{ $reservasHoy }}</p>
                <p class="admin-metric-note">{{ $pendientesPago }} reservas pendientes de pago en total</p>
            </div>
            <div class="admin-metric">
                <div class="flex items-center justify-between">
                    <p class="admin-metric-label">Ocupación próxima</p><span
                        class="admin-metric-icon !bg-emerald-50 !text-emerald-700"><i aria-hidden="true" class="fa-solid fa-chart-simple"></i></span>
                </div>
                <p class="admin-metric-value">{{ number_format($ocupacion, 1, ',', '.') }}<span
                        class="text-lg text-slate-400"> %</span></p>
                <div class="my-2 h-1.5 rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ min(100, max(0, $ocupacion)) }}%">
                    </div>
                </div>
                <p class="admin-metric-note">Cupos reservados · próximos 7 días</p>
            </div>
            <div class="admin-metric">
                <div class="flex items-center justify-between">
                    <p class="admin-metric-label">Salidas de hoy</p><span
                        class="admin-metric-icon !bg-violet-50 !text-violet-600"><i aria-hidden="true" class="fa-solid fa-route"></i></span>
                </div>
                <p class="admin-metric-value">{{ $salidasHoy->count() }}</p>
                <p class="admin-metric-note">{{ (int) $salidasHoy->sum('pax') }} pasajeros confirmados</p>
            </div>
        </section>
        <div class="grid gap-6 xl:grid-cols-3">
            @if ($ingresosHoy !== null)
                <section class="rsv-tarjeta xl:col-span-2">
                    <div class="admin-section-head">
                        <div>
                            <h2>El pulso de tu agencia</h2>
                            <p>Ingresos y reservas de los últimos 30 días</p>
                        </div><span class="admin-pill"><i aria-hidden="true" class="fa-regular fa-calendar"></i> 30 días</span>
                    </div>
                    <div class="h-72"><canvas id="ventas" aria-label="Ingresos y reservas por día"
                            role="img"></canvas></div>
                </section>
            @else
                <section class="rsv-tarjeta xl:col-span-2">
                    <div class="admin-section-head">
                        <div>
                            <h2>Organiza tu próxima salida</h2>
                            <p>Accesos directos para coordinar el día.</p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2"><a href="{{ route('admin.operaciones') }}" class="io-tool"><i aria-hidden="true" class="fa-solid fa-calendar-check text-2xl text-pipocas-principal"></i>
                            <h2>Salidas y recursos</h2>
                            <p>Revisa horarios y confirma que cada salida tenga equipo y personal.</p><span
                                class="tool-link">Ir a operaciones <i aria-hidden="true" class="fa-solid fa-arrow-right"></i></span>
                        </a><a href="{{ route('admin.reservas') }}" class="io-tool"><i aria-hidden="true" class="fa-solid fa-ticket text-2xl text-pipocas-secundario"></i>
                            <h2>Reservas y pasajeros</h2>
                            <p>Consulta participantes, pendientes de pago y detalles de cada reserva.</p><span
                                class="tool-link">Ver reservas <i aria-hidden="true" class="fa-solid fa-arrow-right"></i></span>
                        </a></div>
                </section>
            @endif
            <section class="rsv-tarjeta">
                <div class="admin-section-head">
                    <div>
                        <h2>Alertas del sistema</h2>
                        <p>Lo que conviene resolver primero</p>
                    </div><span class="admin-pill">{{ count($alertas) }}</span>
                </div>
                <div class="space-y-3">
                    @forelse ($alertas as $a)
                        <a href="{{ $a['url'] }}"
                            class="flex items-start gap-3 rounded-xl border border-amber-100 bg-amber-50/50 p-3.5 text-xs leading-relaxed text-slate-600"><i aria-hidden="true" class="fa-solid {{ $a['icono'] }} mt-1 text-amber-600"></i><span
                                class="flex-1">{{ $a['texto'] }}</span><i aria-hidden="true" class="fa-solid fa-chevron-right mt-1 text-[9px]"></i></a>@empty<div class="admin-empty"><i aria-hidden="true" class="fa-regular fa-circle-check"></i><strong class="block text-slate-600">Todo en
                                orden</strong>
                            <p class="mt-1">No hay alertas pendientes.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
        <section class="admin-insight"><span class="insight-icon"><i aria-hidden="true" class="fa-solid fa-compass-drafting"></i></span>
            <div class="flex-1">
                <p class="admin-eyebrow">PLANIFICA CON TUS DATOS</p>
                <h2>¿Más demanda? Prepárate antes de la próxima salida.</h2>
                <p>Compara capacidad, organiza guías y revisa tu equipo con explicaciones fáciles de seguir.</p>
            </div><a href="{{ route('admin.io.index') }}" class="rsv-btn-sec self-center shrink-0">Explorar herramientas <i aria-hidden="true" class="fa-solid fa-arrow-right"></i></a>
        </section>
        <section class="rsv-tarjeta !p-0 overflow-hidden">
            <div class="admin-section-head !mb-0 p-6">
                <div>
                    <h2>Salidas de hoy</h2>
                    <p>Tu agenda operativa y sus responsables</p>
                </div><a href="{{ route('admin.operaciones') }}" class="text-xs font-bold text-pipocas-principal">Ver todas
                    <i aria-hidden="true" class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-left">
                    <thead>
                        <tr>
                            <th>Horario</th>
                            <th>Tour / experiencia</th>
                            <th>Guía responsable</th>
                            <th>Pasajeros</th>
                            <th>Estado</th>
                            <th><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salidasHoy as $s)
                            <tr>
                                <td class="font-bold">{{ substr($s->hra_sal, 0, 5) }}<span
                                        class="block text-[10px] font-normal text-slate-400">Salida</span></td>
                                <td class="font-bold">{{ $s->tour->nom }}</td>
                                <td>{{ $s->guia?->usuario?->nom ?? 'Sin asignar' }}</td>
                                <td><strong>{{ (int) $s->pax }}</strong><span class="text-slate-400"> /
                                        {{ $s->cap_max }}</span></td>
                                <td><span class="admin-pill">{{ ucfirst(str_replace('_', ' ', $s->est)) }}</span></td>
                                <td><a href="{{ route('admin.salidas.edit', $s) }}"
                                        class="admin-icon-button text-pipocas-principal"
                                        aria-label="Revisar salida de {{ $s->tour->nom }}"><i aria-hidden="true" class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                        </tr>@empty<tr>
                                <td colspan="6">
                                    <div class="admin-empty"><i aria-hidden="true" class="fa-regular fa-calendar"></i><strong>No hay salidas
                                            hoy</strong>
                                        <p class="mt-1">Revisa las próximas fechas o programa una nueva experiencia.</p>
                                        <a href="{{ route('admin.operaciones') }}"
                                            class="mt-3 inline-block font-bold text-pipocas-principal">Ver calendario de
                                            salidas →</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
@if ($ingresosHoy !== null)
    @push('scripts')
        <script>
            if (typeof Chart !== 'undefined') {
                Chart.defaults.font.family = '"Plus Jakarta Sans", system-ui, sans-serif';
                Chart.defaults.color = '#81909d';
                const ventas = @json($ventas30);
                new Chart(document.getElementById('ventas'), {
                    data: {
                        labels: ventas.map(v => v.dia),
                        datasets: [{
                                type: 'bar',
                                label: 'Ingresos (Bs.)',
                                data: ventas.map(v => v.ingresos),
                                backgroundColor: '#2f6d92',
                                borderRadius: 4,
                                maxBarThickness: 15,
                                yAxisID: 'y'
                            },
                            {
                                type: 'line',
                                label: 'Reservas',
                                data: ventas.map(v => v.reservas),
                                borderColor: '#d9a84b',
                                backgroundColor: '#d9a84b',
                                tension: .35,
                                pointRadius: 0,
                                yAxisID: 'y1'
                            },
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                align: 'start',
                                labels: {
                                    boxWidth: 8,
                                    boxHeight: 8,
                                    usePointStyle: true,
                                    padding: 24,
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxTicksLimit: 8,
                                    font: {
                                        size: 10
                                    }
                                },
                                border: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#eef2f6'
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    }
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        }
                    },
                });
            } else {
                document.getElementById('ventas').replaceWith(Object.assign(document.createElement('p'), {
                    textContent: 'No se pudo cargar el gráfico. Los totales están disponibles en las tarjetas y en Finanzas.'
                }));
            }
        </script>
    @endpush
@endif
