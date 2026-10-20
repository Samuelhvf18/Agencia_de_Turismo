@extends('layouts.admin')

@section('header-title', 'Panel de Control')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm border-l-4 border-l-[#1F5F8B]">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: #6B6F72;">Ingresos Totales (Hoy)</p>
            <h3 class="text-3xl font-extrabold mt-2" style="color: #1F5F8B;">$ 4,850.00</h3>
            <span class="text-xs mt-2 inline-block font-medium text-emerald-600">↑ +12% vs ayer</span>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm border-l-4 border-l-[#C96F4A]">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: #6B6F72;">Reservas del Día</p>
            <h3 class="text-3xl font-extrabold mt-2" style="color: #C96F4A;">38</h3>
            <span class="text-xs mt-2 inline-block font-medium" style="color: #6B6F72;">8 pendientes de pago</span>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm border-l-4 border-l-[#F4C95D]">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: #6B6F72;">Ocupación General</p>
            <h3 class="text-3xl font-extrabold mt-2 text-[#947412]">84.5%</h3>
            <span class="text-xs mt-2 inline-block font-medium text-[#6B6F72]">Flota casi al límite</span>
        </div>
    </div>

    <!-- Panel de Alertas Interactivas con SweetAlert2 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h4 class="text-base font-bold mb-4" style="color: #343A40;">🔔 Panel de Alertas del Sistema</h4>
            <div class="space-y-3">
                <div onclick="mostrarAlertaFichaMedica('Juan Pérez', '1042')" class="p-3 bg-[#F4C95D]/10 border border-[#F4C95D]/40 rounded-xl text-sm text-[#6B4F3A] cursor-pointer hover:bg-[#F4C95D]/20 transition flex justify-between items-center">
                    <span>⚠️ Alerta: Ficha médica de riesgo pendiente de validación para <strong>Juan Pérez</strong> (Reserva #1042).</span>
                    <span class="text-xs font-bold underline" style="color: #1F5F8B;">Ver detalle</span>
                </div>
                <div onclick="mostrarAlertaPago('3091', '150.00')" class="p-3 bg-[#C96F4A]/10 border border-[#C96F4A]/40 rounded-xl text-sm text-[#6B4F3A] cursor-pointer hover:bg-[#C96F4A]/20 transition flex justify-between items-center">
                    <span>⚡ Pago parcial detectado en factura #3091 por un valor de $150.00.</span>
                    <span class="text-xs font-bold underline" style="color: #1F5F8B;">Ver detalle</span>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-base font-bold mb-2" style="color: #343A40;">⚡ Acciones Directas</h4>
                <p class="text-xs mb-4" style="color: #6B6F72;">Emitir comprobantes o verificar códigos QR rápidamente.</p>
            </div>
            <div class="space-y-2">
                <button onclick="validarBoletoQR('BOL-9982')" style="background-color: #1F5F8B;" class="w-full py-2.5 hover:bg-[#174870] text-white rounded-xl font-medium text-sm transition shadow">Validar Boleto QR</button>
                <button onclick="nuevaFacturaModal()" style="background-color: #C96F4A;" class="w-full py-2.5 hover:bg-[#b55e3b] text-white rounded-xl font-medium text-sm transition shadow">Nueva Factura</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mostrarAlertaFichaMedica(pasajero, reserva) {
    Swal.fire({
        title: 'Ficha Médica de Riesgo',
        html: `<p class="text-sm">El pasajero <strong>${pasajero}</strong> registró hipertensión leve en su ficha médica para la reserva <strong>#${reserva}</strong>.</p>`,
        icon: 'warning',
        iconColor: '#C96F4A',
        showCancelButton: true,
        confirmButtonText: 'Aprobar Pasajero',
        cancelButtonText: 'Cerrar',
        confirmButtonColor: '#1F5F8B',
        cancelButtonColor: '#6B6F72',
        background: '#FFFFFF',
        color: '#343A40'
    });
}

function mostrarAlertaPago(factura, monto) {
    Swal.fire({
        title: 'Pago Parcial Detectado',
        text: `La factura #${factura} registra un abono pendiente de $${monto}.`,
        icon: 'info',
        iconColor: '#1F5F8B',
        confirmButtonText: 'Ir a Finanzas',
        confirmButtonColor: '#C96F4A',
        background: '#FFFFFF',
        color: '#343A40'
    });
}

function validarBoletoQR(codigo) {
    Swal.fire({
        title: 'Validación de QR',
        text: `Verificando boleto ${codigo}...`,
        timer: 1200,
        showConfirmButton: false,
        background: '#FFFFFF',
        color: '#343A40',
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        Swal.fire({
            title: '¡Boleto Válido!',
            text: 'El código QR pertenece a la Reserva #RES-5091 (Confirmada).',
            icon: 'success',
            iconColor: '#1F5F8B',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#1F5F8B',
            background: '#FFFFFF',
            color: '#343A40'
        });
    });
}

function nuevaFacturaModal() {
    Swal.fire({
        title: 'Emitir Nueva Factura',
        html:
            '<input id="swal-input1" class="swal2-input" placeholder="Nombre o Razón Social">' +
            '<input id="swal-input2" class="swal2-input" placeholder="NIT / CI">' +
            '<input id="swal-input3" class="swal2-input" placeholder="Monto Total ($)">',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Generar Comprobante',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#C96F4A',
        cancelButtonColor: '#6B6F72',
        background: '#FFFFFF',
        color: '#343A40',
        preConfirm: () => {
            return [
                document.getElementById('swal-input1').value,
                document.getElementById('swal-input2').value,
                document.getElementById('swal-input3').value
            ]
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Factura Generada',
                text: 'El comprobante ha sido registrado con éxito.',
                icon: 'success',
                iconColor: '#1F5F8B',
                confirmButtonColor: '#1F5F8B'
            });
        }
    });
}
</script>
@endpush
@endsection