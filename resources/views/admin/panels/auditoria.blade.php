@extends('layouts.admin')

@section('header-title', 'Configuración de Usuarios y Bitácora de Auditoría')

@section('content')
<div class="space-y-6">


    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-[#343A40]">Gestión de Usuarios</h3>
            <button onclick="openModalCrear()" class="px-4 py-2 bg-[#1F5F8B] text-white font-semibold rounded-lg text-xs hover:bg-[#154363] transition-colors">
                + Nuevo Usuario
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs font-semibold border-b border-gray-200 uppercase text-[#6B6F72]">
                        <th class="pb-3">Nombre</th>
                        <th class="pb-3">Correo</th>
                        <th class="pb-3">Rol</th>
                        <th class="pb-3">Documento</th>
                        <th class="pb-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($users as $usr)
                    <tr>
                        <td class="py-3 font-medium text-[#343A40]">{{ $usr->nom }}</td>
                        <td class="py-3 text-[#6B6F72]">{{ $usr->cor }}</td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-100 text-blue-800">
                                {{ $usr->rol->nom ?? 'Sin rol' }}
                            </span>
                        </td>
                        <td class="py-3 text-[#6B6F72]">
                            {{ $usr->tip_doc ? $usr->tip_doc . ': ' .$usr->num_doc : 'N/A' }}
                            @if($usr->ci_cmp) - {{$usr->ci_cmp }} @endif
                            @if($usr->departamento) ({{$usr->departamento->cod }}) @endif
                        </td>
                        <td class="py-3 text-right space-x-2">
                            <button onclick='openModalEditar(@json($usr))' class="text-indigo-600 hover:text-indigo-900 font-semibold">Editar</button>
                            <button onclick="eliminarUsuario({{ $usr->id }})" class="text-red-600 hover:text-red-900 font-semibold">Eliminar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">No hay usuarios registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-base font-bold text-[#343A40] mb-4">Bitácora de Auditoría</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs font-semibold border-b border-gray-200 uppercase text-[#6B6F72]">
                        <th class="pb-3">Fecha</th>
                        <th class="pb-3">Usuario</th>
                        <th class="pb-3">Tabla / Reg.</th>
                        <th class="pb-3">Acción</th>
                        <th class="pb-3">IP</th>
                        <th class="pb-3 text-right">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($bitacora as $log)
                    <tr>
                        <td class="py-3 text-[#6B6F72]">{{ $log->cre_en ? $log->cre_en->format('d/m/Y H:i:s') : 'N/A' }}</td>
                        <td class="py-3 font-medium text-[#343A40]">{{ $log->usuario->nom ?? 'Sistema' }}</td>
                        <td class="py-3 font-mono text-[#343A40]">{{ $log->nom_tbl }} #{{ $log->id_reg }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                {{ $log->acc === 'CREATE' ? 'bg-green-100 text-green-800' : ($log->acc === 'UPDATE' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $log->acc }}
                            </span>
                        </td>
                        <td class="py-3 font-mono font-semibold text-[#1F5F8B]">{{ $log->dir_ip ?? 'N/I' }}</td>
                        <td class="py-3 text-right">
                            @if($log->val_ant || $log->val_nvo)
                                <button onclick='verDetalleAuditoria(@json($log))' class="text-[#1F5F8B] hover:underline font-semibold">Ver Cambios</button>
                            @else
                                <span class="text-gray-400">Sin datos</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">Sin registros de auditoría.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div id="modalUsuario" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl p-6 shadow-xl space-y-4">
        <h3 id="modalTitle" class="text-lg font-bold text-gray-800"></h3>
        <form id="formUsuario" onsubmit="guardarUsuario(event)" class="space-y-3">
            <input type="hidden" id="usr_id">
            
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre Completo *</label>
                <input type="text" id="nom" required class="w-full border-gray-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Correo Electrónico *</label>
                    <input type="email" id="cor" required class="w-full border-gray-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Contraseña <span id="pwdHint" class="text-gray-400 font-normal"></span></label>
                    <input type="password" id="clv" class="w-full border-gray-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Rol *</label>
                    <select id="rol_id" required class="w-full border-gray-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                        <option value="">Seleccione...</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Teléfono</label>
                    <input type="text" id="tel" class="w-full border-gray-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo Doc.</label>
                    <select id="tip_doc" onchange="toggleCIFields()" class="w-full border-gray-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                        <option value="">Ninguno</option>
                        <option value="CI">CI (Carnet)</option>
                        <option value="Pasaporte">Pasaporte</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nro Documento</label>
                    <input type="text" id="num_doc" class="w-full border-gray-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Complemento</label>
                    <input type="text" id="ci_cmp" placeholder="ej. 1A" class="w-full border-gray-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                </div>
            </div>

            <div id="divDepto" class="hidden">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Extensión (Departamento) *</label>
                <select id="ci_dep_id" class="w-full border-gray-300 rounded-lg text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                    <option value="">Seleccione Departamento...</option>
                    @foreach($departamentos as $d)
                        <option value="{{ $d->id }}">{{ $d->nom }} ({{$d->cod }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end space-x-2 pt-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-xs font-semibold">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-[#1F5F8B] text-white rounded-lg text-xs font-semibold hover:bg-[#154363]">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleCIFields() {
    const tipDoc = document.getElementById('tip_doc').value;
    const divDepto = document.getElementById('divDepto');
    if (tipDoc === 'CI') {
        divDepto.classList.remove('hidden');
    } else {
        divDepto.classList.add('hidden');
        document.getElementById('ci_dep_id').value = '';
    }
}

function openModalCrear() {
    document.getElementById('modalTitle').innerText = 'Nuevo Usuario';
    document.getElementById('usr_id').value = '';
    document.getElementById('formUsuario').reset();
    document.getElementById('clv').required = true;
    document.getElementById('pwdHint').innerText = '*';
    toggleCIFields();
    document.getElementById('modalUsuario').classList.remove('hidden');
    document.getElementById('modalUsuario').classList.add('flex');
}

function openModalEditar(usr) {
    document.getElementById('modalTitle').innerText = 'Editar Usuario';
    document.getElementById('usr_id').value = usr.id;
    document.getElementById('nom').value = usr.nom;
    document.getElementById('cor').value = usr.cor;
    document.getElementById('rol_id').value = usr.rol_id;
    document.getElementById('tel').value = usr.tel || '';
    document.getElementById('tip_doc').value = usr.tip_doc || '';
    document.getElementById('num_doc').value = usr.num_doc || '';
    document.getElementById('ci_cmp').value = usr.ci_cmp || '';
    document.getElementById('ci_dep_id').value = usr.ci_dep_id || '';
    document.getElementById('clv').value = '';
    document.getElementById('clv').required = false;
    document.getElementById('pwdHint').innerText = '(opcional)';
    toggleCIFields();
    document.getElementById('modalUsuario').classList.remove('hidden');
    document.getElementById('modalUsuario').classList.add('flex');
}

function closeModal() {
    document.getElementById('modalUsuario').classList.add('hidden');
    document.getElementById('modalUsuario').classList.remove('flex');
}

function guardarUsuario(e) {
    e.preventDefault();
    const id = document.getElementById('usr_id').value;
    const url = id ? `/admin/usuarios/${id}` : '/admin/usuarios';
    const method = id ? 'PUT' : 'POST';

    const data = {
        nom: document.getElementById('nom').value,
        cor: document.getElementById('cor').value,
        clv: document.getElementById('clv').value,
        rol_id: document.getElementById('rol_id').value,
        tel: document.getElementById('tel').value,
        tip_doc: document.getElementById('tip_doc').value,
        num_doc: document.getElementById('num_doc').value,
        ci_cmp: document.getElementById('ci_cmp').value,
        ci_dep_id: document.getElementById('ci_dep_id').value,
    };

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(async res => {
        const body = await res.json();
        if (!res.ok) throw body;
        return body;
    })
    .then(res => {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: res.message,
            timer: 1500,
            showConfirmButton: false
        }).then(() => location.reload());
    })
    .catch(err => {
        let msg = 'Ocurrió un error inesperado';
        if (err.errors) {
            msg = Object.values(err.errors).flat().join('<br>');
        }
        Swal.fire({ icon: 'error', title: 'Error de validación', html: msg });
    });
}

function eliminarUsuario(id) {
    Swal.fire({
        title: '¿Confirmas eliminar este usuario?',
        text: "El registro pasará a estado inactivo/eliminado.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/usuarios/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            });
        }
    });
}

function verDetalleAuditoria(log) {
    const parseJSON = (val) => {
        if (!val) return null;
        return typeof val === 'string' ? JSON.parse(val) : val;
    };

    const valAnt = parseJSON(log.val_ant);
    const valNvo = parseJSON(log.val_nvo);

    let valAntHtml = valAnt ? `<pre class="text-left bg-gray-100 p-2 rounded text-[11px] overflow-x-auto">${JSON.stringify(valAnt, null, 2)}</pre>` : '<p class="text-gray-400 italic">Sin datos anteriores</p>';
    let valNvoHtml = valNvo ? `<pre class="text-left bg-gray-100 p-2 rounded text-[11px] overflow-x-auto">${JSON.stringify(valNvo, null, 2)}</pre>` : '<p class="text-gray-400 italic">Sin datos nuevos</p>';

    Swal.fire({
        title: `Auditoría: ${log.acc} en ${log.nom_tbl}`,
        html: `
            <div class="space-y-3 text-xs">
                <p class="text-gray-600"><strong>Usuario ID:</strong> ${log.usr_id ?? 'Sistema'} | <strong>IP:</strong> ${log.dir_ip ?? 'N/I'}</p>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="font-bold text-red-600 mb-1">Valor Anterior:</p>
                        ${valAntHtml}
                    </div>
                    <div>
                        <p class="font-bold text-green-600 mb-1">Valor Nuevo:</p>
                        ${valNvoHtml}
                    </div>
                </div>
            </div>
        `,
        width: '650px',
        confirmButtonColor: '#1F5F8B',
        confirmButtonText: 'Cerrar'
    });
}
</script>
@endsection