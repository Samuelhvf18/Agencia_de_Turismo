@extends('layouts.admin')

@section('header-title', 'Control de Operaciones y Salidas')

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold text-[#343A40]">Salidas Programadas y En Ruta</h3>
            <p class="text-xs text-gray-500 mt-1">Gestión rápida de salidas, rutas y vehículos disponibles.</p>
        </div>
        <button onclick="toggleModalSalida(false)" class="bg-[#1F5F8B] hover:bg-[#16476b] text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-sm">
            Nueva Salida
        </button>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-[#6B6F72] uppercase">
                    <th class="p-4">Tour / Ruta</th>
                    <th class="p-4">Fecha Salida</th>
                    <th class="p-4">Vehículo Asignado</th>
                    <th class="p-4">Capacidad</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-xs">
                @forelse($salidas ?? [] as $salida)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">
                            <div class="font-bold text-[#343A40]">{{ $salida->tour->nom ?? 'Tour General' }}</div>
                            <div class="text-[10px] text-gray-400 font-mono">{{ $salida->tour->cod ?? 'COD-RTA' }}</div>
                        </td>
                        <td class="p-4 text-gray-600 font-medium">
                            {{ $salida->fec_sal ? \Carbon\Carbon::parse($salida->fec_sal)->format('d/m/Y') : '' }}
                        </td>
                        <td class="p-4">
                            @if($salida->vehiculo)
                                <span class="font-bold text-[#1F5F8B] uppercase">{{ $salida->vehiculo->pla }}</span>
                                <span class="text-gray-500">({{ $salida->vehiculo->mar }} {{ $salida->vehiculo->mdl }})</span>
                            @else
                                <span class="text-gray-400 italic">No asignado</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-700 font-semibold">
                            {{ $salida->vehiculo->cap_asi ?? '0' }} asientos
                        </td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full font-bold text-[10px] 
                                {{ $salida->est == 'en_ruta' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                {{ $salida->est == 'programada' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                {{ $salida->est == 'finalizada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}">
                                {{ strtoupper(str_replace('_', ' ', $salida->est)) }}
                            </span>
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <button onclick="abrirModalEditarSalida({{ json_encode($salida) }})" class="text-[#1F5F8B] font-bold underline bg-transparent border-0 cursor-pointer">
                                Editar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400 text-xs">
                            No hay salidas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="salidaModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
            <div class="flex justify-between items-center border-b pb-3">
                <h4 id="salidaModalTitulo" class="font-bold text-sm text-[#343A40]">Programar Nueva Salida</h4>
                <button onclick="toggleModalSalida(true)" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <form id="salidaForm" action="{{ route('admin.operaciones.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div id="salidaMethodField"></div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Ruta / Tour</label>
                    <input type="text" name="nombre_tour" id="inputNombreTour" required placeholder="Ej: Tour Salar de Uyuni 3D/2N" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Vehículo Disponible</label>
                    <select name="veh_id" id="inputVehId" required class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                        <option value="">Seleccione vehículo...</option>
                        @foreach($vehiculos ?? [] as $v)
                            <option value="{{ $v->id }}">
                                {{ $v->pla }} - {{ $v->mar }} {{ $v->mdl }} (Cap: {{ $v->cap_asi }} asientos)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Fecha Salida</label>
                        <input type="date" name="fec_sal" id="inputFecSal" required class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Estado</label>
                        <select name="est" id="inputEst" required class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                            <option value="programada">Programada</option>
                            <option value="en_ruta">En Ruta</option>
                            <option value="finalizada">Finalizada</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="toggleModalSalida(true)" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-[#1F5F8B] text-white rounded-xl hover:bg-[#16476b] font-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function toggleModalSalida(isClose) {
        const modal = document.getElementById('salidaModal');
        const tourInput = document.getElementById('inputNombreTour');

        if (isClose) {
            modal.classList.add('hidden');
            document.getElementById('salidaForm').reset();
            document.getElementById('salidaForm').action = "{{ route('admin.operaciones.store') }}";
            document.getElementById('salidaMethodField').innerHTML = '';
            document.getElementById('salidaModalTitulo').innerText = 'Programar Nueva Salida';

            tourInput.readOnly = false;
            tourInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
        } else {
            modal.classList.remove('hidden');
        }
    }

    function abrirModalEditarSalida(salida) {
        const tourInput = document.getElementById('inputNombreTour');

        document.getElementById('salidaModalTitulo').innerText = 'Editar Salida';
        document.getElementById('salidaForm').action = "/admin/operaciones/" + salida.id;
        document.getElementById('salidaMethodField').innerHTML = '@method("PUT")';

        tourInput.value = salida.tour ? salida.tour.nom : 'Tour General';
        tourInput.readOnly = true;
        tourInput.classList.add('bg-gray-100', 'cursor-not-allowed');

        document.getElementById('inputVehId').value = salida.veh_id || '';
        document.getElementById('inputFecSal').value = salida.fec_sal ? salida.fec_sal.substring(0, 10) : '';
        document.getElementById('inputEst').value = salida.est || 'programada';

        toggleModalSalida(false);
    }


    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validación de Operación',
            text: "{{ $errors->first() }}",
            confirmButtonColor: '#C96F4A'
        });
    @endif
</script>
@endpush
@endsection