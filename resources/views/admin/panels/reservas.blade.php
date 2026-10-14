@extends('layouts.admin')

@section('header-title', 'Gestión de Reservas y Pasajeros')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
        <input type="text" placeholder="Buscar por pasajero, CI o reserva..." class="w-full sm:w-96 bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 text-sm text-[#343A40]">
    </div>
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                <tr>
                    <th class="p-4">Reserva / Tour</th>
                    <th class="p-4">Pasajero</th>
                    <th class="p-4">Boleto QR</th>
                    <th class="p-4 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td class="p-4 font-medium text-[#343A40]">#RES-5091<br><span class="text-xs text-[#6B6F72]">Salar de Uyuni</span></td>
                    <td class="p-4 text-[#343A40]">Maria Gomez (CI: 8392011)</td>
                    <td class="p-4"><span class="px-2.5 py-1 bg-[#1F5F8B]/10 text-[#1F5F8B] rounded-lg text-xs font-medium">Validado</span></td>
                    <td class="p-4 text-right"><button onclick="detalleReserva()" class="text-xs font-bold text-[#1F5F8B]">Ver Detalle</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function detalleReserva() { Swal.fire({ title: 'Detalle de Reserva', text: 'Ficha médica firmada y datos de contacto cargados.', icon: 'info', confirmButtonColor: '#1F5F8B' }); }
</script>
@endpush
@endsection