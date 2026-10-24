<nav class="space-y-1 text-xs font-semibold">
    @php
        $links = [
            ['route' => 'admin.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dashboard general', 'active' => request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')],
            ['route' => 'admin.operaciones', 'icon' => 'fa-route', 'label' => 'Operaciones y salidas', 'active' => request()->routeIs('admin.operaciones')],
            ['route' => 'admin.inventario', 'icon' => 'fa-boxes-stacked', 'label' => 'Inventario y equipos', 'active' => request()->routeIs('admin.inventario')],
            ['route' => 'admin.transporte', 'icon' => 'fa-van-shuttle', 'label' => 'Flota de transporte', 'active' => request()->routeIs('admin.transporte')],
            ['route' => 'admin.tours.index', 'icon' => 'fa-mountain-sun', 'label' => 'Catálogo y rutas', 'active' => request()->routeIs('admin.tours.*')],
            ['route' => 'admin.reservas', 'icon' => 'fa-ticket', 'label' => 'Reservas y pasajeros', 'active' => request()->routeIs('admin.reservas')],
            ['route' => 'admin.seguridad', 'icon' => 'fa-shield-halved', 'label' => 'Configuración de usuarios', 'active' => request()->routeIs('admin.seguridad')],
        ];
    @endphp

    @foreach($links as $link)
        <a href="{{ route($link['route']) }}" 
            class="flex items-center gap-3 min-h-[44px] px-3 py-2.5 rounded-xl border transition-all duration-200 {{ $link['active'] ? 'bg-gradient-to-r from-[#C96F4A] to-[#b85c3b] text-white border-white/10 shadow-md' : 'text-white/80 border-transparent hover:bg-white/10 hover:text-white hover:translate-x-0.5' }}">
            <i class="fa-solid {{ $link['icon'] }} w-4 text-center text-sm {{ $link['active'] ? 'text-pipocas-gold' : 'opacity-90' }}"></i>
            <span>{{ $link['label'] }}</span>
        </a>
    @endforeach
</nav>