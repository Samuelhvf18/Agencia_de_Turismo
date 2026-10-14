<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gestión de Tours | Pipocas Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body
    class="min-h-screen bg-slate-100 text-slate-800 antialiased"
    x-data="{ sidebarMovil: false, submenu: 'tours' }"
>

<div class="min-h-screen md:flex">

    {{-- FONDO OSCURO PARA EL MENÚ MÓVIL --}}
    <div
        x-cloak
        x-show="sidebarMovil"
        x-transition.opacity
        @click="sidebarMovil = false"
        class="fixed inset-0 z-40 bg-slate-950/60 md:hidden"
    ></div>

    {{-- MENÚ LATERAL --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] flex-col
               bg-slate-950 text-slate-300 shadow-2xl
               transition-transform duration-300
               md:sticky md:top-0 md:h-screen md:w-64 md:max-w-none
               md:shrink-0 md:translate-x-0 md:shadow-none
               xl:w-72"
        :class="sidebarMovil ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    >

        {{-- MARCA --}}
        <div class="flex items-center justify-between border-b border-slate-800 px-5 py-5">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-slate-950">
                    <i class="fa-solid fa-mountain-sun text-xl"></i>
                </span>

                <span class="min-w-0">
                    <span class="block text-xl font-black text-white">
                        Pipocas <span class="text-amber-400">Admin</span>
                    </span>
                    <span class="block text-xs text-slate-400">
                        Agencia de viajes y turismo
                    </span>
                </span>
            </a>

            <button
                type="button"
                @click="sidebarMovil = false"
                class="ml-2 rounded-lg p-2 text-slate-400 hover:bg-slate-800 md:hidden"
                aria-label="Cerrar menú"
            >
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        {{-- NAVEGACIÓN --}}
        <nav class="flex-1 space-y-2 overflow-y-auto p-4 text-sm">

            <p class="mb-3 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">
                Menú principal
            </p>

            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-800 hover:text-white"
            >
                <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                Dashboard
            </a>

            {{-- TOURS --}}
            <div>
                <button
                    type="button"
                    @click="submenu = submenu === 'tours' ? '' : 'tours'"
                    class="flex w-full items-center gap-3 rounded-xl bg-amber-500 px-4 py-3 font-bold text-slate-950"
                    :aria-expanded="submenu === 'tours'"
                >
                    <i class="fa-solid fa-map-location-dot w-5 text-center"></i>
                    <span class="flex-1 text-left">Gestión de tours</span>
                    <i
                        class="fa-solid fa-chevron-down text-xs transition-transform"
                        :class="submenu === 'tours' ? 'rotate-180' : ''"
                    ></i>
                </button>

                <div
                    x-cloak
                    x-show="submenu === 'tours'"
                    x-transition
                    class="ml-6 mt-2 space-y-1 border-l border-slate-700 pl-4"
                >
                    <a
                        href="{{ route('admin.tours') }}"
                        @click="sidebarMovil = false"
                        aria-current="page"
                        class="flex items-center gap-3 rounded-lg bg-slate-800 px-3 py-2.5 font-semibold text-amber-300"
                    >
                        <i class="fa-solid fa-list w-4 text-center"></i>
                        Catálogo de tours
                    </a>

                    <a
                        href="{{ route('admin.tours') }}"
                        @click="sidebarMovil = false"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-slate-800 hover:text-white"
                    >
                        <i class="fa-solid fa-layer-group w-4 text-center"></i>
                        Categorías
                    </a>

                    <a
                        href="{{ route('admin.tours') }}"
                        @click="sidebarMovil = false"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-slate-800 hover:text-white"
                    >
                        <i class="fa-solid fa-route w-4 text-center"></i>
                        Rutas y puntos
                    </a>
                </div>
            </div>

            <p class="mb-3 mt-7 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">
                Gestión operativa
            </p>

            <a href="{{ route('admin.operaciones') }}"
               @click="sidebarMovil = false"
               class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-800 hover:text-white">
                <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                Operaciones y salidas
            </a>

            <a href="{{ route('admin.reservas') }}"
               @click="sidebarMovil = false"
               class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-800 hover:text-white">
                <i class="fa-solid fa-ticket w-5 text-center"></i>
                Reservas
            </a>

            <a href="{{ route('admin.personal') }}"
               @click="sidebarMovil = false"
               class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-800 hover:text-white">
                <i class="fa-solid fa-users w-5 text-center"></i>
                Personal
            </a>

            <a href="{{ route('admin.transporte') }}"
               @click="sidebarMovil = false"
               class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-800 hover:text-white">
                <i class="fa-solid fa-bus w-5 text-center"></i>
                Transporte
            </a>

            <a href="{{ route('admin.inventario') }}"
               @click="sidebarMovil = false"
               class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-800 hover:text-white">
                <i class="fa-solid fa-boxes-stacked w-5 text-center"></i>
                Inventario
            </a>

            <p class="mb-3 mt-7 px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">
                Administración
            </p>

            <a href="{{ route('admin.finanzas') }}"
               @click="sidebarMovil = false"
               class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-800 hover:text-white">
                <i class="fa-solid fa-coins w-5 text-center"></i>
                Finanzas
            </a>

            <a href="{{ route('admin.auditoria') }}"
               @click="sidebarMovil = false"
               class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-800 hover:text-white">
                <i class="fa-solid fa-shield-halved w-5 text-center"></i>
                Auditoría
            </a>
        </nav>

        {{-- USUARIO Y SESIÓN --}}
        <div class="border-t border-slate-800 p-4">
            <div class="mb-3 flex items-center gap-3 px-2">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-500 font-black text-slate-950">
                    {{ strtoupper(substr(auth()->user()->nom ?? auth()->user()->name ?? 'U', 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold text-white">
                        {{ auth()->user()->nom ?? auth()->user()->name ?? 'Usuario' }}
                    </p>
                    <p class="text-xs text-slate-400">Sesión iniciada</p>
                </div>
            </div>

            <form id="formCerrarSesion" method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full rounded-xl px-4 py-3 text-left text-rose-300 transition hover:bg-slate-800"
                >
                    <i class="fa-solid fa-right-from-bracket mr-2"></i>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- CONTENIDO --}}
    <div class="flex min-h-screen min-w-0 flex-1 flex-col">

        {{-- BARRA SUPERIOR --}}
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-3">

                <div class="flex min-w-0 items-center gap-3">
                    <button
                        type="button"
                        @click="sidebarMovil = true"
                        class="shrink-0 rounded-xl border border-slate-200 px-3 py-2 text-slate-700 transition hover:bg-slate-100 md:hidden"
                        aria-label="Abrir menú"
                    >
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <div class="min-w-0">
                        <p class="hidden text-xs text-slate-500 sm:block">
                            Pipocas / Administración / Tours
                        </p>
                        <h1 class="truncate text-xl font-black text-slate-900 sm:mt-1 sm:text-2xl">
                            Gestión de tours
                        </h1>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <span class="hidden rounded-full bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-800 sm:inline-flex">
                        <i class="fa-solid fa-location-dot mr-2"></i>
                        La Paz, Bolivia
                    </span>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 font-black text-amber-400">
                        {{ strtoupper(substr(auth()->user()->nom ?? auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">

            {{-- ENCABEZADO --}}
            <section class="relative overflow-hidden rounded-2xl bg-slate-900 p-6 text-white sm:p-8">
                <div class="relative z-10 max-w-2xl">
                    <span class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-400/10 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-300">
                        <i class="fa-solid fa-compass"></i>
                        Administración turística
                    </span>

                    <h2 class="mt-4 text-2xl font-black sm:text-3xl">
                        Gestiona la oferta turística
                    </h2>

                    <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300 sm:text-base">
                        Organiza los tours de Pipocas, sus categorías y los recorridos
                        turísticos desde el panel administrativo.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a
                            href="#seccionesTours"
                            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-3 text-sm font-bold text-slate-950 transition hover:bg-amber-400"
                        >
                            <i class="fa-solid fa-layer-group"></i>
                            Explorar secciones
                        </a>

                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            <i class="fa-solid fa-arrow-left"></i>
                            Volver al dashboard
                        </a>
                    </div>
                </div>

                <i class="fa-solid fa-mountain-sun pointer-events-none absolute -bottom-8 -right-4 text-8xl text-white opacity-5 sm:text-[190px]"></i>
            </section>

            {{-- ESTADO GENERAL --}}
            <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                <article class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i class="fa-solid fa-map-location-dot text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Catálogo de tours</p>
                        <p class="mt-1 text-sm leading-5 text-slate-500">
                            Información de las experiencias y recorridos disponibles.
                        </p>
                        <span class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                            <i class="fa-solid fa-database"></i>
                            Pendiente de conexión
                        </span>
                    </div>
                </article>

                <article class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                        <i class="fa-solid fa-layer-group text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Categorías</p>
                        <p class="mt-1 text-sm leading-5 text-slate-500">
                            Clasificación de las experiencias por tipo de turismo.
                        </p>
                        <span class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                            <i class="fa-solid fa-database"></i>
                            Pendiente de conexión
                        </span>
                    </div>
                </article>

                <article class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-700">
                        <i class="fa-solid fa-route text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Rutas y puntos de interés</p>
                        <p class="mt-1 text-sm leading-5 text-slate-500">
                            Recorridos, paradas y lugares turísticos asociados.
                        </p>
                        <span class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                            <i class="fa-solid fa-database"></i>
                            Pendiente de conexión
                        </span>
                    </div>
                </article>
            </section>

            {{-- SECCIONES DEL MÓDULO --}}
            <section id="seccionesTours" class="mt-8 scroll-mt-24">
                <div class="mb-5">
                    <h2 class="text-lg font-black text-slate-900 sm:text-xl">
                        Secciones del módulo
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Accede a las áreas de administración turística.
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

                    {{-- CATÁLOGO --}}
                    <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                                <i class="fa-solid fa-map-location-dot text-xl"></i>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-500">
                                CATÁLOGO
                            </span>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-slate-900">
                            Catálogo de tours
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Área destinada a administrar nombres, descripciones,
                            precios, duración y demás datos definidos para cada tour.
                        </p>

                        <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                            <i class="fa-solid fa-circle-info text-amber-600"></i>
                            Preparado para integrar con PostgreSQL
                        </div>
                    </article>

                    {{-- CATEGORÍAS --}}
                    <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-sky-300 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                                <i class="fa-solid fa-layer-group text-xl"></i>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-500">
                                CLASIFICACIÓN
                            </span>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-slate-900">
                            Categorías
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Área destinada a organizar los tours mediante las
                            categorías registradas en el sistema.
                        </p>

                        <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                            <i class="fa-solid fa-circle-info text-sky-600"></i>
                            Pendiente de verificar relaciones
                        </div>
                    </article>

                    {{-- RUTAS --}}
                    <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-orange-300 hover:shadow-md md:col-span-2 xl:col-span-1">
                        <div class="flex items-center justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-orange-700">
                                <i class="fa-solid fa-route text-xl"></i>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-500">
                                RECORRIDOS
                            </span>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-slate-900">
                            Rutas y puntos de interés
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Área destinada a organizar los recorridos y los puntos
                            turísticos relacionados con cada experiencia.
                        </p>

                        <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                            <i class="fa-solid fa-circle-info text-orange-600"></i>
                            Pendiente de conexión
                        </div>
                    </article>
                </div>
            </section>

            {{-- AVISO ADMINISTRATIVO --}}
            <section class="mt-8 flex items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <i class="fa-solid fa-database"></i>
                </div>

                <div>
                    <h3 class="font-bold text-amber-950">
                        Integración con la base de datos
                    </h3>
                    <p class="mt-1 text-sm leading-6 text-amber-900/80">
                        Esta vista es la interfaz administrativa del módulo. Antes de
                        implementar altas, modificaciones, eliminaciones y filtros,
                        verificaremos las tablas y relaciones reales de PostgreSQL
                        para evitar utilizar columnas inexistentes.
                    </p>
                </div>
            </section>

        </main>

        <footer class="border-t border-slate-200 bg-white px-4 py-5 text-center text-xs text-slate-400 sm:px-6">
            Pipocas — Agencia de Viajes y Turismo · La Paz, Bolivia
        </footer>
    </div>
</div>

{{-- SWEETALERT2: CIERRE DE SESIÓN Y MENSAJES --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formulario = document.getElementById('formCerrarSesion');

        if (formulario) {
            formulario.addEventListener('submit', async function (event) {
                event.preventDefault();

                const resultado = await Swal.fire({
                    title: '¿Cerrar sesión?',
                    text: 'Tendrás que iniciar sesión nuevamente para entrar al panel.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#475569',
                    confirmButtonText: 'Sí, cerrar sesión',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                });

                if (resultado.isConfirmed) {
                    formulario.submit();
                }
            });
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Operación exitosa',
                text: @json(session('success')),
                confirmButtonColor: '#d97706'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'No se pudo completar',
                text: @json(session('error')),
                confirmButtonColor: '#d97706'
            });
        @endif
    });
</script>

</body>
</html>