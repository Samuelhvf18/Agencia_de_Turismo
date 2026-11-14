<!DOCTYPE html>
<html lang="es">

<head>
    @auth<meta name="offline-account" content="{{ auth()->id() }}">@endauth
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('header-title', 'Administración') · Pipocas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @stack('styles')
</head>

<body class="admin-shell" x-data="{ menu: false, movil: window.innerWidth < 1024 }" @resize.window="movil = window.innerWidth < 1024"
    @keydown.escape.window="menu = false">
    <a href="#contenido-admin" class="admin-skip">Saltar al contenido</a>
    @php
        $administrador = auth()->user()?->esAdmin();
        $grupos = [
            'Tu agencia' => [
                ['admin.dashboard', 'fa-chart-pie', 'Vista general', 'admin.dashboard'],
                ['admin.reservas', 'fa-ticket', 'Reservas y pasajeros', 'admin.reservas*'],
                ['admin.operaciones', 'fa-calendar-days', 'Salidas y operación', 'admin.operaciones,admin.salidas.*'],
                ['admin.tours', 'fa-map-location-dot', 'Tours y rutas', 'admin.tours,admin.catalogo.*'],
            ],
            'Recursos' => [
                ['admin.personal', 'fa-user-group', 'Guías y choferes', 'admin.personal*'],
                ['admin.transporte', 'fa-van-shuttle', 'Vehículos', 'admin.transporte*'],
                ['admin.inventario', 'fa-boxes-stacked', 'Equipos e inventario', 'admin.inventario*'],
            ],
            'Gestión y resultados' => [
                ['admin.io.index', 'fa-compass-drafting', 'Planificación inteligente', 'admin.io.*'],
                ['admin.chatbot', 'fa-comments', 'Atención al cliente', 'admin.chatbot*'],
            ],
        ];
        if ($administrador) {
            $grupos['Gestión y resultados'][] = ['admin.finanzas', 'fa-wallet', 'Finanzas', 'admin.finanzas*'];
            $grupos['Gestión y resultados'][] = [
                'admin.analitica',
                'fa-chart-line',
                'Analítica y reportes',
                'admin.analitica',
            ];
            $grupos['Administración'] = [
                ['admin.auditoria', 'fa-shield-halved', 'Seguridad y auditoría', 'admin.auditoria*'],
            ];
        }
        $nombre = auth()->user()?->nom ?? 'Vista de demostración';
    @endphp
    <div x-cloak x-show="menu" x-transition.opacity @click="menu = false" class="admin-overlay lg:hidden"></div>
    <aside class="admin-sidebar" :class="{ 'is-open': menu }" :inert="!menu && movil" aria-label="Navegación principal">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <span class="admin-brand-icon"><i aria-hidden="true" class="fa-solid fa-mountain-sun"></i></span>
            <span><strong>Pipocas<span class="text-[#F4C95D]">.</span></strong><small>GESTIÓN DE TU
                    AGENCIA</small></span>
        </a>
        <div class="admin-workspace"><span class="admin-online"></span> Espacio de administración <button type="button"
                @click="menu = false" class="ml-auto lg:hidden" aria-label="Cerrar menú"><i aria-hidden="true" class="fa-solid fa-xmark"></i></button></div>
        <nav class="admin-navigation">
            @foreach ($grupos as $grupo => $enlaces)
                <p class="admin-nav-label">{{ $grupo }}</p>
                @foreach ($enlaces as [$ruta, $icono, $texto, $patrones])
                    @php $activo = request()->routeIs(...explode(',', $patrones)); @endphp
                    <a href="{{ route($ruta) }}" class="admin-nav-link {{ $activo ? 'is-active' : '' }}"
                        @if ($activo) aria-current="page" @endif>
                        <i class="fa-solid {{ $icono }}" aria-hidden="true"></i><span>{{ $texto }}</span>
                        @if ($activo)
                            <span class="admin-nav-dot"></span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </nav>
        <div class="admin-sidebar-bottom">
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="admin-nav-link"><i aria-hidden="true" class="fa-solid fa-arrow-up-right-from-square"></i> Ver sitio público</a>
            <div class="admin-user"><span class="admin-avatar">{{ mb_substr($nombre, 0, 1) }}</span>
                <div class="min-w-0 flex-1"><strong
                        class="block truncate">{{ $nombre }}</strong><small>{{ $administrador ? 'Administrador' : (auth()->check() ? 'Operaciones' : 'Demostración') }}</small>
                </div>
                @auth<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"
                            class="admin-icon-button" aria-label="Cerrar sesión" title="Cerrar sesión"><i aria-hidden="true" class="fa-solid fa-arrow-right-from-bracket"></i></button></form>@endauth
            </div>
        </div>
    </aside>
    <div class="admin-workarea">
        <header class="admin-topbar">
            <div class="flex min-w-0 items-center gap-3"><button type="button" @click="menu = !menu"
                    class="admin-icon-button lg:hidden" :aria-expanded="menu" aria-label="Abrir menú"><i aria-hidden="true" class="fa-solid fa-bars"></i></button><span class="admin-breadcrumb">Administración <i aria-hidden="true" class="fa-solid fa-chevron-right"></i> <strong>@yield('header-title', 'Vista general')</strong></span></div>
            <div class="flex shrink-0 items-center gap-3"><span
                    class="hidden text-xs text-slate-500 md:block">{{ now('America/La_Paz')->locale('es')->translatedFormat('d M Y') }}</span><span
                    class="admin-avatar admin-avatar-light"
                    title="{{ $nombre }}">{{ mb_substr($nombre, 0, 1) }}</span></div>
        </header>
        <main id="contenido-admin" class="admin-main" tabindex="-1">
            <div class="admin-page-heading">
                <div>
                    <p class="admin-eyebrow">PIPOCAS /
                        {{ request()->routeIs('admin.io.*') ? 'PLANIFICACIÓN' : 'ADMINISTRACIÓN' }}</p>
                    <h1>@yield('header-title', 'Vista general')</h1>
                    <p class="admin-page-description">@yield('page-description', 'Toda la información de tu agencia, en un solo lugar.')</p>
                </div>
                <div class="admin-page-actions">@yield('page-actions')</div>
            </div>
            @yield('content')
            <footer class="admin-footer"><span>Pipocas · Agencia de turismo</span><span>Planifica mejor. Viaja más
                    lejos.</span></footer>
        </main>
    </div>
    @stack('scripts')
</body>

</html>
