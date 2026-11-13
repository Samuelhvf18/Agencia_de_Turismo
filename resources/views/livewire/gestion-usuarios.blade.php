<div class="space-y-6">

    @if($mensajeExito)
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center justify-between transition-all">
            <span><i class="fa-solid fa-circle-check mr-2"></i> {{ $mensajeExito }}</span>
            <button wire:click="$set('mensajeExito', '')" class="text-emerald-600 hover:text-emerald-900 font-bold">✕</button>
        </div>
    @endif

    @error('general')
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span><i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ $message }}</span>
            <button wire:click="$set('error', '')" class="text-red-600 hover:text-red-900 font-bold">✕</button>
        </div>
    @enderror

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Total Usuarios</span>
                <h4 class="text-2xl font-bold text-[#1F5F8B] mt-1">{{ \App\Models\User::count() }}</h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-blue-50 text-[#1F5F8B] flex items-center justify-center font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Usuarios Activos</span>
                <h4 class="text-2xl font-bold text-emerald-600 mt-1">
                    {{ \App\Models\User::where('act', true)->count() }}
                </h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6F72] uppercase font-semibold">Usuarios Desactivados</span>
                <h4 class="text-2xl font-bold text-red-600 mt-1">
                    {{ \App\Models\User::where('act', false)->count() }}
                </h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-user-slash"></i>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="w-full sm:w-96 relative">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Buscar en tiempo real por nombre, CI, rol..." 
                   class="w-full bg-gray-50 border border-gray-300 rounded-xl pl-9 pr-4 py-2 text-sm text-[#343A40] focus:ring-[#1F5F8B] focus:border-[#1F5F8B]">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
            <span wire:loading wire:target="search" class="absolute right-3 top-2.5 text-xs text-[#1F5F8B]">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
            </span>
        </div>

        <button wire:click="abrirModalCrear" 
                class="w-full sm:w-auto px-4 py-2 bg-[#1F5F8B] hover:bg-[#184B6E] text-white rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
            <span>+ Nuevo Usuario</span>
        </button>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase">
                    <tr>
                        <th class="p-4">Usuario / Correo</th>
                        <th class="p-4">Rol Asignado</th>
                        <th class="p-4">Documento / Teléfono</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $usr)
                    <tr class="hover:bg-gray-50/70 transition-colors {{ !$usr->act ? 'bg-gray-50/60 opacity-75' : '' }}">
                        <td class="p-4">
                            <div class="font-bold text-[#343A40] text-sm">{{ $usr->nom }}</div>
                            <div class="text-xs text-[#6B6F72]">{{ $usr->cor }}</div>
                        </td>
                        <td class="p-4">
                            @php
                                $rolNombre = strtolower($usr->rolRelacion->nom ?? $usr->rol ?? 'turista');
                                $badgeColor = match($rolNombre) {
                                    'admin' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'guia' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'chofer' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'operaciones' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase border {{ $badgeColor }}">
                                {{ $usr->rolRelacion->nom ?? $usr->rol ?? 'Turista' }}
                            </span>
                        </td>
                        <td class="p-4 text-xs text-[#343A40]">
                            <div>
                                <span class="font-semibold">{{ $usr->tip_doc ?? 'CI' }}:</span>
                                {{ $usr->num_doc ?? 'S/N' }}@if($usr->ci_cmp)-{{ $usr->ci_cmp }}@endif
                                @if($usr->departamento)
                                    <span class="text-[#6B6F72]">({{ $usr->departamento->cod }})</span>
                                @endif
                            </div>
                            <div class="text-[#6B6F72] mt-0.5">
                                <i class="fa-solid fa-phone text-[10px] mr-1"></i>{{ $usr->tel ?? 'Sin teléfono' }}
                            </div>
                        </td>
                        <td class="p-4">
                            @if($usr->act)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span> Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span> Desactivado
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <button wire:click="abrirModalEditar({{ $usr->id }})" 
                                    class="text-xs font-bold text-[#1F5F8B] hover:underline">
                                Editar
                            </button>

                            @if(auth()->id() !== $usr->id)
                                <button wire:click="toggleEstado({{ $usr->id }})" 
                                        wire:loading.attr="disabled"
                                        class="text-xs font-bold {{ $usr->act ? 'text-red-500 hover:underline' : 'text-emerald-600 hover:underline' }}">
                                    {{ $usr->act ? 'Desactivar' : 'Activar' }}
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-[#6B6F72]">No se encontraron usuarios coincidentes con la búsqueda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>

    @if($modalAbierto)
    <div class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-base font-bold text-gray-800">
                    {{ $userId ? 'Editar Usuario' : 'Nuevo Usuario' }}
                </h3>
                <button type="button" wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
            </div>

            <form wire:submit.prevent="guardar" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nombre Completo *</label>
                    <input type="text" wire:model="nom" placeholder="Ej. Juan Pérez" 
                           class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                    @error('nom') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Correo Electrónico *</label>
                        <input type="email" wire:model="cor" placeholder="usuario@gmail.com" 
                                class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                        @error('cor') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Rol en el Sistema *</label>
                        <select wire:model="rol_id" class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                            <option value="">Seleccione un Rol...</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ ucfirst($r->nom) }}</option>
                            @endforeach
                        </select>
                        @error('rol_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if(!$userId)
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Contraseña Inicial *</label>
                    <input type="password" wire:model="clv" placeholder="Mínimo 6 caracteres" 
                           class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                    @error('clv') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Teléfono / WhatsApp</label>
                        <input type="text" wire:model="tel" placeholder="70000000" 
                               class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                        @error('tel') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tipo Doc.</label>
                        <select wire:model.live="tip_doc" class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                            <option value="CI">CI (Carnet)</option>
                            <option value="Pasaporte">Pasaporte</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nº Documento</label>
                        <input type="text" wire:model="num_doc" placeholder="1234567" 
                               class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Complemento</label>
                        <input type="text" wire:model="ci_cmp" placeholder="1A (opcional)" 
                               class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                    </div>
                </div>

                @if($tip_doc === 'CI')
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Extensión (Departamento)</label>
                    <select wire:model="ci_dep_id" class="w-full border-gray-300 rounded-xl text-xs p-2.5 focus:ring-1 focus:ring-[#1F5F8B]">
                        <option value="">Seleccione Departamento...</option>
                        @foreach($departamentos as $d)
                            <option value="{{ $d->id }}">{{ $d->nom }} ({{ $d->cod }})</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="flex justify-end space-x-2 pt-4 border-t">
                    <button type="button" wire:click="cerrarModal" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-[#1F5F8B] text-white rounded-xl text-xs font-semibold hover:bg-[#184B6E] transition flex items-center gap-1">
                        <span wire:loading.remove>Guardar</span>
                        <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
