@extends('layouts.admin')

@section('header-title', 'Gestión de Reservas y Pasajeros')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('admin.reservas') }}" class="w-full sm:w-96">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Buscar por pasajero, CI o reserva..." 
                   class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 text-sm text-[#343A40] focus:ring-[#1F5F8B] focus:border-[#1F5F8B]">
        </form>
        <button onclick="modalNuevaReserva()" class="w-full sm:w-auto px-4 py-2 bg-[#1F5F8B] hover:bg-[#184B6E] text-white rounded-xl text-sm font-semibold transition">
            + Nueva Reserva
        </button>
    </div>


    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
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
                <tr>
                    <td class="p-4 font-medium text-[#343A40]">
                        <span class="font-bold text-[#1F5F8B]">#{{ $reserva->cod }}</span><br>
                        <span class="text-xs text-[#6B6F72]">{{ $reserva->salidaTour->tour->nom ?? 'Tour No Asignado' }}</span>
                    </td>
                    <td class="p-4 text-[#343A40]">
                        {{ $reserva->nom_ctc }}<br>
                        <span class="text-xs text-[#6B6F72]">{{ $reserva->tel_ctc }}</span>
                    </td>
                    <td class="p-4 text-[#343A40]">
                        <span class="px-2.5 py-1 bg-gray-100 border rounded-lg text-xs font-semibold">
                            {{ $reserva->cnt_ptc }} Pax
                        </span>
                    </td>
                    <td class="p-4 space-y-1">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium inline-block
                            {{ $reserva->est === 'confirmada' ? 'bg-green-100 text-green-700' : ($reserva->est === 'cancelada' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($reserva->est) }}
                        </span>
                        <div class="text-xs text-[#6B6F72]">Bs. {{ number_format($reserva->pre_tot, 2) }}</div>
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
                    <td colspan="5" class="p-6 text-center text-[#6B6F72]">No se encontraron reservas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $reservas->links() }}
        </div>
    </div>
@push('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';

function verDetalle(id) {
    Swal.fire({
        title: 'Cargando información...',
        didOpen: () => Swal.showLoading()
    });

    fetch(`/admin/reservas/${id}`)
        .then(res => res.json())
        .then(data => {
            let htmlPasajeros = data.participantes.map(p => `
                <div class="border-b border-gray-100 py-2 flex justify-between items-center text-left text-xs">
                    <div>
                        <p class="font-bold text-gray-800">${p.nom_cpl} ${p.es_ctc_pri ? '<span class="text-blue-600 font-semibold">(Contacto Principal)</span>' : ''}</p>
                        <p class="text-gray-500">${p.tip_doc || 'Doc'}: ${p.num_doc || 'S/N'} ${p.ci_cmp ? '- ' + p.ci_cmp : ''}</p>
                    </div>
                    <div>
                        ${p.fichas_medicas ? '<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px]">Ficha Médica OK</span>' : '<span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-[10px]">Sin Ficha</span>'}
                    </div>
                </div>
            `).join('');

            Swal.fire({
                title: `Reserva #${data.cod}`,
                html: `
                    <div class="text-left text-sm space-y-3">
                        <div class="bg-gray-50 p-3 rounded-xl border">
                            <p><strong>Tour:</strong> ${data.salida_tour && data.salida_tour.tour ? data.salida_tour.tour.nom : 'N/A'}</p>
                            <p><strong>Contacto:</strong> ${data.nom_ctc} (${data.tel_ctc})</p>
                            <p><strong>Correo:</strong> ${data.cor_ctc}</p>
                            <p><strong>Total:</strong> Bs. ${data.pre_tot}</p>
                        </div>
                        <h4 class="font-bold text-gray-700 text-xs uppercase tracking-wider mt-2">Pasajeros (${data.participantes.length})</h4>
                        <div class="max-h-48 overflow-y-auto divide-y divide-gray-100">
                            ${htmlPasajeros}
                        </div>
                    </div>
                `,
                confirmButtonColor: '#1F5F8B',
                confirmButtonText: 'Cerrar'
            });
        });
}


let deptosData = [];
let paisesData = [];

function modalNuevaReserva() {
    Swal.fire({
        title: 'Cargando datos...',
        didOpen: () => Swal.showLoading()
    });

    fetch('{{ route("admin.reservas.create-data") }}')
        .then(res => res.json())
        .then(data => {
            deptosData = data.departamentos;
            paisesData = data.paises;

            let opcionesSalidas = data.salidas.map(s => 
                `<option value="${s.id}">${s.tour ? s.tour.nom : 'Salida'} - ${s.fec_sal} (${s.hra_sal})</option>`
            ).join('');

            Swal.fire({
                title: 'Nueva Reserva',
                width: '700px',
                html: `
                    <form id="formNuevaReserva" class="text-left text-sm space-y-4">
                        <div>
                            <label class="block font-medium text-gray-700">Seleccionar Salida / Tour</label>
                            <select id="sal_tur_id" class="w-full bg-gray-50 border border-gray-300 rounded-xl p-2 text-xs" required>
                                <option value="">-- Seleccione una Salida --</option>
                                ${opcionesSalidas}
                            </select>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div>
                                <label class="block font-medium text-gray-700">Contacto</label>
                                <input type="text" id="nom_ctc" placeholder="Nombre Completo" class="w-full border rounded-xl p-2 text-xs" required>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Correo</label>
                                <input type="email" id="cor_ctc" placeholder="correo@ejemplo.com" class="w-full border rounded-xl p-2 text-xs" required>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700">Teléfono</label>
                                <input type="text" id="tel_ctc" placeholder="70000000" class="w-full border rounded-xl p-2 text-xs" required>
                            </div>
                        </div>

                        <hr class="my-2">
                        <div class="flex justify-between items-center">
                            <h4 class="font-bold text-xs uppercase text-gray-600">Pasajeros</h4>
                            <button type="button" onclick="agregarFilaPasajero()" class="text-xs bg-gray-200 hover:bg-gray-300 px-2.5 py-1 rounded-lg font-bold text-gray-700">+ Pasajero</button>
                        </div>

                        <div id="contenedorPasajeros" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            <!-- Filas dinámicas generadas desde JS -->
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonColor: '#1F5F8B',
                confirmButtonText: 'Guardar Reserva',
                cancelButtonText: 'Cancelar',
                didOpen: () => {
                    agregarFilaPasajero();
                },
                preConfirm: () => {
                    return guardarReserva();
                }
            });
        });
}

function agregarFilaPasajero() {
    const container = document.getElementById('contenedorPasajeros');
    const index = container.children.length;

    let deptosOpts = deptosData.map(d => `<option value="${d.id}">${d.nom} (${d.cod})</option>`).join('');

    let div = document.createElement('div');
    div.className = 'p-2 border rounded-xl bg-gray-50 grid grid-cols-12 gap-2 items-center pasajero-row';
    div.innerHTML = `
        <div class="col-span-4">
            <input type="text" class="ptc-nombre w-full border rounded-lg p-1.5 text-xs" placeholder="Nombre Completo" required>
        </div>
        <div class="col-span-3">
            <input type="text" class="ptc-doc w-full border rounded-lg p-1.5 text-xs" placeholder="Nº Documento">
        </div>
        <div class="col-span-2">
            <select class="ptc-depto w-full border rounded-lg p-1.5 text-xs">
                <option value="">Ext.</option>
                ${deptosOpts}
            </select>
        </div>
        <div class="col-span-2">
            <input type="number" class="ptc-edad w-full border rounded-lg p-1.5 text-xs" placeholder="Edad">
        </div>
        <div class="col-span-1 text-center">
            ${index > 0 ? `<button type="button" onclick="this.closest('.pasajero-row').remove()" class="text-red-500 font-bold text-xs">✕</button>` : ''}
        </div>
    `;
    container.appendChild(div);
}

function guardarReserva() {
    const sal_tur_id = document.getElementById('sal_tur_id').value;
    const nom_ctc = document.getElementById('nom_ctc').value;
    const cor_ctc = document.getElementById('cor_ctc').value;
    const tel_ctc = document.getElementById('tel_ctc').value;

    if (!sal_tur_id || !nom_ctc || !cor_ctc || !tel_ctc) {
        Swal.showValidationMessage('Completa los campos obligatorios del contacto y la salida.');
        return false;
    }

    let participantes = [];
    let isValid = true;

    document.querySelectorAll('.pasajero-row').forEach(row => {
        let nom = row.querySelector('.ptc-nombre').value;
        let numDoc = row.querySelector('.ptc-doc').value;
        let deptoId = row.querySelector('.ptc-depto').value;
        let edad = row.querySelector('.ptc-edad').value;

        if (!nom) {
            isValid = false;
        }

        participantes.push({
            nom_cpl: nom,
            tip_doc: 'CI', 
            num_doc: numDoc || null,
            ci_dep_id: deptoId || null,
            eda: edad ? parseInt(edad) : null
        });
    });

    if (!isValid || participantes.length === 0) {
        Swal.showValidationMessage('Todos los pasajeros deben tener al menos un nombre completo.');
        return false;
    }

    return fetch('{{ route("admin.reservas.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json', 
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({ 
            sal_tur_id, 
            nom_ctc, 
            cor_ctc, 
            tel_ctc, 
            participantes 
        })
    })
    .then(async res => {
        const contentType = res.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {

            const text = await res.text();
            console.error("Respuesta HTML del servidor:", text);
            throw new Error("El servidor devolvió un error inesperado (revisa la consola F12).");
        }
        return res.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Error al guardar');
        }
        Swal.fire({ icon: 'success', title: 'Éxito', text: data.message, confirmButtonColor: '#1F5F8B' })
            .then(() => window.location.reload());
    })
    .catch(error => {
        Swal.showValidationMessage(`Error: ${error.message}`);
    });
}

function cancelarReserva(id) {
    Swal.fire({
        title: '¿Cancelar esta reserva?',
        text: 'El estado de la reserva cambiará a cancelada.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'Atrás'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/reservas/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Cancelada', data.message, 'success')
                        .then(() => window.location.reload());
                }
            });
        }
    });
}
</script>
@endpush
@endsection