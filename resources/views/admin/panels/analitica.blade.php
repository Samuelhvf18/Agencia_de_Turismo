@extends('layouts.admin')

@section('header-title', 'Analítica y Reportes')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
        <div>
            <h3 class="text-sm font-bold text-[#343A40]">Métricas y Reportes de Rendimiento</h3>
            <p class="text-xs text-[#6B6F72]">Datos en tiempo real de turistas, reservas y demandas de tours.</p>
        </div>
        <a href="{{ route('admin.analitica.exportar') }}" 
           onclick="Swal.fire('Descargando...', 'Tu reporte en Excel se está descargando', 'success')"
           class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-file-excel text-sm"></i>
            <span>Exportar Padrón a Excel</span>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Total Reservas</span>
                <h4 class="text-2xl font-bold text-[#1F5F8B] mt-1">{{ $totalReservas }}</h4>
                <span class="text-[10px] text-emerald-600 font-bold">{{ $reservasConfirmadas }} confirmadas</span>
            </div>
            <div class="h-10 w-10 rounded-xl bg-blue-50 text-[#1F5F8B] flex items-center justify-center font-bold">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Turistas Atendidos</span>
                <h4 class="text-2xl font-bold text-[#C96F4A] mt-1">{{ $totalPasajeros }} Pax</h4>
                <span class="text-[10px] text-[#6B6F72]">Pasajeros registrados</span>
            </div>
            <div class="h-10 w-10 rounded-xl bg-orange-50 text-[#C96F4A] flex items-center justify-center font-bold">
                <i class="fa-solid fa-people-group"></i>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Ingresos Percibidos</span>
                <h4 class="text-2xl font-bold text-emerald-600 mt-1">Bs. {{ number_format($ingresosTotales, 2) }}</h4>
                <span class="text-[10px] text-emerald-700">Pagos completados</span>
            </div>
            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Tours en Catálogo</span>
                <h4 class="text-2xl font-bold text-[#947412] mt-1">{{ \App\Models\Tour::count() }}</h4>
                <span class="text-[10px] text-amber-700">Rutas disponibles</span>
            </div>
            <div class="h-10 w-10 rounded-xl bg-amber-50 text-[#947412] flex items-center justify-center font-bold">
                <i class="fa-solid fa-mountain-sun"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-[#343A40]">
                    <i class="fa-solid fa-fire text-amber-500 mr-1.5"></i> Tours Más Populares
                </h3>
                <span class="text-[11px] text-[#6B6F72]">Por cantidad de turistas</span>
            </div>

            <div class="space-y-3">
                @forelse($toursPopulares as $tour)
                    @php
                        $porcentaje = $totalPasajeros > 0 ? min(100, round(($tour->total_turistas / $totalPasajeros) * 100)) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-bold text-[#343A40]">{{ $tour->nom }}</span>
                            <span class="text-[#6B6F72]">{{ $tour->total_turistas }} turistas ({{ $tour->total_reservas }} reservas)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-[#1F5F8B] to-[#C96F4A] h-2.5 rounded-full" style="width: {{ max(8, $porcentaje) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-3 text-center">No hay datos de salidas ni reservas registradas aún.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-[#343A40]">
                    <i class="fa-solid fa-map-location-dot text-[#1F5F8B] mr-1.5"></i> Turistas por Departamento (Bolivia)
                </h3>
                <span class="text-[11px] text-[#6B6F72]">Origen de los pasajeros</span>
            </div>

            <div class="grid grid-cols-3 gap-2">
                @foreach($turistasPorDepto as $dep)
                    <div class="p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-center">
                        <span class="text-[10px] uppercase font-bold text-gray-500 block">{{ $dep->cod }} - {{ $dep->nom }}</span>
                        <span class="text-base font-extrabold text-[#1F5F8B]">{{ $dep->cantidad }}</span>
                        <span class="text-[9px] text-gray-400 block">pasajeros</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-bold text-[#343A40]">
                <i class="fa-solid fa-route text-[#C96F4A] mr-1.5"></i> Monitoreo de Salidas y Ocupación
            </h3>
            <span class="text-xs text-[#6B6F72]">Últimas 5 salidas registradas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                    <tr>
                        <th class="p-3">Tour / Fecha</th>
                        <th class="p-3">Hora</th>
                        <th class="p-3">Capacidad Máx.</th>
                        <th class="p-3">Turistas Inscritos</th>
                        <th class="p-3">Ocupación</th>
                        <th class="p-3">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($salidasRecientes as $salida)
                    @php
                        $cap = $salida->cap_max > 0 ? $salida->cap_max : 15;
                        $ocupados = (int) $salida->turistas_inscritos;
                        $porcOcupado = min(100, round(($ocupados / $cap) * 100));
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <td class="p-3 font-semibold text-[#343A40]">
                            {{ $salida->tour->nom ?? 'Tour no asignado' }}
                            <div class="text-[11px] text-[#6B6F72]">{{ \Carbon\Carbon::parse($salida->fec_sal)->format('d/m/Y') }}</div>
                        </td>
                        <td class="p-3 text-[#6B6F72]">{{ substr($salida->hra_sal ?? '08:00', 0, 5) }}</td>
                        <td class="p-3 font-bold">{{ $cap }} Pax</td>
                        <td class="p-3 font-semibold text-[#1F5F8B]">{{ $ocupados }} Pax</td>
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $porcOcupado }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600">{{ $porcOcupado }}%</span>
                            </div>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                {{ $salida->est === 'programada' ? 'bg-blue-100 text-blue-800' : ($salida->est === 'en_ruta' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                                {{ $salida->est }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-400">No hay salidas programadas para mostrar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
