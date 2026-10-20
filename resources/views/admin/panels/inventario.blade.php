@extends('layouts.admin')

@section('header-title', 'Stock de Equipos & Algoritmo EOQ')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <span class="text-xs text-[#6B6F72]">Total Items</span>
            <h3 class="text-2xl font-bold text-[#1F5F8B] mt-1">1,240 Unidades</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <span class="text-xs text-[#6B6F72]">Punto de Reorden</span>
            <h3 class="text-2xl font-bold text-[#C96F4A] mt-1">4 Equipos críticos</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <span class="text-xs text-[#6B6F72]">Lote EOQ</span>
            <h3 class="text-2xl font-bold text-[#6B4F3A] mt-1">45 Unidades</h3>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h4 class="font-bold text-[#343A40]">Control de Inventario</h4>
            <button onclick="nuevoItem()" class="px-3 py-1.5 bg-[#C96F4A] hover:bg-[#b55e3b] text-white text-xs font-semibold rounded-lg transition">+ Agregar Item</button>
        </div>
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                    <th class="p-4">Equipo</th>
                    <th class="p-4">Stock</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-right">Accion</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td class="p-4 font-bold text-[#343A40]">Bicicleta Trek Marlin</td>
                    <td class="p-4 text-[#1F5F8B] font-semibold">18 uds</td>
                    <td class="p-4"><span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-xs">Optimo</span></td>
                    <td class="p-4 text-right"><button onclick="reponer()" class="text-xs text-[#1F5F8B] font-bold">Solicitar</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function nuevoItem() { Swal.fire({ title: 'Nuevo Item', html: '<input placeholder="Nombre" class="swal2-input">', confirmButtonColor: '#C96F4A' }); }
function reponer() { Swal.fire({ title: 'Orden Creada', text: 'Solicitud EOQ enviada.', icon: 'success', confirmButtonColor: '#1F5F8B' }); }
</script>
@endpush
@endsection