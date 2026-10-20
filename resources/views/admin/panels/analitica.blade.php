@extends('layouts.admin')

@section('header-title', 'Analítica, demanda y BI')

@section('content')
@php $fmt = fn ($v, $d = 1) => number_format((float) $v, $d, ',', '.'); @endphp
<div class="space-y-6">
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-wrap items-end gap-3">
        <label class="text-xs text-[#6B6F72]">Desde<input type="date" name="desde" value="{{ $filtros['desde'] }}" class="mt-1 block rounded-xl border border-gray-300 px-3 py-2 text-sm"></label>
        <label class="text-xs text-[#6B6F72]">Hasta<input type="date" name="hasta" value="{{ $filtros['hasta'] }}" class="mt-1 block rounded-xl border border-gray-300 px-3 py-2 text-sm"></label>
        <button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Aplicar</button>
        <span class="text-xs text-[#6B6F72]">Fuente: vistas SQL del esquema v5 (vista_demanda_por_tour_mes, vista_turistas_por_nacionalidad, …).</span>
    </form>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h3 class="text-base font-bold text-[#343A40]">Rutas más solicitadas por mes</h3>
            <p class="text-xs text-[#6B6F72] mb-3">Reservas no canceladas por mes de salida (RF-33).</p>
            <div class="h-72"><canvas id="demanda"></canvas></div>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h3 class="text-base font-bold text-[#343A40]">Demanda real vs. proyectada</h3>
            <p class="text-xs text-[#6B6F72] mb-3">
                RF-35 · Modelo: <b>{{ $modeloProyeccion ?? 'sin proyecciones aún (ejecuta io:proyectar-demanda)' }}</b>
                @if ($proyeccionGenerada) · generado {{ \Carbon\Carbon::parse($proyeccionGenerada)->format('d/m/Y H:i') }} @endif
            </p>
            <div class="h-72"><canvas id="proyeccion"></canvas></div>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h3 class="text-base font-bold text-[#343A40]">Turistas por nacionalidad</h3>
            <p class="text-xs text-[#6B6F72] mb-3">Participantes de reservas no canceladas (RF-34).</p>
            <div class="h-72"><canvas id="nacionalidad"></canvas></div>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h3 class="text-base font-bold text-[#343A40]">Llegadas al sistema de reservas (30 días)</h3>
            <p class="text-xs text-[#6B6F72] mb-3">Solicitudes admitidas y rechazadas por falta de cupo (vista_llegadas_diarias_tour).</p>
            <div class="h-72"><canvas id="llegadas"></canvas></div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h3 class="text-base font-bold text-[#343A40] mb-3">Calificación promedio por tour</h3>
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach ($calificaciones as $c)
                    <li class="flex justify-between py-2"><span>{{ $c->tour }}</span><span class="font-semibold">{{ $c->cantidad_resenas ? $fmt($c->calificacion_promedio).' ★ ('.$c->cantidad_resenas.')' : '—' }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h3 class="text-base font-bold text-[#343A40] mb-3">Ocupación de la flota</h3>
            <ul class="divide-y divide-gray-100 text-sm">
                @forelse ($ocupacion as $o)
                    <li class="flex justify-between py-2"><span class="font-mono">{{ $o->pla }}</span><span>{{ $fmt($o->ocupacion) }} % · {{ $o->salidas }} salidas @if ($o->combustible) · Bs. {{ $fmt($o->combustible, 2) }} @endif</span></li>
                @empty
                    <li class="py-2 text-[#6B6F72]">Sin salidas con vehículo en el periodo.</li>
                @endforelse
            </ul>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h3 class="text-base font-bold text-[#343A40] mb-3">Carga horaria de guías (esta semana)</h3>
            <ul class="divide-y divide-gray-100 text-sm">
                @forelse ($cargaGuias as $g)
                    <li class="py-2"><div class="flex justify-between"><span>{{ $g->guia }}</span><span>{{ $fmt($g->horas_asignadas) }} / {{ $g->lim_hrs_sem }} h</span></div>
                        <div class="mt-1 h-1.5 rounded-full bg-gray-100"><div class="h-1.5 rounded-full bg-[#1F5F8B]" style="width: {{ min(100, round($g->horas_asignadas * 100 / max(1, $g->lim_hrs_sem))) }}%"></div></div></li>
                @empty
                    <li class="py-2 text-[#6B6F72]">Ningún guía tiene salidas esta semana.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const paleta = ['#1F5F8B', '#C96F4A', '#F4C95D', '#457699', '#6B4F3A', '#2E7D32', '#8E44AD', '#16A085'];
    const meses = @json($meses);
    const porTour = @json($porTour);
    new Chart(document.getElementById('demanda'), {
        type: 'bar',
        data: { labels: meses, datasets: Object.entries(porTour).map(([tour, datos], i) => ({ label: tour, data: datos, backgroundColor: paleta[i % paleta.length] })) },
        options: { maintainAspectRatio: false, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } } },
    });
    new Chart(document.getElementById('proyeccion'), {
        type: 'line',
        data: { labels: meses, datasets: [
            { label: 'Real', data: @json($real), borderColor: '#1F5F8B', backgroundColor: 'rgba(31,95,139,.15)', fill: true, tension: .3 },
            { label: 'Proyectada', data: @json($proyectado), borderColor: '#C96F4A', borderDash: [6, 6], spanGaps: true, tension: .3 },
        ] },
        options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
    });
    const nac = @json($nacionalidades);
    new Chart(document.getElementById('nacionalidad'), {
        type: 'doughnut',
        data: { labels: nac.map(n => n.pais), datasets: [{ data: nac.map(n => n.turistas), backgroundColor: paleta }] },
        options: { maintainAspectRatio: false, plugins: { legend: { position: 'right' } } },
    });
    const lleg = @json($llegadas);
    new Chart(document.getElementById('llegadas'), {
        type: 'bar',
        data: { labels: lleg.map(l => l.fec.slice(5)), datasets: [
            { label: 'Admitidas', data: lleg.map(l => l.admitidas), backgroundColor: '#457699' },
            { label: 'Rechazadas', data: lleg.map(l => l.rechazadas), backgroundColor: '#C96F4A' },
        ] },
        options: { maintainAspectRatio: false, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } } },
    });
</script>
@endpush
@endsection
