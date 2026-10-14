<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101d30">

    <title>Dashboard | Pipocas</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --navy: #101d30;
            --navy-light: #1b2e47;
            --gold: #e8b65c;
            --cream: #f8f6f1;
        }

        body {
            background: var(--cream);
        }

        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: #334155 transparent;
        }

        .hero-banner {
            background:
                radial-gradient(ellipse at 88% 15%, rgba(232,182,92,.20), transparent 32%),
                linear-gradient(115deg, #101d30 0%, #1b3049 65%, #243c54 100%);
        }

        .hero-landscape {
            position: absolute;
            inset: 0 0 0 auto;
            width: 48%;
            overflow: hidden;
            pointer-events: none;
        }

        .hero-landscape .mountain {
            position: absolute;
            bottom: -2px;
            right: -3%;
            width: 100%;
            height: 75%;
            background: #31465c;
            opacity: .75;
            clip-path: polygon(0 100%, 25% 34%, 37% 53%, 59% 4%, 77% 43%, 91% 25%, 100% 42%, 100% 100%);
        }

        .hero-landscape .mountain-back {
            right: 22%;
            bottom: -2px;
            width: 100%;
            height: 55%;
            background: #496076;
            opacity: .42;
            clip-path: polygon(0 100%, 28% 45%, 45% 64%, 69% 9%, 100% 100%);
        }

        .stat-card,
        .module-card {
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .stat-card:hover,
        .module-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(16, 29, 48, .08);
        }

        .nav-link {
            transition: background .2s ease, color .2s ease;
        }

        .nav-link:hover {
            background: rgba(255,255,255,.07);
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
            }
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="min-h-screen antialiased text-slate-800"
      x-data="{ submenu: 'tours', sidebarMovil: false }">

@php
    $usuario = auth()->user();
    $nombreUsuario = $usuario->nom ?? $usuario->name ?? 'Usuario';
    $inicialUsuario = mb_strtoupper(mb_substr($nombreUsuario, 0, 1));
@endphp

<div class="min-h-screen">

    {{-- FONDO DEL MENÚ MÓVIL --}}
    <div x-cloak
         x-show="sidebarMovil"
         x-transition.opacity
         @click="sidebarMovil = false"
         class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm md:hidden">
    </div>

    {{-- MENÚ LATERAL --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-[#101d30] text-slate-300 shadow-2xl transition-transform duration-300 md:translate-x-0"
        :class="sidebarMovil ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        {{-- MARCA --}}
        <div class="relative border-b border-white/10 px-6 py-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-400 text-[#101d30] shadow-lg shadow-amber-950/20">
                    <i class="fa-solid fa-mountain-sun text-2xl"></i>
                </span>

                <span>
                    <span class="block text-xl font-black tracking-tight text-white">
                        Pipocas<span class="text-amber-400">.</span>
                    </span>
                    <span class="mt-0.5 block text-[10px] font-bold uppercase tracking-[.2em] text-slate-400">
                        Administración
                    </span>
                </span>
            </a>

            <button type="button"
                    @click="sidebarMovil = false"
                    class="absolute right-3 top-4 rounded-lg p-2 text-slate-400 hover:bg-white/10 md:hidden"
                    aria-label="Cerrar menú">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        {{-- NAVEGACIÓN --}}
        <nav class="sidebar-scroll flex-1 space-y-1 overflow-y-auto px-4 py-5">

            <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[.2em] text-slate-500">
                Menú principal
            </p>

            <a href="{{ route('dashboard') }}"
               class="nav-link flex items-center gap-3 rounded-xl bg-amber-400 px-4 py-3 font-bold text-[#101d30] shadow-md shadow-black/10">
                <i class="fa-solid fa-house w-5 text-center"></i>
                <span class="flex-1 text-sm">Dashboard</span>
                <i class="fa-solid fa-circle text-[6px]"></i>
            </a>

            <p class="mb-2 mt-7 px-3 text-[10px] font-bold uppercase tracking-[.2em] text-slate-500">
                Gestión turística
            </p>

            {{-- TOURS --}}
            <div>
                <button type="button"
                        @click="submenu = submenu === 'tours' ? '' : 'tours'"
                        class="nav-link flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left">
                    <i class="fa-solid fa-map-location-dot w-5 text-center text-amber-400"></i>
                    <span class="flex-1 text-sm font-medium">Gestión de tours</span>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform"
                       :class="submenu === 'tours' ? 'rotate-180' : ''"></i>
                </button>

                <div x-cloak
                     x-show="submenu === 'tours'"
                     x-transition
                     class="ml-6 mt-1 space-y-1 border-l border-slate-700 pl-4">

                    <a href="{{ route('admin.tours') }}"
                       class="nav-link block rounded-lg px-3 py-2 text-xs hover:text-white">
                        Catálogo de tours
                    </a>

                    <a href="{{ route('admin.tours') }}"
                       class="nav-link block rounded-lg px-3 py-2 text-xs hover:text-white">
                        Categorías
                    </a>

                    <a href="{{ route('admin.tours') }}"
                       class="nav-link block rounded-lg px-3 py-2 text-xs hover:text-white">
                        Rutas y puntos de interés
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.operaciones') }}"
               class="nav-link flex items-center gap-3 rounded-xl px-4 py-3">
                <i class="fa-solid fa-calendar-days w-5 text-center text-slate-400"></i>
                <span class="text-sm">Operaciones y salidas</span>
            </a>

            <a href="{{ route('admin.reservas') }}"
               class="nav-link flex items-center gap-3 rounded-xl px-4 py-3">
                <i class="fa-solid fa-ticket w-5 text-center text-slate-400"></i>
                <span class="text-sm">Reservas</span>
            </a>

            <p class="mb-2 mt-7 px-3 text-[10px] font-bold uppercase tracking-[.2em] text-slate-500">
                Administración
            </p>

            <a href="{{ route('admin.personal') }}"
               class="nav-link flex items-center gap-3 rounded-xl px-4 py-3">
                <i class="fa-solid fa-users w-5 text-center text-slate-400"></i>
                <span class="text-sm">Personal</span>
            </a>

            <a href="{{ route('admin.transporte') }}"
               class="nav-link flex items-center gap-3 rounded-xl px-4 py-3">
                <i class="fa-solid fa-bus w-5 text-center text-slate-400"></i>
                <span class="text-sm">Transporte</span>
            </a>

            <a href="{{ route('admin.inventario') }}"
               class="nav-link flex items-center gap-3 rounded-xl px-4 py-3">
                <i class="fa-solid fa-boxes-stacked w-5 text-center text-slate-400"></i>
                <span class="text-sm">Inventario</span>
            </a>

            <a href="{{ route('admin.finanzas') }}"
               class="nav-link flex items-center gap-3 rounded-xl px-4 py-3">
                <i class="fa-solid fa-coins w-5 text-center text-slate-400"></i>
                <span class="text-sm">Finanzas</span>
            </a>

            <a href="{{ route('admin.auditoria') }}"
               class="nav-link flex items-center gap-3 rounded-xl px-4 py-3">
                <i class="fa-solid fa-shield-halved w-5 text-center text-slate-400"></i>
                <span class="text-sm">Auditoría</span>
            </a>
        </nav>

        {{-- PERFIL --}}
        <div class="border-t border-white/10 p-4">
            <div class="mb-3 flex items-center gap-3 rounded-xl bg-white/[.04] p-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-400 font-black text-[#101d30]">
                    {{ $inicialUsuario }}
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-white">
                        {{ $nombreUsuario }}
                    </p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Sesión iniciada</p>
                </div>

                <span class="h-2 w-2 rounded-full bg-emerald-400"
                      title="Sesión activa"></span>
            </div>

            <form method="POST"
                  action="{{ route('logout') }}"
                  onsubmit="return confirmarSalida(event)">
                @csrf
                <button type="submit"
                        class="nav-link w-full rounded-xl px-3 py-3 text-left text-sm text-rose-300 hover:text-rose-200">
                    <i class="fa-solid fa-right-from-bracket mr-3 w-4 text-center"></i>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="min-h-screen min-w-0 md:ml-72">

        {{-- BARRA SUPERIOR --}}
        <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur-md">
            <div class="flex min-h-[76px] items-center justify-between gap-4 px-5 py-3 sm:px-8">

                <div class="flex min-w-0 items-center gap-3">
                    <button type="button"
                            @click="sidebarMovil = true"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 md:hidden"
                            aria-label="Abrir menú">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">
                            Pipocas <span class="mx-1 text-slate-300">/</span> Administración
                        </p>
                        <h1 class="mt-1 truncate text-lg font-black tracking-tight text-[#101d30] sm:text-xl">
                            Panel de control
                        </h1>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="max-w-44 truncate text-sm font-bold text-slate-800">
                            {{ $nombreUsuario }}
                        </p>
                        <p class="mt-0.5 text-xs text-slate-500">Administrador</p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-amber-200 bg-amber-100 font-black text-[#8b5e16]">
                        {{ $inicialUsuario }}
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-[1600px] p-5 sm:p-7 lg:p-9">

            {{-- BIENVENIDA --}}
            <section class="hero-banner relative isolate overflow-hidden rounded-[28px] shadow-xl shadow-slate-900/10">
                <div class="hero-landscape">
                    <div class="mountain-back"></div>
                    <div class="mountain"></div>
                </div>

                <div class="absolute right-[15%] top-8 h-20 w-20 rounded-full bg-amber-300/10 blur-2xl"></div>

                <div class="relative z-10 max-w-3xl px-6 py-9 sm:px-9 sm:py-11 lg:px-11 lg:py-12">
                    <span class="inline-flex items-center gap-2 rounded-full border border-amber-300/25 bg-amber-300/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[.2em] text-amber-300">
                        <i class="fa-solid fa-mountain-sun"></i>
                        Agencia de viajes · La Paz, Bolivia
                    </span>

                    <h2 class="mt-5 text-3xl font-black leading-tight tracking-tight text-white sm:text-4xl lg:text-[42px]">
                        ¡Bienvenido,<br class="sm:hidden">
                        <span class="text-amber-300">{{ $nombreUsuario }}</span>!
                    </h2>

                    <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300 sm:text-base">
                        Tu espacio para organizar experiencias, coordinar salidas
                        y administrar las operaciones de Pipocas desde un solo lugar.
                    </p>

                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <a href="{{ route('admin.tours') }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-3 text-sm font-extrabold text-[#101d30] shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-amber-300">
                            <i class="fa-solid fa-compass"></i>
                            Explorar tours
                            <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                        </a>

                        <a href="{{ route('admin.operaciones') }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            <i class="fa-regular fa-calendar-check"></i>
                            Ver operaciones
                        </a>
                    </div>
                </div>
            </section>

            {{-- TÍTULO DE SECCIÓN --}}
            <section class="mt-9">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-[.2em] text-amber-700">
                            Vista general
                        </p>
                        <h2 class="mt-1 text-xl font-black tracking-tight text-[#101d30] sm:text-2xl">
                            Resumen de la agencia
                        </h2>
                        <p class="mt-1.5 text-sm text-slate-500">
                            Indicadores clave de tu actividad turística.
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800">
                        <i class="fa-solid fa-database"></i>
                        Datos por conectar
                    </span>
                </div>

                {{-- INDICADORES --}}
                <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                    <article class="stat-card rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-500">Reservas</p>
                                <p class="mt-4 text-3xl font-black tracking-tight text-[#101d30]">—</p>
                            </div>
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-700">
                                <i class="fa-solid fa-ticket text-xl"></i>
                            </span>
                        </div>
                        <div class="mt-5 border-t border-slate-100 pt-3">
                            <p class="text-xs text-slate-500">Reservas registradas</p>
                        </div>
                    </article>

                    <article class="stat-card rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-500">Tours activos</p>
                                <p class="mt-4 text-3xl font-black tracking-tight text-[#101d30]">—</p>
                            </div>
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                                <i class="fa-solid fa-map-location-dot text-xl"></i>
                            </span>
                        </div>
                        <div class="mt-5 border-t border-slate-100 pt-3">
                            <p class="text-xs text-slate-500">Tours disponibles</p>
                        </div>
                    </article>

                    <article class="stat-card rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-500">Próximas salidas</p>
                                <p class="mt-4 text-3xl font-black tracking-tight text-[#101d30]">—</p>
                            </div>
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-700">
                                <i class="fa-solid fa-calendar-days text-xl"></i>
                            </span>
                        </div>
                        <div class="mt-5 border-t border-slate-100 pt-3">
                            <p class="text-xs text-slate-500">Salidas por realizar</p>
                        </div>
                    </article>

                    <article class="stat-card rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-500">Ingresos</p>
                                <p class="mt-4 text-3xl font-black tracking-tight text-[#101d30]">—</p>
                            </div>
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                                <i class="fa-solid fa-sack-dollar text-xl"></i>
                            </span>
                        </div>
                        <div class="mt-5 border-t border-slate-100 pt-3">
                            <p class="text-xs text-slate-500">Ingresos registrados en Bs</p>
                        </div>
                    </article>

                </div>
            </section>

            {{-- ACCESOS RÁPIDOS --}}
            <section class="mt-10">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-[.2em] text-amber-700">
                        Navegación
                    </p>
                    <h2 class="mt-1 text-xl font-black tracking-tight text-[#101d30] sm:text-2xl">
                        Accesos rápidos
                    </h2>
                    <p class="mt-1.5 text-sm text-slate-500">
                        Continúa directamente con el módulo que necesitas.
                    </p>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                    {{-- TOURS --}}
                    <a href="{{ route('admin.tours') }}"
                       class="module-card group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 transition group-hover:bg-amber-400 group-hover:text-[#101d30]">
                            <i class="fa-solid fa-compass text-xl"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-[#101d30]">Gestión de tours</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Catálogo, categorías y rutas</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-300 transition group-hover:text-amber-700"></i>
                    </a>

                    {{-- RESERVAS --}}
                    <a href="{{ route('admin.reservas') }}"
                       class="module-card group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-700 transition group-hover:bg-sky-600 group-hover:text-white">
                            <i class="fa-solid fa-ticket text-xl"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-[#101d30]">Reservas</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Reservaciones y participantes</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-300 transition group-hover:text-sky-700"></i>
                    </a>

                    {{-- OPERACIONES --}}
                    <a href="{{ route('admin.operaciones') }}"
                       class="module-card group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-violet-50 text-violet-700 transition group-hover:bg-violet-600 group-hover:text-white">
                            <i class="fa-solid fa-calendar-check text-xl"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-[#101d30]">Operaciones</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Salidas y coordinación</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-300 transition group-hover:text-violet-700"></i>
                    </a>

                    {{-- PERSONAL --}}
                    <a href="{{ route('admin.personal') }}"
                       class="module-card group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 transition group-hover:bg-emerald-600 group-hover:text-white">
                            <i class="fa-solid fa-users text-xl"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-[#101d30]">Personal</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Usuarios y colaboradores</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-300 transition group-hover:text-emerald-700"></i>
                    </a>

                    {{-- TRANSPORTE --}}
                    <a href="{{ route('admin.transporte') }}"
                       class="module-card group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-orange-50 text-orange-700 transition group-hover:bg-orange-500 group-hover:text-white">
                            <i class="fa-solid fa-bus text-xl"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-[#101d30]">Transporte</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Vehículos y bitácoras</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-300 transition group-hover:text-orange-700"></i>
                    </a>

                    {{-- INVENTARIO --}}
                    <a href="{{ route('admin.inventario') }}"
                       class="module-card group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 transition group-hover:bg-teal-600 group-hover:text-white">
                            <i class="fa-solid fa-boxes-stacked text-xl"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-[#101d30]">Inventario</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Equipos y materiales</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-300 transition group-hover:text-teal-700"></i>
                    </a>

                    {{-- FINANZAS --}}
                    <a href="{{ route('admin.finanzas') }}"
                       class="module-card group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-700 transition group-hover:bg-rose-600 group-hover:text-white">
                            <i class="fa-solid fa-coins text-xl"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-[#101d30]">Finanzas</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Pagos e ingresos</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-300 transition group-hover:text-rose-700"></i>
                    </a>

                    {{-- AUDITORÍA --}}
                    <a href="{{ route('admin.auditoria') }}"
                       class="module-card group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition group-hover:bg-[#101d30] group-hover:text-white">
                            <i class="fa-solid fa-shield-halved text-xl"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold text-[#101d30]">Auditoría</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Seguimiento de actividades</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-300 transition group-hover:text-slate-700"></i>
                    </a>

                </div>
            </section>

            {{-- ESTADO DE CONEXIÓN --}}
            <section class="mt-9 overflow-hidden rounded-2xl border border-amber-200/80 bg-white shadow-sm">
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:p-6">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                        <i class="fa-solid fa-database text-xl"></i>
                    </span>

                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-[#101d30]">Panel en preparación</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Los indicadores se mostrarán cuando estén conectados a las
                            tablas correspondientes de PostgreSQL. No se muestran cifras
                            de ejemplo como si fueran datos reales.
                        </p>
                    </div>

                    <span class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-amber-50 px-3 py-2 text-xs font-bold text-amber-800">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        Integración pendiente
                    </span>
                </div>
                <div class="h-1 bg-gradient-to-r from-amber-300 via-amber-500 to-[#101d30]"></div>
            </section>

            {{-- PIE DE PÁGINA --}}
            <footer class="mt-9 flex flex-col items-center justify-between gap-2 border-t border-slate-200 py-5 text-center sm:flex-row sm:text-left">
                <p class="text-xs font-medium text-slate-500">
                    <i class="fa-solid fa-mountain-sun mr-1 text-amber-700"></i>
                    Pipocas · Agencia de Viajes y Turismo
                </p>
                <p class="text-xs text-slate-400">
                    La Paz, Bolivia · Panel administrativo
                </p>
            </footer>

        </main>
    </div>
</div>

<script>
    function confirmarSalida(event) {
        event.preventDefault();

        Swal.fire({
            title: '¿Cerrar sesión?',
            text: 'Tendrás que iniciar sesión nuevamente para entrar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#101d30',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'Seguir aquí',
            reverseButtons: true,
            background: '#ffffff',
            color: '#101d30'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });

        return false;
    }

    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Correcto!',
            text: @json(session('success')),
            confirmButtonColor: '#101d30'
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Ocurrió un problema',
            text: @json(session('error')),
            confirmButtonColor: '#101d30'
        });
    @endif
</script>

</body>
</html>