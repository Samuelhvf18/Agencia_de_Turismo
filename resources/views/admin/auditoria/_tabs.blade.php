<nav aria-label="Seguridad y auditoría" class="inline-flex flex-wrap rounded-xl bg-white border border-gray-200 p-1 text-sm font-semibold">
    @foreach (['admin.auditoria' => 'Registro de actividad', 'admin.auditoria.notificaciones' => 'Notificaciones', 'admin.auditoria.usuarios' => 'Usuarios y roles'] as $ruta => $texto)
        <a href="{{ route($ruta) }}" @if (request()->routeIs($ruta)) aria-current="page" @endif class="rounded-lg px-4 py-2 {{ request()->routeIs($ruta) ? 'bg-[#1F5F8B] text-white' : 'text-[#343A40]' }}">{{ $texto }}</a>
    @endforeach
</nav>
