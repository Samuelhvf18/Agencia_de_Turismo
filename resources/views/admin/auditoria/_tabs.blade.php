<div class="inline-flex flex-wrap rounded-xl bg-white border border-gray-200 p-1 text-sm font-semibold">
    @foreach (['admin.auditoria' => 'Bitácora', 'admin.auditoria.notificaciones' => 'Notificaciones', 'admin.auditoria.usuarios' => 'Usuarios y roles'] as $ruta => $texto)
        <a href="{{ route($ruta) }}" class="rounded-lg px-4 py-2 {{ request()->routeIs($ruta) ? 'bg-[#1F5F8B] text-white' : 'text-[#343A40]' }}">{{ $texto }}</a>
    @endforeach
</div>
