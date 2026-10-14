<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario | Pipocas</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        body {
            background: #f8f6f1;
        }

        .fondo-andino {
            background: linear-gradient(135deg, #172033 0%, #263b55 100%);
        }
    </style>
</head>

<body class="min-h-screen text-slate-800"
      x-data="{ sidebarMovil: false, submenu: '' }">

    {{-- FONDO DEL MENÚ MÓVIL --}}
    <div x-cloak
         x-show="sidebarMovil"
         x-transition.opacity
         @click="sidebarMovil = false"
         class="fixed inset-0 z-40 bg-slate-950/60 lg:hidden">
    </div>

    <div class="min-h-screen lg:flex">

        {{-- MENÚ LATERAL --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-950 text-white transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
            :class="sidebarMovil ? 'translate-x-0' : '-translate-x-full'">

            <div class="flex items-center justify-between border-b border-white/10 px-6 py-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-400 text-slate-950">
                        <i class="fa-solid fa-mountain-sun text-xl"></i>
                    </span>
                    <span>
                        <span class="block text-lg font-black tracking-wide">Pipocas</span>
                        <span class="block text-xs text-slate-400">Administración</span>
                    </span>
                </a>

                <button @click="sidebarMovil = false"
                        class="text-slate-400 hover:text-white lg:hidden"
                        aria-label="Cerrar menú">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="px-5 py-5">
                <p class="mb-3 px-3 text-xs font-bold uppercase tracking-widest text-slate-500">
                    Menú principal
                </p>

                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-house w-5"></i>
                        Dashboard
                    </a>

                    <button @click="submenu = submenu === 'tours' ? '' : 'tours'"
                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-map-location-dot w-5"></i>
                            Tours
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs transition"
                           :class="submenu === 'tours' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-cloak x-show="submenu === 'tours'"
                         class="ml-5 space-y-1 border-l border-white/10 pl-4">
                        <a href="{{ route('admin.tours') }}"
                           class="block rounded-lg px-3 py-2 text-sm text-slate-400 hover:bg-white/10 hover:text-white">
                            Catálogo y categorías
                        </a>
                    </div>

                    <a href="{{ route('admin.operaciones') }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-calendar-check w-5"></i>
                        Operaciones
                    </a>

                    <a href="{{ route('admin.reservas') }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-ticket w-5"></i>
                        Reservas
                    </a>

                    <a href="{{ route('admin.personal') }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-users w-5"></i>
                        Personal
                    </a>

                    <a href="{{ route('admin.transporte') }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-van-shuttle w-5"></i>
                        Transporte
                    </a>

                    <a href="{{ route('admin.inventario') }}"
                       class="flex items-center gap-3 rounded-xl bg-amber-400 px-4 py-3 font-semibold text-slate-950 shadow-lg shadow-amber-950/20">
                        <i class="fa-solid fa-boxes-stacked w-5"></i>
                        Inventario
                    </a>

                    <a href="{{ route('admin.finanzas') }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-wallet w-5"></i>
                        Finanzas
                    </a>

                    <a href="{{ route('admin.auditoria') }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-shield-halved w-5"></i>
                        Auditoría
                    </a>
                </nav>
            </div>

            <div class="mt-auto border-t border-white/10 p-5">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 text-amber-400">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold">
                            {{ auth()->user()->nom ?? auth()->user()->name ?? 'Usuario' }}
                        </p>
                        <p class="text-xs text-slate-400">Panel administrativo</p>
                    </div>
                </div>

                <form id="formCerrarSesion" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-rose-300 transition hover:bg-rose-500/10 hover:text-rose-200">
                        <i class="fa-solid fa-right-from-bracket w-5"></i>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- CONTENIDO PRINCIPAL --}}
        <div class="min-w-0 flex-1">

            {{-- BARRA SUPERIOR --}}
            <header class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 bg-white/95 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button @click="sidebarMovil = true"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 lg:hidden"
                            aria-label="Abrir menú">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Administración
                        </p>
                        <h2 class="font-bold text-slate-800">Inventario</h2>
                    </div>
                </div>

                <a href="{{ route('dashboard') }}"
                   class="hidden items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 sm:flex">
                    <i class="fa-solid fa-house"></i>
                    Dashboard
                </a>
            </header>

            <main class="mx-auto max-w-7xl space-y-7 p-4 sm:p-6 lg:p-8">

                {{-- ENCABEZADO --}}
                <section class="fondo-andino relative overflow-hidden rounded-3xl p-6 text-white shadow-xl sm:p-9">
                    <div class="pointer-events-none absolute -right-10 -top-16 h-56 w-56 rounded-full border-[30px] border-white/5"></div>
                    <div class="pointer-events-none absolute -bottom-20 right-36 h-52 w-52 rounded-full bg-amber-400/10 blur-2xl"></div>

                    <div class="relative z-10 max-w-3xl">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-amber-300">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            Equipamiento turístico
                        </span>

                        <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl">
                            Control de inventario
                        </h1>

                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                            Organiza los equipos y materiales utilizados en los tours,
                            consulta las existencias y prepara el análisis de reposición.
                        </p>

                        <a href="#secciones-inventario"
                           class="mt-6 inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-amber-300">
                            Explorar inventario
                            <i class="fa-solid fa-arrow-down"></i>
                        </a>
                    </div>
                </section>

                {{-- INDICADORES --}}
                <section>
                    <div class="mb-4">
                        <h2 class="text-lg font-black text-slate-800">Resumen de inventario</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Indicadores pendientes de conexión con los registros reales.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-500">Tipos de equipamiento</span>
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                                    <i class="fa-solid fa-bicycle text-lg"></i>
                                </span>
                            </div>
                            <p class="mt-4 text-3xl font-black">—</p>
                            <p class="mt-1 text-xs text-slate-400">Pendiente de conexión</p>
                        </article>

                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-500">Unidades disponibles</span>
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-box-open text-lg"></i>
                                </span>
                            </div>
                            <p class="mt-4 text-3xl font-black">—</p>
                            <p class="mt-1 text-xs text-slate-400">Según existencias registradas</p>
                        </article>

                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-500">Equipos asignados</span>
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                                    <i class="fa-solid fa-people-carry-box text-lg"></i>
                                </span>
                            </div>
                            <p class="mt-4 text-3xl font-black">—</p>
                            <p class="mt-1 text-xs text-slate-400">Pendiente de conexión</p>
                        </article>

                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-500">Reposición pendiente</span>
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                                </span>
                            </div>
                            <p class="mt-4 text-3xl font-black">—</p>
                            <p class="mt-1 text-xs text-slate-400">Sin cálculo hasta conectar datos</p>
                        </article>

                    </div>
                </section>

                {{-- SECCIONES --}}
                <section id="secciones-inventario">
                    <div class="mb-5">
                        <h2 class="text-lg font-black text-slate-800">Administración de recursos</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Secciones previstas para gestionar el equipamiento turístico.
                        </p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

                        <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-700 transition group-hover:bg-amber-400 group-hover:text-slate-950">
                                <i class="fa-solid fa-bicycle text-xl"></i>
                            </div>
                            <h3 class="mt-5 text-lg font-bold">Equipamiento</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Consulta los equipos utilizados en las actividades turísticas y sus características registradas.
                            </p>
                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Pendiente de conexión
                                </span>
                            </div>
                        </article>

                        <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition group-hover:bg-amber-400 group-hover:text-slate-950">
                                <i class="fa-solid fa-boxes-stacked text-xl"></i>
                            </div>
                            <h3 class="mt-5 text-lg font-bold">Existencias</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Prepara la consulta de cantidades disponibles, unidades utilizadas y existencias registradas.
                            </p>
                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Pendiente de conexión
                                </span>
                            </div>
                        </article>

                        <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-700 transition group-hover:bg-amber-400 group-hover:text-slate-950">
                                <i class="fa-solid fa-calculator text-xl"></i>
                            </div>
                            <h3 class="mt-5 text-lg font-bold">Modelo EOQ</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Espacio para estimar la cantidad económica de pedido cuando existan datos de demanda y costos.
                            </p>
                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Pendiente de implementación
                                </span>
                            </div>
                        </article>

                        <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition group-hover:bg-amber-400 group-hover:text-slate-950">
                                <i class="fa-solid fa-clipboard-check text-xl"></i>
                            </div>
                            <h3 class="mt-5 text-lg font-bold">Asignación de equipos</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Prepara el seguimiento de los equipos entregados o reservados para las actividades turísticas.
                            </p>
                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Pendiente de conexión
                                </span>
                            </div>
                        </article>

                        <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-700 transition group-hover:bg-amber-400 group-hover:text-slate-950">
                                <i class="fa-solid fa-arrow-rotate-left text-xl"></i>
                            </div>
                            <h3 class="mt-5 text-lg font-bold">Devoluciones</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Organiza la consulta de devoluciones y del estado de los equipos, si estas operaciones están contempladas.
                            </p>
                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Pendiente de conexión
                                </span>
                            </div>
                        </article>

                        <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700 transition group-hover:bg-amber-400 group-hover:text-slate-950">
                                <i class="fa-solid fa-chart-line text-xl"></i>
                            </div>
                            <h3 class="mt-5 text-lg font-bold">Análisis de reposición</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Prepara indicadores de consumo y reposición para ayudar a planificar las compras de equipamiento.
                            </p>
                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Pendiente de implementación
                                </span>
                            </div>
                        </article>

                    </div>
                </section>

                {{-- INFORMACIÓN DEL MODELO EOQ --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <i class="fa-solid fa-calculator text-xl"></i>
                        </div>

                        <div class="min-w-0">
                            <h2 class="text-lg font-black">Modelo EOQ: cantidad económica de pedido</h2>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                El modelo EOQ permite estimar cuánto pedir para equilibrar los costos
                                de realizar pedidos y mantener inventario. Para calcularlo se necesitan
                                datos reales de demanda, costo por pedido y costo de almacenamiento.
                            </p>

                            <div class="mt-4 rounded-xl bg-slate-50 p-4">
                                <p class="text-sm font-semibold text-slate-700">Fórmula de referencia</p>
                                <p class="mt-2 text-lg font-bold text-slate-800">
                                    EOQ = √(2DS / H)
                                </p>
                                <ul class="mt-3 space-y-1 text-sm text-slate-500">
                                    <li><strong>D:</strong> demanda del período.</li>
                                    <li><strong>S:</strong> costo de realizar un pedido.</li>
                                    <li><strong>H:</strong> costo de mantener una unidad en inventario durante el período.</li>
                                </ul>
                            </div>

                            <p class="mt-3 text-xs text-slate-400">
                                No se muestran resultados calculados porque todavía no se han conectado los datos necesarios.
                            </p>
                        </div>
                    </div>
                </section>

                {{-- AVISO --}}
                <section class="flex items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-amber-950">Integración con PostgreSQL</h3>
                        <p class="mt-1 text-sm leading-6 text-amber-900/80">
                            Esta pantalla es visual. Antes de implementar registros, entradas,
                            salidas, asignaciones o cálculos automáticos, verificaremos las tablas
                            y relaciones reales de tu base de datos para respetar su estructura.
                        </p>
                    </div>
                </section>

                <footer class="border-t border-slate-200 py-5 text-center text-xs text-slate-400">
                    Pipocas · Agencia de Viajes y Turismo · Panel administrativo
                </footer>

            </main>
        </div>
    </div>

    {{-- ALERTAS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formulario = document.getElementById('formCerrarSesion');

            if (formulario) {
                formulario.addEventListener('submit', function (event) {
                    event.preventDefault();

                    Swal.fire({
                        title: '¿Cerrar sesión?',
                        text: 'Tendrás que iniciar sesión nuevamente para ingresar.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#d97706',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'Sí, cerrar sesión',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((resultado) => {
                        if (resultado.isConfirmed) {
                            formulario.submit();
                        }
                    });
                });
            }

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Correcto',
                    text: @json(session('success')),
                    confirmButtonColor: '#d97706'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Ocurrió un problema',
                    text: @json(session('error')),
                    confirmButtonColor: '#d97706'
                });
            @endif
        });
    </script>
</body>
</html>