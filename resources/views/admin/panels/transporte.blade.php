@extends('layouts.admin')

@section('header-title', 'Flota de Transporte y Bitácora')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-3">
            <div class="flex justify-between items-center">
                <span class="font-bold text-lg text-[#343A40]">Minibus Mercedes-Benz</span>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-lg">Disponible</span>
            </div>
            <p class="text-xs text-[#6B6F72]">Placa: <strong>4092-LXB</strong> | Capacidad: 15 pax</p>
            <div class="pt-3 border-t border-gray-100 flex gap-2">
                <button onclick="bitacora()" class="w-full py-2 bg-[#1F5F8B] hover:bg-[#174870] text-white text-xs font-medium rounded-xl transition">Registrar Viaje</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function bitacora() {
    Swal.fire({ title: 'Bitacora de Viaje', html: '<input placeholder="Kilometraje" class="swal2-input" type="number">', confirmButtonColor: '#1F5F8B', confirmButtonText: 'Guardar' });
}
</script>
@endpush
@endsection