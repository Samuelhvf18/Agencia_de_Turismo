@extends('layouts.personal')

@section('header-title', 'Mis tours y control de operaciones')

@section('content')
    <div class="space-y-6">
        
        <!-- Tarjeta de Bienvenida -->
        <div class="bg-gradient-to-r from-[#1F5F8B] to-[#174870] rounded-2xl p-6 text-white shadow-md flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="bg-[#F4C95D] text-[#1F5F8B] text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full">Guía Encargado de Salida</span>
                <h3 class="text-lg font-bold mt-2">¡Hola, {{ auth()->user()->name ?? auth()->user()->nom }}!</h3>
                <p class="text-xs text-white/80 mt-1">Control operativo de tu ruta: supervisa el itinerario, los pasajeros y el transporte asignado.</p>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-xl p-3 text-right">
                <p class="text-[10px] uppercase text-white/70">Fecha actual</p>
                <p class="text-sm font-bold">{{ date('d / m / Y') }}</p>
            </div>
        </div>

        <!-- Listado de Salidas Asignadas Exclusivamente a este Guía -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-mountain-sun text-[#C96F4A]"></i> Mis Expediciones Asignadas
                </h4>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-lg">Filtrado en tiempo real</span>
            </div>

            <div class="p-6">
                @php
                    $userId = auth()->id();
                    // Obtenemos el perfil de guía vinculado a este usuario
                    $perfilGuia = App\Models\PerfilGuia::where('usr_id', $userId)->first();
                    $perfilGuiaId = $perfilGuia ? $perfilGuia->id : null;

                    $misSalidas = App\Models\SalidaTour::with(['tour', 'vehiculo'])
                        ->where('prf_gui_id', $perfilGuiaId)
                        ->whereIn('est', ['programada', 'en_ruta'])
                        ->get();
                @endphp

                @if(isset($misSalidas) && count($misSalidas) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($misSalidas as $salida)
                            @php $tour = $salida->tour; $vehiculo = $salida->vehiculo; @endphp
                            <div class="border border-gray-200 rounded-2xl p-5 hover:shadow-md transition-shadow bg-gray-50/40 relative flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start gap-2 mb-3">
                                        <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-lg bg-[#1F5F8B]/10 text-[#1F5F8B]">
                                            {{ $tour->cod ?? 'S/C' }}
                                        </span>
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-600 rounded-full border border-emerald-200 uppercase">
                                            {{ str_replace('_', ' ', $salida->est) }}
                                        </span>
                                    </div>
                                    <h5 class="font-bold text-gray-900 text-base mb-1">{{ $tour->nom ?? 'Tour Operativo' }}</h5>
                                    <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $tour->des ?? 'Sin descripción registrada.' }}</p>
                                    
                                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 border-t border-gray-200/60 pt-3 mb-4">
                                        <div><i class="fa-regular fa-calendar mr-1 text-[#1F5F8B]"></i> Fecha: <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($salida->fec_sal)->format('d/m/Y') }}</span></div>
                                        <div><i class="fa-solid fa-users mr-1 text-[#1F5F8B]"></i> Capacidad: <span class="font-semibold text-gray-800">{{ $salida->cap_max ?? '0' }} Max</span></div>
                                    </div>

                                    @if($vehiculo)
                                        <div class="bg-sky-50/60 border border-sky-200/60 rounded-xl p-3 mb-4">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <span class="text-[11px] font-bold text-sky-900 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-van-shuttle text-[#1F5F8B]"></i> Flota y Características del Auto
                                                </span>
                                                <span class="text-[10px] bg-sky-200/60 text-sky-800 px-2 py-0.5 rounded-md font-bold font-mono">
                                                    Placa: {{ $vehiculo->pla }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-sky-900/80 space-y-1 bg-white px-2.5 py-2 rounded-lg border border-sky-100">
                                                <div class="flex justify-between">
                                                    <span>Vehículo: <strong>{{ $vehiculo->mar }} {{ $vehiculo->mdl }}</strong></span>
                                                    <span>Tipo: <strong>{{ $vehiculo->tip ?? 'Minibus' }}</strong></span>
                                                </div>
                                                <div class="flex justify-between border-t border-gray-100 pt-1 text-gray-500">
                                                    <span>Asientos: {{ $vehiculo->cap_asi ?? 'N/D' }}</span>
                                                    <span>Combustible: {{ $vehiculo->comb ?? 'N/D' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2">
                                    <button onclick="Swal.fire('Lista de Pasajeros', 'Aquí se gestionará la asistencia y control de pasajeros de esta salida específica.', 'info')" 
                                        class="w-full py-2.5 bg-[#1F5F8B] text-white font-bold rounded-xl text-xs hover:bg-[#174870] transition-colors flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-users-viewfinder"></i> Ver Pasajeros y Asistencia
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                            <i class="fa-solid fa-route"></i>
                        </div>
                        <h5 class="text-sm font-bold text-gray-800">No hay tours asignados</h5>
                        <p class="text-xs text-gray-500 mt-1">Actualmente no tienes salidas o rutas programadas bajo tu cargo en el sistema.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection