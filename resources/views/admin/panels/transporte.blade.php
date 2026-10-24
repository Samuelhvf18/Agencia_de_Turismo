@extends('layouts.admin')

@section('header-title', 'Gestión de Flota de Transporte')

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold text-[#343A40]">Unidades de Transporte</h3>
            <p class="text-xs text-gray-500 mt-1">Monitoreo y administración de la flota disponible para las salidas de tours.</p>
        </div>
        <button onclick="toggleModalVehiculo(false)" class="bg-[#1F5F8B] hover:bg-[#16476b] text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-sm">
            Nuevo Vehículo
        </button>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-[#6B6F72] uppercase">
                    <th class="p-4">Placa</th>
                    <th class="p-4">Marca / Modelo</th>
                    <th class="p-4">Tipo</th>
                    <th class="p-4">Capacidad Turistas</th>
                    <th class="p-4">Combustible</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-xs">
                @forelse($vehiculos ?? [] as $veh)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-bold text-[#1F5F8B] uppercase">{{ $veh->pla }}</td>
                        <td class="p-4 text-gray-700 font-semibold">{{ $veh->mar }} {{ $veh->mdl }}</td>
                        <td class="p-4 text-gray-600 font-medium capitalize">{{ $veh->tip }}</td>
                        <td class="p-4 font-bold text-gray-800">{{ $veh->cap_asi }} Asientos útiles</td>
                        <td class="p-4 text-gray-600 capitalize">{{ $veh->tip_cmb ?? 'Gasolina' }}</td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full font-bold text-[10px] 
                                {{ $veh->est == 'act' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                {{ $veh->est == 'inact' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}">
                                {{ $veh->est == 'act' ? 'DISPONIBLE'  : 'MANTENIMIENTO' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <button onclick="abrirModalEditarVehiculo({{ json_encode($veh) }})" class="text-[#1F5F8B] font-bold underline bg-transparent border-0 cursor-pointer">
                                Editar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400 text-xs">
                            No hay vehículos registrados en la flota.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="vehiculoModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
            <div class="flex justify-between items-center border-b pb-3">
                <h4 id="vehiculoModalTitulo" class="font-bold text-sm text-[#343A40]">Registrar Unidad de Transporte</h4>
                <button onclick="toggleModalVehiculo(true)" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <form id="vehiculoForm" action="{{ route('admin.transporte.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div id="vehiculoMethodField"></div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Placa</label>
                        <input type="text" id="inputPla" name="pla" required placeholder="Ej: 4050-LMN" class="w-full border border-gray-300 rounded-xl p-2.5 uppercase focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Tipo de Vehículo</label>
                        <select name="tip" id="inputTip" required onchange="fijarAsientos(this.value)" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                            <option value="">Seleccione tipo...</option>
                            <option value="Minibús">Minibús</option>
                            <option value="Vagoneta 4x4">Vagoneta 4x4</option>
                            <option value="Bus">Bus</option>
                            <option value="Automóvil">Automóvil</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Marca</label>
                        <input type="text" id="inputMar" name="mar" placeholder="Ej: Toyota" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Modelo</label>
                        <input type="text" id="inputMdl" name="mdl" placeholder="Ej: Land Cruiser 2022" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Capacidad Turistas <span class="text-[10px] text-gray-400 font-normal">(-2 Chofer/Guía)</span></label>
                        <input type="number" id="inputCap" name="cap_asi" readonly class="w-full border border-gray-200 bg-gray-100 text-gray-700 font-bold rounded-xl p-2.5 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Combustible</label>
                        <select name="tip_cmb" id="inputCmb" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                            <option value="Gasolina">Gasolina</option>
                            <option value="Diesel">Diesel</option>
                            <option value="GNV">GNV</option>
                        </select>
                    </div>
                </div>

                <div id="divEstado" class="hidden">
                    <label class="block font-semibold text-gray-600 mb-1">Estado Operativo</label>
                    <select name="est" id="inputEst" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                        <option value="act">DISPONIBLE</option>
                        <option value="inact">MANTENIMIENTO</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="toggleModalVehiculo(true)" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-[#1F5F8B] text-white rounded-xl hover:bg-[#16476b] font-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function toggleModalVehiculo(isClose) {
        const modal = document.getElementById('vehiculoModal');
        if (isClose) {
            modal.classList.add('hidden');
            document.getElementById('vehiculoForm').reset();
            document.getElementById('vehiculoForm').action = "{{ route('admin.transporte.store') }}";
            document.getElementById('vehiculoMethodField').innerHTML = '';
            document.getElementById('vehiculoModalTitulo').innerText = 'Registrar Unidad de Transporte';
            document.getElementById('divEstado').classList.add('hidden');
        } else {
            modal.classList.remove('hidden');
        }
    }

    function fijarAsientos(tipo) {
        const capInput = document.getElementById('inputCap');
        switch(tipo) {
            case 'Automóvil':
                capInput.value = 3;
                break;
            case 'Vagoneta 4x4':
                capInput.value = 5;
                break;
            case 'Minibús':
                capInput.value = 13;
                break;
            case 'Bus':
                capInput.value = 22;
                break;
            default:
                capInput.value = '';
        }
    }

    function abrirModalEditarVehiculo(veh) {
        document.getElementById('vehiculoModalTitulo').innerText = 'Editar Unidad de Transporte: ' + veh.pla;
        document.getElementById('vehiculoForm').action = "/admin/transporte/" + veh.id;
        document.getElementById('vehiculoMethodField').innerHTML = '@method("PUT")';

        document.getElementById('inputPla').value = veh.pla || '';
        document.getElementById('inputMar').value = veh.mar || '';
        document.getElementById('inputMdl').value = veh.mdl || '';
        document.getElementById('inputTip').value = veh.tip || '';
        document.getElementById('inputCap').value = veh.cap_asi || '';
        document.getElementById('inputCmb').value = veh.tip_cmb || 'Gasolina';

        const estadoInput = document.getElementById('inputEst');
        estadoInput.value = (veh.est === 'inact') ? 'inact' : 'act';

        document.getElementById('divEstado').classList.remove('hidden');
        toggleModalVehiculo(false);
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Operación exitosa!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#1F5F8B',
            timer: 3000
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validación de Transporte',
            text: "{{ $errors->first() }}",
            confirmButtonColor: '#C96F4A'
        });
    @endif
</script>
@endpush
@endsection