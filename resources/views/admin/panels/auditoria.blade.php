@extends('layouts.admin')

@section('header-title', 'Gestión de Usuarios y Accesos')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <button onclick="openModal()" class="px-4 py-2 bg-[#1F5F8B] text-white rounded-xl text-sm font-semibold hover:bg-[#184B6E] transition">
            + Nuevo Usuario
        </button>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="bg-white border rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="p-4">Usuario / Correo</th>
                        <th class="p-4">Rol</th>
                        <th class="p-4">Documento / Teléfono</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $usr)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">
                            <div class="font-bold text-gray-800">{{ $usr->nom }}</div>
                            <div class="text-xs text-gray-500">{{ $usr->cor }}</div>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase border bg-blue-100 text-blue-800 border-blue-200">
                                {{ $usr->rolRelacion->nom ?? $usr->rol ?? 'Turista' }}
                            </span>
                        </td>
                        <td class="p-4 text-xs text-gray-800">
                            <div>
                                <b>{{ $usr->tip_doc ?? 'CI' }}:</b> {{ $usr->num_doc ?? 'S/N' }}{{ $usr->ci_cmp ? "-{$usr->ci_cmp}" : '' }}
                                @if($usr->departamento) <span class="text-gray-500">({{ $usr->departamento->cod }})</span> @endif
                            </div>
                            <div class="text-gray-500 mt-0.5"><i class="fa-solid fa-phone text-[10px] mr-1"></i>{{ $usr->tel ?? 'Sin teléfono' }}</div>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $usr->act ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $usr->act ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <button onclick='openModal(@json($usr))' class="text-xs font-bold text-[#1F5F8B] hover:underline">Editar</button>
                            @if(Auth::id() !== $usr->id)
                                <button onclick="eliminarUsuario({{ $usr->id }}, '{{ addslashes($usr->nom) }}')" class="text-xs font-bold text-red-600 hover:underline">Eliminar</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">No hay usuarios registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $users->links() }}</div>
    </div>
</div>

<!-- Modal Crear / Editar -->
<div id="modalUsuario" class="fixed inset-0 z-50 bg-gray-900/50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 id="modalTitle" class="text-base font-bold text-gray-800">Usuario</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 font-bold">✕</button>
        </div>

        <form id="formUsuario" onsubmit="guardarUsuario(event)" class="space-y-3">
            <input type="hidden" id="usr_id">
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre Completo *</label>
                <input type="text" id="nom" required class="w-full border-gray-300 rounded-xl text-xs p-2.5">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Correo *</label>
                    <input type="email" id="cor" required class="w-full border-gray-300 rounded-xl text-xs p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Rol *</label>
                    <select id="rol_id" required class="w-full border-gray-300 rounded-xl text-xs p-2.5">
                        <option value="">Seleccionar...</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ ucfirst($r->nom) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Contraseña <span id="pwdHint" class="text-gray-400"></span></label>
                    <input type="password" id="clv" class="w-full border-gray-300 rounded-xl text-xs p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Teléfono</label>
                    <input type="text" id="tel" class="w-full border-gray-300 rounded-xl text-xs p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tipo Doc.</label>
                    <select id="tip_doc" onchange="toggleCIFields()" class="w-full border-gray-300 rounded-xl text-xs p-2.5">
                        <option value="CI">CI</option>
                        <option value="Pasaporte">Pasaporte</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nº Doc.</label>
                    <input type="text" id="num_doc" class="w-full border-gray-300 rounded-xl text-xs p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Comp.</label>
                    <input type="text" id="ci_cmp" class="w-full border-gray-300 rounded-xl text-xs p-2.5">
                </div>
            </div>

            <div id="divDepto">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Extensión (Departamento)</label>
                <select id="ci_dep_id" class="w-full border-gray-300 rounded-xl text-xs p-2.5">
                    <option value="">Seleccionar...</option>
                    @foreach($departamentos as $d)
                        <option value="{{ $d->id }}">{{ $d->nom }} ({{$d->cod }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end space-x-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-xl text-xs font-semibold">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-[#1F5F8B] text-white rounded-xl text-xs font-semibold">Guardar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function toggleCIFields() {
    document.getElementById('divDepto').classList.toggle('hidden', document.getElementById('tip_doc').value !== 'CI');
}

function openModal(usr = null) {
    document.getElementById('modalTitle').innerText = usr ? 'Editar Usuario' : 'Nuevo Usuario';
    document.getElementById('usr_id').value = usr ? usr.id : '';
    document.getElementById('nom').value = usr?.nom || '';
    document.getElementById('cor').value = usr?.cor || '';
    document.getElementById('rol_id').value = usr?.rol_id || '';
    document.getElementById('tel').value = usr?.tel || '';
    document.getElementById('tip_doc').value = usr?.tip_doc || 'CI';
    document.getElementById('num_doc').value = usr?.num_doc || '';
    document.getElementById('ci_cmp').value = usr?.ci_cmp || '';
    document.getElementById('ci_dep_id').value = usr?.ci_dep_id || '';
    document.getElementById('clv').value = '';
    document.getElementById('clv').required = !usr;
    document.getElementById('pwdHint').innerText = usr ? '(opcional)' : '(requerida)';
    
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

    const data = {
        nom: document.getElementById('nom').value,
        cor: document.getElementById('cor').value,
        clv: document.getElementById('clv').value,
        rol_id: document.getElementById('rol_id').value,
        tel: document.getElementById('tel').value,
        tip_doc: document.getElementById('tip_doc').value,
        num_doc: document.getElementById('num_doc').value,
        ci_cmp: document.getElementById('ci_cmp').value,
        ci_dep_id: document.getElementById('ci_dep_id').value || null,
    };

    fetch(url, {
        method: id ? 'PUT' : 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.message) {
            Swal.fire('OK', res.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', 'Revise los campos requeridos', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'No se pudo guardar', 'error'));
}

function eliminarUsuario(id, nombre) {
    if (!confirm(`¿Eliminar a ${nombre}?`)) return;

    fetch(`/admin/usuarios/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(res => {
        Swal.fire('Resultado', res.message, res.success ? 'success' : 'warning').then(() => location.reload());
    });
}
</script>
@endpush
@endsection