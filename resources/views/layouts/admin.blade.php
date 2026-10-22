<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel Administrativo - Pipocas</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pipocas: {
                            blue: '#1F5F8B',
                            dark: '#174870',
                            light: '#457699',
                            terra: '#C96F4A',
                            gold: '#F4C95D',
                            background: '#F0F0F1',
                            text: '#343A40',
                            muted: '#6B6F72',
                            warm: '#6B4F3A'
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        display: ['Fraunces', 'serif']
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --principal: #1F5F8B;
            --secundario: #C96F4A;
            --acento: #F4C95D;
            --complementario: #457699;
            --fondo: #F0F0F1;
            --texto: #343A40;
            --texto-2: #6B6F72;
            --calido: #6B4F3A;
            --hover: #174870;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--fondo);
            color: var(--texto);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        button, a, input, select, textarea {
            -webkit-tap-highlight-color: transparent;
        }

        .admin-shell {
            background:
                radial-gradient(ellipse at 85% 0%, rgba(69,118,153,.08), transparent 36%),
                var(--fondo);
        }

        .admin-sidebar {
            background: linear-gradient(165deg, #1F5F8B 0%, #174870 100%);
            box-shadow: 10px 0 40px rgba(23,72,112,.10);
        }

        .admin-brand-icon {
            display: grid;
            place-items: center;
            width: 43px;
            height: 43px;
            flex-shrink: 0;
            border-radius: 14px;
            background: var(--acento);
            color: var(--principal);
            box-shadow: 0 5px 15px rgba(0,0,0,.12);
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
            padding: 11px 13px;
            border: 1px solid transparent;
            border-radius: 12px;
            color: rgba(255,255,255,.82);
            font-size: 12px;
            font-weight: 600;
            transition: background .2s ease, color .2s ease,
                        transform .2s ease, border-color .2s ease;
        }

        .admin-nav-link i {
            width: 17px;
            text-align: center;
            font-size: 14px;
            opacity: .9;
        }

        .admin-nav-link:hover {
            background: rgba(255,255,255,.09);
            border-color: rgba(255,255,255,.08);
            color: #fff;
            transform: translateX(2px);
        }

        .admin-nav-link.active {
            color: #fff;
            background: linear-gradient(110deg, #C96F4A, #b85c3b);
            border-color: rgba(255,255,255,.12);
            box-shadow: 0 6px 16px rgba(10,35,55,.18);
        }

        .admin-nav-link.active i {
            color: #F4C95D;
        }

        .admin-topbar {
            background: rgba(255,255,255,.88);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(31,95,139,.10);
        }

        .admin-page-title {
            font-family: 'Fraunces', serif;
            letter-spacing: -.025em;
        }

        .admin-card {
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(31,95,139,.10);
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(31,58,80,.035);
            transition: box-shadow .25s ease, transform .25s ease;
        }

        .admin-card:hover {
            box-shadow: 0 12px 30px rgba(31,58,80,.075);
        }

        .admin-main {
            min-width: 0;
        }

        .admin-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(201,111,74,.16);
            border: 1px solid rgba(244,201,93,.18);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            transition: background .2s ease;
        }

        .admin-logout:hover {
            background: #C96F4A;
        }

        .admin-content {
            width: 100%;
            max-width: 1800px;
            margin: 0 auto;
        }

        /* Mejora visual para tablas de los distintos paneles */
        main table {
            border-collapse: separate;
            border-spacing: 0;
        }

        main table thead th {
            color: #6B6F72;
            font-size: 10px;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        main table tbody tr {
            transition: background .18s ease;
        }

        main table tbody tr:hover {
            background-color: rgba(69,118,153,.055);
        }

        main table tbody td {
            border-color: rgba(31,95,139,.08);
        }

        /* Controles coherentes con la identidad de Pipocas */
        main input:not([type="checkbox"]):not([type="radio"]),
        main select,
        main textarea {
            border-radius: 11px;
            border-color: #DDE3E8;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        main input:focus,
        main select:focus,
        main textarea:focus {
            outline: none;
            border-color: #457699;
            box-shadow: 0 0 0 3px rgba(69,118,153,.13);
        }

        main button,
        main a {
            transition-duration: .2s;
        }

        @media (max-width: 767px) {
            .admin-sidebar {
                width: min(290px, 86vw);
            }

            .admin-page-title {
                font-size: 1.15rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="antialiased min-h-screen">
    <div class="admin-shell flex h-screen overflow-hidden relative">

        {{-- Fondo al abrir el menú en móviles --}}
        <div id="mobile-overlay"
             onclick="toggleSidebar()"
             class="fixed inset-0 bg-[#102C40]/60 z-20 hidden md:hidden backdrop-blur-sm transition-opacity">
        </div>

        {{-- BARRA LATERAL --}}
        <aside id="sidebar"
               class="admin-sidebar fixed md:static inset-y-0 left-0 z-30 w-64 flex flex-col justify-between text-white overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

            <div class="p-5">
                {{-- Identidad de marca --}}
                <div class="flex items-center gap-3 pb-5 border-b border-white/10">
                    <div class="admin-brand-icon">
                        <i class="fa-solid fa-mountain-sun text-xl"></i>
                    </div>

                    <div class="min-w-0">
                        <h1 class="text-xl font-extrabold tracking-tight text-white">
                            Pipocas<span class="text-[#F4C95D]">.</span>
                        </h1>
                        <p class="text-[9px] font-bold tracking-[.16em] text-white/65 uppercase">
                            Agencia de turismo
                        </p>
                    </div>

                    <button onclick="toggleSidebar()"
                            class="md:hidden ml-auto text-white/80 hover:text-[#F4C95D] focus:outline-none p-2"
                            aria-label="Cerrar menú">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="mt-6 mb-3 px-3">
                    <p class="text-[9px] uppercase tracking-[.2em] font-bold text-white/45">
                        Administración
                    </p>
                </div>

                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard general</span>
                    </a>

                    <a href="{{ route('admin.operaciones') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.operaciones') ? 'active' : '' }}">
                        <i class="fa-solid fa-route"></i>
                        <span>Operaciones y salidas</span>
                    </a>

                    <a href="{{ route('admin.inventario') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.inventario') ? 'active' : '' }}">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>Inventario y equipos</span>
                    </a>

                    <a href="{{ route('admin.transporte') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.transporte') ? 'active' : '' }}">
                        <i class="fa-solid fa-van-shuttle"></i>
                        <span>Flota de transporte</span>
                    </a>

                    <a href="{{ route('admin.tours.index') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.tours.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-mountain-sun"></i>
                        <span>Catálogo y rutas</span>
                    </a>

                    <a href="{{ route('admin.reservas') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.reservas') ? 'active' : '' }}">
                        <i class="fa-solid fa-ticket"></i>
                        <span>Reservas y pasajeros</span>
                    </a>

                    {{-- Enlaces originales conservados y comentados --}}
                    <!--
                    <a href="{{ route('admin.finanzas') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.finanzas') ? 'active' : '' }}">
                        <i class="fa-solid fa-wallet"></i>
                        <span>Finanzas y comprobantes</span>
                    </a>

                    <a href="{{ route('admin.analitica') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.analitica') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Analítica y reportes</span>
                    </a>
                    -->

                    <a href="{{ route('admin.seguridad') }}"
                       class="admin-nav-link {{ request()->routeIs('admin.seguridad') ? 'active' : '' }}">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Configuración de usuarios</span>
                    </a>
                </nav>
            </div>

            {{-- Pie del menú --}}
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 mb-4 px-1">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-[#F4C95D]">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white">Vive La Paz.</p>
                        <p class="text-[10px] text-white/55">Descubre Bolivia.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Cerrar Sesión" class="admin-logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Cerrar sesión
                    </button>
                </form>

                <p class="text-center text-[9px] text-white/35 mt-4">
                    © {{ date('Y') }} Pipocas · Turismo
                </p>
            </div>
        </aside>

        {{-- CONTENIDO PRINCIPAL --}}
        <div class="admin-main flex-1 flex flex-col min-w-0 overflow-y-auto w-full">

            <header class="admin-topbar min-h-16 flex items-center justify-between gap-4 px-4 sm:px-8 py-3 sticky top-0 z-10">
                <div class="flex items-center gap-3 min-w-0">
                    <button onclick="toggleSidebar()"
                            class="md:hidden text-[#1F5F8B] hover:text-[#C96F4A] focus:outline-none p-2 rounded-xl border border-[#1F5F8B]/10 bg-white"
                            aria-label="Abrir menú">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="min-w-0">
                        <p class="text-[9px] sm:text-[10px] font-bold uppercase tracking-[.16em] text-[#457699] mb-0.5">
                            Pipocas · Administración
                        </p>
                        <h2 class="admin-page-title text-lg sm:text-xl font-semibold truncate text-[#343A40]">
                            @yield('header-title', 'Panel de Control')
                        </h2>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-2 rounded-full bg-[#1F5F8B]/5 border border-[#1F5F8B]/10 px-3 py-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-[10px] font-bold text-[#457699]">Panel administrativo</span>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8 flex-1">
                <div class="admin-content">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>

    @stack('scripts')
</body>
</html>