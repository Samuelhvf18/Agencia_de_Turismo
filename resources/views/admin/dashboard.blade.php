@extends('layouts.admin')

@section('header-title', 'Panel de control')

@section('content')
@php $bs = fn ($n) => 'Bs. ' . number_format((float) $n, 2, ',', '.'); @endphp
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm border-l-4 border-l-[#1F5F8B]">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#6B6F72]">Ingresos de hoy</p>
            <h3 class="text-2xl font-extrabold mt-2 text-[#1F5F8B]">{{ $bs($ingresosHoy) }}</h3>
            <span class="text-xs text-[#6B6F72]">Mes: {{ $bs($ingresosMes) }}</span>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm border-l-4 border-l-[#C96F4A]">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#6B6F72]">Reservas de hoy</p>
            <h3 class="text-2xl font-extrabold mt-2 text-[#C96F4A]">{{ $reservasHoy }}</h3>
            <span class="text-xs text-[#6B6F72]">{{ $pendientesPago }} pendientes de pago</span>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm border-l-4 border-l-[#F4C95D]">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#6B6F72]">Ocupación próximos 7 días</p>
            <h3 class="text-2xl font-extrabold mt-2 text-[#947412]">{{ number_format($ocupacion, 1, ',', '.') }} %</h3>
            <span class="text-xs text-[#6B6F72]">cupos vendidos / cupos programados</span>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm border-l-4 border-l-[#457699]">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#6B6F72]">Salidas de hoy</p>
            <h3 class="text-2xl font-extrabold mt-2 text-[#457699]">{{ $salidasHoy->count() }}</h3>
            <span class="text-xs text-[#6B6F72]">{{ (int) $salidasHoy->sum('pax') }} pasajeros confirmados</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h4 class="text-base font-bold mb-4 text-[#343A40]">Ingresos y reservas (últimos 30 días)</h4>
            <div class="h-64"><canvas id="ventas" aria-label="Ingresos y reservas por día"></canvas></div>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h4 class="text-base font-bold mb-4 text-[#343A40]">Alertas del sistema</h4>
            <div class="space-y-3">
                @forelse ($alertas as $a)
                    <a href="{{ $a['url'] }}" class="flex items-start gap-3 rounded-xl border border-[#F4C95D]/40 bg-[#F4C95D]/10 p-3 text-sm text-[#6B4F3A] hover:bg-[#F4C95D]/20">
                        <i class="fa-solid {{ $a['icono'] }} mt-0.5"></i><span>{{ $a['texto'] }}</span>
                    </a>
                @empty
                    <p class="text-sm text-[#6B6F72]">Todo en orden. No hay alertas pendientes.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <div class="flex items-center justify-between p-4"><h4 class="font-bold text-[#343A40]">Salidas de hoy</h4><a href="{{ route('admin.operaciones') }}" class="text-xs font-bold text-[#1F5F8B]">Ver operaciones</a></div>
        <table class="w-full min-w-[560px] text-left text-sm">
            <thead class="bg-gray-50 border-y border-gray-200 text-xs text-[#6B6F72] uppercase"><tr><th class="p-3">Hora</th><th class="p-3">Tour</th><th class="p-3">Guía</th><th class="p-3">Pasajeros</th><th class="p-3">Estado</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($salidasHoy as $s)
                    <tr><td class="p-3">{{ substr($s->hra_sal, 0, 5) }}</td><td class="p-3">{{ $s->tour->nom }}</td><td class="p-3">{{ $s->guia?->usuario?->nom ?? 'Sin asignar' }}</td><td class="p-3">{{ (int) $s->pax }} / {{ $s->cap_max }}</td><td class="p-3">{{ str_replace('_', ' ', $s->est) }}</td></tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-[#6B6F72]">No hay salidas hoy.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    const ventas = @json($ventas30);
    new Chart(document.getElementById('ventas'), {
        data: {
            labels: ventas.map(v => v.dia),
            datasets: [
                { type: 'bar', label: 'Ingresos (Bs.)', data: ventas.map(v => v.ingresos), backgroundColor: 'rgba(31,95,139,.7)', yAxisID: 'y' },
                { type: 'line', label: 'Reservas', data: ventas.map(v => v.reservas), borderColor: '#C96F4A', backgroundColor: '#C96F4A', tension: .3, yAxisID: 'y1' },
            ],
        },
        options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Bs.' } }, y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } } } },
    });
</script>
@endpush
@endsection
