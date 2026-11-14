@extends('layouts.personal')

@section('header-title', 'Portal del Chofer - Control de Ruta')

@section('content')
    <div class="space-y-6">
        
        <div class="bg-gradient-to-r from-[#1F5F8B] to-[#174870] rounded-2xl p-6 text-white shadow-md flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="bg-[#F4C95D] text-[#1F5F8B] text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full">Chofer Asignado</span>
                <h3 class="text-lg font-bold mt-2">¡Hola, {{ auth()->user()->name ?? auth()->user()->nom }}!</h3>
                <p class="text-xs text-white/80 mt-1">Control operativo de ruta: supervisa tu unidad de transporte asignada y el itinerario.</p>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-xl p-3 text-right">
                <p class="text-[10px] uppercase text-white/70">Fecha actual</p>
                <p class="text-sm font-bold">{{ date('d / m / Y') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-route text-[#C96F4A]"></i> Mis Expediciones Asignadas
                </h4>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-lg">Filtrado en tiempo real</span>
            </div>

            <div class="p-6">
                @php
                    $perfilChofer = App\Models\PerfilChofer::where('usr_id', auth()->id())->first();
                    $misSalidas = collect();

                    if ($perfilChofer) {
                        $misSalidas = App\Models\SalidaTour::with(['tour', 'vehiculo', 'guia.user', 'reservas'])
                            ->where(function ($query) use ($perfilChofer) {
                                $query->where('prf_cho_id', $perfilChofer->id)
                                      ->orWhereHas('vehiculo', function ($q) use ($perfilChofer) {
                                          $q->where('prf_cho_id', $perfilChofer->id);
                                      });
                            })
                            ->get();
                    }
                @endphp

                @if(isset($misSalidas) && count($misSalidas) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($misSalidas as $salida)
                            @php 
                                $tour = $salida->tour; 
                                $vehiculo = $salida->vehiculo; 
                                $guia = $salida->guia;
                            @endphp
                            <div class="border border-gray-200 rounded-2xl p-5 hover:shadow-md transition-shadow bg-gray-50/40 relative flex flex-col justify-between space-y-4">
                                
                                <div>
                                    <div class="flex justify-between items-start gap-2 mb-3">
                                        <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-lg bg-[#1F5F8B]/10 text-[#1F5F8B]">
                                            {{ $tour->cod ?? 'TOUR-01' }}
                                        </span>
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-600 rounded-full border border-emerald-200 uppercase">
                                            {{ str_replace('_', ' ', $salida->est) }}
                                        </span>
                                    </div>

                                    <h5 class="font-bold text-gray-900 text-base mb-1">{{ $tour->nom ?? 'Ruta Operativa' }}</h5>
                                    <p class="text-xs text-gray-600 mb-3">{{ $tour->des ?? 'Sin descripción registrada.' }}</p>
                                    
                                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 border-t border-b border-gray-200/60 py-3 mb-4">
                                        <div><i class="fa-regular fa-calendar mr-1 text-[#1F5F8B]"></i> Fecha: <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($salida->fec_sal)->format('d/m/Y') }}</span></div>
                                        <div><i class="fa-solid fa-users mr-1 text-[#1F5F8B]"></i> Capacidad: <span class="font-semibold text-gray-800">{{ $vehiculo->cap_asi ?? '12' }} Máx</span></div>
                                        <div class="col-span-2 mt-1"><i class="fa-solid fa-user-tie mr-1 text-[#1F5F8B]"></i> Guía a Cargo: <span class="font-semibold text-gray-800">{{ $guia?->user?->name ?? $guia?->user?->nom ?? 'Por asignar' }}</span></div>
                                    </div>

                                    @if($vehiculo)
                                        <div class="bg-sky-50/60 border border-sky-200/60 rounded-xl p-3">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-[11px] font-bold text-sky-900 flex items-center gap-1">
                                                    <i class="fa-solid fa-van-shuttle text-[#1F5F8B]"></i> Flota y Características del Auto
                                                </span>
                                                <span class="text-[10px] bg-sky-200/60 text-sky-800 px-2 py-0.5 rounded-md font-bold font-mono">
                                                    Placa: {{ $vehiculo->pla }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-sky-900/80 space-y-0.5 bg-white px-2.5 py-2 rounded-lg border border-sky-100">
                                                <div class="flex justify-between">
                                                    <span>Vehículo: <strong>{{ $vehiculo->mar }} {{ $vehiculo->mdl }}</strong></span>
                                                    <span>Tipo: <strong>{{ $vehiculo->tip }}</strong></span>
                                                </div>
                                                <div class="flex justify-between text-gray-500 pt-1 border-t border-gray-100">
                                                    <span>Asientos: {{ $vehiculo->cap_asi }}</span>
                                                    <span>Combustible: {{ $vehiculo->tip_cmb ?? 'Diesel' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800 text-center">
                                            Vehículo pendiente de asignación.
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="bg-slate-900 text-white rounded-xl p-3.5 space-y-2">
                                    <div class="flex justify-between items-center text-[11px]">
                                        <span class="font-bold flex items-center gap-1.5 text-emerald-400">
                                            <i class="fa-solid fa-satellite-dish animate-pulse"></i> Módulo GPS / Georreferenciación
                                        </span>
                                        <span class="text-[9px] bg-slate-800 px-2 py-0.5 rounded text-gray-300">Modo Pruebas U.</span>
                                    </div>
                                    <button onclick="Swal.fire('Georreferenciación GPS', 'Transmisión de coordenadas en vivo lista para conectar con tus compañeros.', 'success')" 
                                        class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-location-crosshairs"></i> Iniciar Transmisión GPS
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                            <i class="fa-solid fa-road"></i>
                        </div>
                        <h5 class="text-sm font-bold text-gray-800">No tienes expediciones de transporte asignadas</h5>
                        <p class="text-xs text-gray-500 mt-1">Actualmente no estás asignado como chofer en ninguna salida activa.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection