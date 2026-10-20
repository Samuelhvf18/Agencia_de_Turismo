@extends('layouts.admin')

@section('header-title', 'Catálogo de Tours y Rutas')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex justify-between items-center">
        <h3 class="text-base font-bold text-[#343A40]">Paquetes Turisticos</h3>
        <button onclick="nuevoTour()" class="px-4 py-2 bg-[#1F5F8B] text-white text-xs font-semibold rounded-xl">+ Nuevo Tour</button>
    </div>
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <h4 class="font-bold text-[#343A40]">Salar de Uyuni 3D/2N</h4>
        <p class="text-xs text-[#6B6F72] mt-1">Precio: $180.00 | Dificultad: Moderada</p>
    </div>
</div>

@push('scripts')
<script>
function nuevoTour() { Swal.fire({ title: 'Crear Tour', html: '<input placeholder="Nombre" class="swal2-input"><input placeholder="Precio" class="swal2-input">', confirmButtonColor: '#1F5F8B' }); }
</script>
@endpush
@endsection