@extends('layouts.admin')

@section('header-title', 'Operaciones & Salidas Diarias')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-base font-bold text-[#343A40]">Programacion Diaria de Tours</h3>
            <p class="text-xs text-[#6B6F72]">Asignacion de guia, chofer y vehiculos.</p>
        </div>
        <button onclick="programarSalidaModal()" class="px-4 py-2.5 bg-[#1F5F8B] hover:bg-[#174870] text-white text-xs font-semibold rounded-xl transition shadow">
            + Programar Salida
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white border-l-4 border-l-[#1F5F8B] border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <span class="px-2.5 py-1 bg-[#1F5F8B]/10 text-[#1F5F8B] text-xs font-bold rounded-lg">Salida 08:00 AM</span>
                    <h4 class="font-bold text-[#343A40] mt-2">Valle de la Luna</h4>
                </div>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">Confirmado</span>
            </div>
            <div class="text-xs space-y-1 text-[#6B6F72]">
                <p>Guia: Carlos Mamani</p>
                <p>Chofer: Jorge Quispe</p>
                <p>Pasajeros: 14 / 15</p>
            </div>
            <div class="pt-2 border-t border-gray-100 flex gap-2">
                <button onclick="verPasajeros()" class="w-full py-2 bg-[#F0F0F1] hover:bg-gray-200 text-[#343A40] text-xs font-medium rounded-xl transition">Ver Pasajeros</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function programarSalidaModal() {
    Swal.fire({
        title: 'Programar Salida',
        html: '<input placeholder="Nombre Tour" class="swal2-input"><input type="datetime-local" class="swal2-input">',
        confirmButtonColor: '#1F5F8B',
        confirmButtonText: 'Guardar'
    });
}
function verPasajeros() {
    Swal.fire({ title: 'Lista de Pasajeros', text: 'Mostrando registros de check-in.', icon: 'info', confirmButtonColor: '#1F5F8B' });
}
</script>
@endpush
@endsection