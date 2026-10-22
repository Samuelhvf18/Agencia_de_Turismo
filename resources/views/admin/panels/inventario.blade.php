@extends('layouts.admin')

@section('header-title', 'Control de Inventario y Equipos')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm border-l-4 border-l-[#1F5F8B]">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#6B6F72]">Total Unidades en Stock</p>
            <h3 class="text-2xl font-extrabold mt-2 text-[#1F5F8B]">{{ $items->sum('cnt_tot') ?? 0 }} Unidades</h3>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm border-l-4 border-l-emerald-600">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#6B6F72]">Unidades Disponibles</p>
            <h3 class="text-2xl font-extrabold mt-2 text-emerald-700">{{ $items->sum('cnt_dis') ?? 0 }} Unidades</h3>
        </div>
    </div>

    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm flex justify-between items-center">
        <div>
            <h3 class="text-base font-bold text-[#343A40]">Inventario de Equipos</h3>
            <p class="text-xs text-gray-500 mt-1">Gestión de stock, tallas y condiciones del equipamiento para los tours.</p>
        </div>
        <button onclick="toggleModal(false)" class="bg-[#1F5F8B] hover:bg-[#16476b] text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-sm">
            Nuevo Ítem / Equipo
        </button>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-[#6B6F72] uppercase">
                    <th class="p-4">Equipo</th>
                    <th class="p-4">Talla</th>
                    <th class="p-4">Stock Total</th>
                    <th class="p-4">Disponible</th>
                    <th class="p-4">Condición</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-xs">
                @forelse($items ?? [] as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-bold text-[#343A40]">
                            {{ $item->tipoEquipo->nom ?? 'Equipo de Montaña' }}
                        </td>
                        <td class="p-4 text-gray-600 font-semibold">{{ $item->tal }}</td>
                        <td class="p-4 text-gray-600">{{ $item->cnt_tot }} uds</td>
                        <td class="p-4 text-emerald-600 font-bold">{{ $item->cnt_dis }} uds</td>
                        <td class="p-4">
                            <form action="{{ route('admin.inventario.update', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                
                                @php
                                    $siguienteCon = match($item->con) {
                                        'bueno' => 'regular',
                                        'regular' => 'malo',
                                        default => 'bueno',
                                    };
                                @endphp
                                <input type="hidden" name="con" value="{{ $siguienteCon }}">
                                <input type="hidden" name="nom_equipo" value="{{ $item->tipoEquipo->nom ?? 'Equipo' }}">
                                <input type="hidden" name="tal" value="{{ $item->tal }}">
                                <input type="hidden" name="cnt_tot" value="{{ $item->cnt_tot }}">
                                <input type="hidden" name="cnt_dis" value="{{ $item->cnt_dis }}">

                                <button type="submit" title="Haz clic para cambiar condición" class="px-3 py-1 rounded-full font-bold text-xs transition cursor-pointer shadow-sm
                                    {{ $item->con == 'bueno' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : '' }}
                                    {{ $item->con == 'regular' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : '' }}
                                    {{ $item->con == 'malo' ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' : '' }}">
                                    ● {{ strtoupper($item->con) }}
                                </button>
                            </form>
                        </td>
                        <td class="p-4 text-center">
                            <button onclick="abrirModalEditar({{ json_encode($item) }})" class="text-[#1F5F8B] font-bold underline bg-transparent border-0 cursor-pointer">
                                Editar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400 text-xs">
                            No hay registros de inventario en la base de datos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="itemModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
            <div class="flex justify-between items-center border-b pb-3">
                <h4 id="modalTitulo" class="font-bold text-sm text-[#343A40]">Registrar Nuevo Ítem de Inventario</h4>
                <button onclick="toggleModal(true)" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <form id="itemForm" action="{{ route('admin.inventario.store') }}" method="POST" onsubmit="return validarStock()" class="space-y-4 text-xs">
                @csrf
                <div id="methodField"></div>

                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Nombre del Equipo</label>
                    <input type="text" id="inputNombreEquipo" name="nom_equipo" required placeholder="Ej: Bolsa de Dormir, Carpa Térmica..." class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Talla</label>
                        <select name="tal" id="inputTal" required class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L" selected>L</option>
                            <option value="XL">XL</option>
                            <option value="XS">XS</option>
                            <option value="XXL">XXL</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Condición</label>
                        <select name="con" id="inputCon" required class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                            <option value="bueno">Bueno</option>
                            <option value="regular">Regular</option>
                            <option value="malo">Malo</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Stock Total</label>
                        <input type="number" id="inputTot" name="cnt_tot" min="0" required oninput="validarStockRealtime()" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Stock Disponible</label>
                        <input type="number" id="inputDis" name="cnt_dis" min="0" required oninput="validarStockRealtime()" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                    </div>
                </div>
                
                <p id="errorStock" class="text-rose-600 font-bold hidden">El stock disponible no puede superar el stock total.</p>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="toggleModal(true)" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" id="btnGuardar" class="px-4 py-2 bg-[#1F5F8B] text-white rounded-xl hover:bg-[#16476b] font-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function toggleModal(isClose) {
        const modal = document.getElementById('itemModal');
        if (isClose) {
            modal.classList.add('hidden');
            document.getElementById('itemForm').reset();
            document.getElementById('itemForm').action = "{{ route('admin.inventario.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('modalTitulo').innerText = 'Registrar Nuevo Ítem de Inventario';
            document.getElementById('errorStock').classList.add('hidden');
            document.getElementById('btnGuardar').disabled = false;
        } else {
            modal.classList.remove('hidden');
        }
    }

    function abrirModalEditar(item) {
        document.getElementById('modalTitulo').innerText = 'Editar Ítem de Inventario';
        document.getElementById('itemForm').action = "/admin/inventario/" + item.id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';

        document.getElementById('inputNombreEquipo').value = item.tipo_equipo ? item.tipo_equipo.nom : '';
        document.getElementById('inputTal').value = item.tal || 'L';
        document.getElementById('inputTot').value = item.cnt_tot || 0;
        document.getElementById('inputDis').value = item.cnt_dis || 0;
        document.getElementById('inputCon').value = item.con || 'bueno';

        toggleModal(false);
    }

    function validarStockRealtime() {
        const tot = parseInt(document.getElementById('inputTot').value) || 0;
        const dis = parseInt(document.getElementById('inputDis').value) || 0;
        const errorMsg = document.getElementById('errorStock');
        const btn = document.getElementById('btnGuardar');

        if (dis > tot) {
            errorMsg.classList.remove('hidden');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            errorMsg.classList.add('hidden');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function validarStock() {
        const tot = parseInt(document.getElementById('inputTot').value) || 0;
        const dis = parseInt(document.getElementById('inputDis').value) || 0;
        if (dis > tot) {
            alert('El stock disponible no puede superar el stock total.');
            return false;
        }
        return true;
    }
</script>
@endsection