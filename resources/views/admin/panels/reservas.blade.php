@extends('layouts.admin')

@section('header-title', 'Gestión de Reservas y Pasajeros')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-4 rounded-2xl shadow-sm border flex flex-col sm:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('admin.reservas') }}" class="w-full sm:w-96">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Buscar por pasajero, CI o reserva..." 
                   class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
        </form>
        <button onclick="modalNuevaReserva()" class="w-full sm:w-auto px-4 py-2 bg-[#1F5F8B] hover:bg-[#184B6E] text-white rounded-xl text-sm font-semibold transition shadow-sm">
            + Nueva Reserva
        </button>
    </div>

    <div class="bg-white border rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b text-xs text-gray-500 uppercase">
                <tr>
                    <th class="p-4">Reserva / Tour</th>
                    <th class="p-4">Contacto Principal</th>
                    <th class="p-4">Pasajeros</th>
                    <th class="p-4">Estado / Pago</th>
                    <th class="p-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reservas as $reserva)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-medium text-gray-800">
                        <span class="font-bold text-[#1F5F8B]">#{{ $reserva->cod }}</span><br>
                        <span class="text-xs text-gray-500">{{ $reserva->salidaTour->tour->nom ?? 'Sin Tour' }}</span>
                    </td>
                    <td class="p-4 text-gray-800">
                        {{ $reserva->nom_ctc }}<br>
                        <span class="text-xs text-gray-500">{{ $reserva->tel_ctc }}</span>
                    </td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 bg-gray-100 border rounded-lg text-xs font-semibold text-gray-700">
                            {{ $reserva->cnt_ptc }} Pax
                        </span>
                    </td>
                    <td class="p-4 space-y-1">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium inline-block
                            {{ $reserva->est === 'confirmada' ? 'bg-green-100 text-green-700' : ($reserva->est === 'cancelada' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($reserva->est) }}
                        </span>
                        <div class="text-xs text-gray-500">Bs. {{ number_format($reserva->pre_tot, 2) }}</div>
                    </td>
                    <td class="p-4 text-right space-x-2">
                        <button onclick="verDetalle({{ $reserva->id }})" class="text-xs font-bold text-[#1F5F8B] hover:underline">Ver Detalle</button>
                        @if($reserva->est !== 'cancelada')
                            <button onclick="cancelarReserva({{ $reserva->id }})" class="text-xs font-bold text-red-600 hover:underline">Cancelar</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">No se encontraron reservas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t">{{ $reservas->links() }}</div>
    </div>
</div>

<div id="modalReserva" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-2xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto border border-gray-100">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-base font-bold text-gray-800">Nueva Reserva</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
        </div>

        <form id="formReserva" onsubmit="guardarReserva(event)" class="space-y-4 text-xs">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Salida / Tour *</label>
                <select id="sal_tur_id" required class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                    <option value="">Cargando salidas...</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Contacto *</label>
                    <input type="text" id="nom_ctc" required placeholder="Nombre Completo" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Correo *</label>
                    <input type="email" id="cor_ctc" required placeholder="correo@ejemplo.com" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Teléfono *</label>
                    <input type="text" id="tel_ctc" required placeholder="70000000" class="w-full border border-gray-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none">
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="flex justify-between items-center">
                <h4 class="font-bold text-gray-700 uppercase">Pasajeros</h4>
                <button type="button" onclick="agregarPasajero()" class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded-lg font-bold text-gray-700 transition">+ Pasajero</button>
            </div>

            <div id="contenedorPasajeros" class="space-y-2 max-h-48 overflow-y-auto pr-1"></div>

            <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-xl font-semibold hover:bg-gray-50 text-gray-700 transition">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-[#1F5F8B] text-white rounded-xl font-semibold hover:bg-[#184B6E] transition">Guardar Reserva</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
let deptosData = [];

const swalTailwindConfig = {
    buttonsStyling: false,
    customClass: {
        popup: 'rounded-2xl p-6 shadow-xl border border-gray-100',
        title: 'text-base font-bold text-gray-800',
        confirmButton: 'px-4 py-2 bg-[#1F5F8B] hover:bg-[#184B6E] text-white text-xs font-semibold rounded-xl transition mx-1',
        cancelButton: 'px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition mx-1',
        denyButton: 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl transition mx-1',
        validationMessage: 'bg-red-50 text-red-600 text-xs p-2 rounded-lg mt-2 border border-red-200'
    }
};

function verDetalle(id) {
    fetch(`/admin/reservas/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(data => {
            const paxList = (data.participantes || []).map(p => `
                <div class="py-2 border-b flex justify-between items-center text-xs">
                    <div>
                        <b>${p.nom_cpl}</b> ${p.es_ctc_pri ? '<span class="text-blue-600 font-semibold">(Contacto)</span>' : ''}<br>
                        <span class="text-gray-500">${p.tip_doc || 'Doc'}: ${p.num_doc || 'S/N'}</span>
                    </div>
                    <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-[10px] font-mono font-bold">${p.boleto?.cod || 'Sin Ticket'}</span>
                </div>
            `).join('');

            Swal.fire({
                ...swalTailwindConfig,
                title: `Reserva #${data.cod}`,
                html: `
                    <div class="text-left text-xs space-y-2 mt-2">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 space-y-1">
                            <p><b>Tour:</b> ${data.salida_tour?.tour?.nom || 'N/A'}</p>
                            <p><b>Contacto:</b> ${data.nom_ctc} (${data.tel_ctc})</p>
                            <p><b>Total:</b> <span class="text-[#1F5F8B] font-bold">Bs. ${Number(data.pre_tot || 0).toFixed(2)}</span></p>
                        </div>
                        <h4 class="font-bold uppercase text-gray-600 mt-3">Pasajeros (${data.participantes?.length || 0})</h4>
                        <div class="max-h-40 overflow-y-auto">${paxList || 'Sin pasajeros'}</div>
                    </div>
                `,
                confirmButtonText: 'Cerrar'
            });
        });
}

function modalNuevaReserva() {
    fetch('{{ route("admin.reservas.create-data") }}', { headers: { 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(data => {
            deptosData = data.departamentos || [];
            const selectSalidas = document.getElementById('sal_tur_id');
            selectSalidas.innerHTML = '<option value="">-- Seleccione una Salida --</option>' + 
                (data.salidas || []).map(s => `<option value="${s.id}">${s.tour?.nom || 'Salida'} - ${s.fec_sal} [${s.cupos_disponibles} cupos]</option>`).join('');

            document.getElementById('formReserva').reset();
            document.getElementById('contenedorPasajeros').innerHTML = '';
            agregarPasajero();

            document.getElementById('modalReserva').classList.remove('hidden');
            document.getElementById('modalReserva').classList.add('flex');
        });
}

function closeModal() {
    document.getElementById('modalReserva').classList.add('hidden');
    document.getElementById('modalReserva').classList.remove('flex');
}

function agregarPasajero() {
    const container = document.getElementById('contenedorPasajeros');
    const index = container.children.length;
    const deptosOpts = deptosData.map(d => `<option value="${d.id}">${d.cod}</option>`).join('');

    const div = document.createElement('div');
    div.className = 'p-2 border border-gray-200 rounded-xl bg-gray-50 grid grid-cols-12 gap-2 items-center pax-row';
    div.innerHTML = `
        <input type="text" class="pax-nom col-span-5 border border-gray-300 rounded-lg p-1.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none" placeholder="Nombre Completo" required>
        <input type="text" class="pax-doc col-span-3 border border-gray-300 rounded-lg p-1.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none" placeholder="Documento">
        <select class="pax-dep col-span-2 border border-gray-300 rounded-lg p-1.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none"><option value="">Ext.</option>${deptosOpts}</select>
        <input type="number" class="pax-eda col-span-1 border border-gray-300 rounded-lg p-1.5 focus:ring-2 focus:ring-[#1F5F8B] focus:outline-none" placeholder="Edad" min="1" max="120" oninput="if(this.value < 1 && this.value !== '') this.value = 1; if(this.value > 120) this.value = 120;">
        <div class="col-span-1 text-center">
            ${index > 0 ? `<button type="button" onclick="this.closest('.pax-row').remove()" class="text-red-500 font-bold hover:text-red-700">&times;</button>` : ''}
        </div>
    `;
    container.appendChild(div);
}

function guardarReserva(e) {
    e.preventDefault();

    const participantes = Array.from(document.querySelectorAll('.pax-row')).map(row => {
        const valEdad = parseInt(row.querySelector('.pax-eda').value);
        return {
            nom_cpl: row.querySelector('.pax-nom').value.trim(),
            tip_doc: 'CI',
            num_doc: row.querySelector('.pax-doc').value.trim() || null,
            ci_dep_id: row.querySelector('.pax-dep').value || null,
            eda: (!isNaN(valEdad) && valEdad > 0) ? Math.min(valEdad, 120) : null
        };
    });

    const payload = {
        sal_tur_id: document.getElementById('sal_tur_id').value,
        nom_ctc: document.getElementById('nom_ctc').value.trim(),
        cor_ctc: document.getElementById('cor_ctc').value.trim(),
        tel_ctc: document.getElementById('tel_ctc').value.trim(),
        participantes
    };

    fetch('{{ route("admin.reservas.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                ...swalTailwindConfig,
                icon: 'success',
                title: '¡Éxito!',
                text: data.message
            }).then(() => location.reload());
        } else {
            Swal.fire({
                ...swalTailwindConfig,
                icon: 'warning',
                title: 'Atención',
                text: data.message || 'Error al procesar la reserva'
            });
        }
    })
    .catch(() => {
        Swal.fire({
            ...swalTailwindConfig,
            icon: 'error',
            title: 'Error',
            text: 'No se pudo guardar la reserva.'
        });
    });
}

function cancelarReserva(id) {
    Swal.fire({
        ...swalTailwindConfig,
        title: '¿Cancelar reserva?',
        text: 'Esta acción cancelará la reserva y anulará sus boletos asociados.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, mantener',
        customClass: {
            ...swalTailwindConfig.customClass,
            confirmButton: swalTailwindConfig.customClass.denyButton
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/reservas/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    ...swalTailwindConfig,
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Cancelada' : 'Error',
                    text: data.message
                }).then(() => location.reload());
            });
        }
    });
}
</script>
@endpush
@endsection