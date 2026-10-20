@extends('layouts.admin')

@section('header-title', 'Catálogo de Tours y Rutas')

@section('content')
<div class="space-y-6">
    <!-- Encabezado y Botón Nuevo -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex justify-between items-center">
        <h3 class="text-base font-bold text-[#343A40]">Paquetes Turísticos</h3>
        <button onclick="modalCrearTour()" class="px-4 py-2 bg-[#1F5F8B] hover:bg-[#164566] text-white text-xs font-semibold rounded-xl transition-colors">
            + Nuevo Tour
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($tours as $tour)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow relative">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-extrabold uppercase px-2 py-1 rounded-md bg-blue-50 text-[#1F5F8B]">
                        {{ $tour->categoria->nom ?? 'Sin Categoría' }}
                    </span>
                <button onclick="cambiarEstadoTour({{ $tour->id }}, {{ $tour->act ? 0 : 1 }})" 
                        class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $tour->act ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $tour->act ? 'Activo' : 'Inactivo' }}
                </button>
                </div>
                

                <h4 class="font-bold text-[#343A40] text-base mt-2">{{ $tour->nom }}</h4>
                <p class="text-xs text-[#6B6F72] mt-1 line-clamp-2">{{ $tour->des ?? 'Sin descripción' }}</p>

                <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-600 space-y-1">
                    <div class="flex justify-between">
                        <span>Código: <strong>{{ $tour->cod }}</strong></span>
                        <span>Dificultad: <strong class="capitalize">{{ $tour->niv_dif }}</strong></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Duración: <strong>{{ $tour->dur_hrs }} hrs</strong></span>
                        <span>Capacidad: <strong>{{ $tour->cap_max }} Max</strong></span>
                    </div>
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-dashed">
                        <span class="text-sm font-extrabold text-[#1F5F8B]">Bs{{ number_format($tour->pre_bas, 2) }}</span>
                        
                        <div class="space-x-2">
                            <button onclick="modalEditarTour({{ json_encode($tour) }})" class="text-xs font-semibold text-amber-600 hover:underline">
                                Editar
                            </button>
                            <!--<button onclick="eliminarTour({{ $tour->id }})" class="text-xs font-semibold text-red-600 hover:underline">
                                Eliminar
                            </button>-->
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-8 rounded-2xl border border-gray-200 text-center text-gray-500">
                No hay tours creados aún. Haz clic en <strong>+ Nuevo Tour</strong> para agregar el primero.
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>

    const categorias = @json($categorias);

function generarFormularioHTML(data = {}) {
    const catOptions = categorias.map(c => 
        `<option value="${c.id}" ${data.cat_id == c.id ? 'selected' : ''}>${c.nom}</option>`
    ).join('');

    return `
        <form id="formTour" class="text-left text-xs space-y-3 p-1">
            <div>
                <label class="font-bold text-gray-700 block mb-1">Categoría</label>
                <select id="cat_id" class="w-full p-2 border rounded-xl text-xs">${catOptions}</select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="font-bold text-gray-700 block mb-1">Código Unique</label>
                    <input id="cod" type="text" placeholder="TUR-01" value="${data.cod || ''}" class="w-full p-2 border rounded-xl text-xs">
                </div>
                <div>
                    <label class="font-bold text-gray-700 block mb-1">Nombre</label>
                    <input id="nom" type="text" placeholder="Salar de Uyuni" value="${data.nom || ''}" class="w-full p-2 border rounded-xl text-xs">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="font-bold text-gray-700 block mb-1">Dificultad</label>
                    <select id="niv_dif" class="w-full p-2 border rounded-xl text-xs">
                        <option value="facil" ${data.niv_dif == 'facil' ? 'selected' : ''}>Fácil</option>
                        <option value="moderado" ${data.niv_dif == 'moderado' ? 'selected' : ''}>Moderado</option>
                        <option value="dificil" ${data.niv_dif == 'dificil' ? 'selected' : ''}>Difícil</option>
                        <option value="experto" ${data.niv_dif == 'experto' ? 'selected' : ''}>Experto</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-gray-700 block mb-1">Duración (hrs)</label>
                    <!-- min="1" max="14" evita valores <= 0 o > 14 -->
                    <input id="dur_hrs" type="number" step="0.5" min="1" max="14" value="${data.dur_hrs || 1}" oninput="if(this.value < 1) this.value = 1; if(this.value > 14) this.value = 14;" class="w-full p-2 border rounded-xl text-xs">
                </div>
                <div>
                    <label class="font-bold text-gray-700 block mb-1">Capacidad Máx</label>
                    <!-- min="1" max="25" evita capacidad cero o negativa -->
                    <input id="cap_max" type="number" min="1" max="25" value="${data.cap_max || 10}" oninput="if(this.value < 1) this.value = 1;" class="w-full p-2 border rounded-xl text-xs">
                </div>
            </div>
            <div>
                <label class="font-bold text-gray-700 block mb-1">Precio Base (Bs)</label>
                <!-- min="0" evita valores negativos -->
                <input id="pre_bas" type="number" step="0.01" min="0" value="${data.pre_bas || ''}" oninput="if(this.value < 0) this.value = 0;" class="w-full p-2 border rounded-xl text-xs">
            </div>
            <div>
                <label class="font-bold text-gray-700 block mb-1">Descripción</label>
                <textarea id="des" class="w-full p-2 border rounded-xl text-xs" rows="2">${data.des || ''}</textarea>
            </div>
        </form>
    `;
}

    function modalCrearTour() {
    Swal.fire({
        title: 'Crear Nuevo Tour',
        html: generarFormularioHTML(),
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#1F5F8B',
        preConfirm: () => {
            const catId = document.getElementById('cat_id').value;
            const cod = document.getElementById('cod').value.trim();
            const nom = document.getElementById('nom').value.trim();
            const preBas = document.getElementById('pre_bas').value;


            if (!catId) {
                Swal.showValidationMessage('Debes seleccionar una Categoría.');
                return false;
            }
            if (!cod) {
                Swal.showValidationMessage('El Código Unique es obligatorio.');
                return false;
            }
            if (!nom) {
                Swal.showValidationMessage('El Nombre del tour es obligatorio.');
                return false;
            }
            if (!preBas || parseFloat(preBas) < 0) {
                Swal.showValidationMessage('Ingresa un Precio Base válido.');
                return false;
            }



            return {
                cat_id: parseInt(catId),
                cod: cod,
                nom: nom,
                niv_dif: document.getElementById('niv_dif').value,
                dur_hrs: parseFloat(document.getElementById('dur_hrs').value),
                cap_max: parseInt(document.getElementById('cap_max').value),
                pre_bas: parseFloat(preBas),
                des: document.getElementById('des').value.trim() || null,
                act: true
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            enviarFormulario("{{ route('admin.tours.store') }}", 'POST', result.value);
        }
    });
}

    function cambiarEstadoTour(id) {
    let url = "{{ route('admin.tours.toggle', ':id') }}".replace(':id', id);
    
    enviarFormulario(url, 'PATCH', {});
}
    function modalEditarTour(tour) {
        Swal.fire({
            title: 'Editar Tour',
            html: generarFormularioHTML(tour),
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#1F5F8B',
            preConfirm: () => {
                return {
                    cat_id: document.getElementById('cat_id').value,
                    cod: document.getElementById('cod').value,
                    nom: document.getElementById('nom').value,
                    niv_dif: document.getElementById('niv_dif').value,
                    dur_hrs: document.getElementById('dur_hrs').value,
                    cap_max: document.getElementById('cap_max').value,
                    pre_bas: document.getElementById('pre_bas').value,
                    des: document.getElementById('des').value,
                    act: 1
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let url = "{{ route('admin.tours.update', ':id') }}".replace(':id', tour.id);
                enviarFormulario(url, 'PUT', result.value);
            }
        });
    }

    function eliminarTour(id) {
        Swal.fire({
            title: '¿Eliminar Tour?',
            text: "Esta acción enviará el registro a la papelera.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = "{{ route('admin.tours.destroy', ':id') }}".replace(':id', id);
                enviarFormulario(url, 'DELETE', {});
            }
        });
    }

function enviarFormulario(url, metodo, datos) {

    let HTTP_METHOD = metodo;
    let bodyData = { ...datos };

    if (metodo === 'PUT' || metodo === 'DELETE') {
        HTTP_METHOD = 'POST';
        bodyData['_method'] = metodo;
    }

    fetch(url, {
        method: HTTP_METHOD,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(bodyData)
    })
    .then(async response => {
        const data = await response.json();
        return { status: response.status, body: data };
    })
    .then(res => {
        if (res.status === 200 || res.status === 201) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: res.body.message,
                confirmButtonColor: '#1F5F8B'
            }).then(() => location.reload());
        } else if (res.status === 422) {

            let errores = Object.values(res.body.errors).flat().join('<br>');
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                html: errores
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: res.body.message || 'Ocurrió un error inesperado.'
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Error del Servidor',
            text: 'No se pudo completar la solicitud.'
        });
    });
}
</script>
@endpush
@endsection