{{--
    Layout de los paneles de Investigación Operativa II (Módulo 07).
    Uso en las vistas:
        @extends('layouts.admin-io')
        @section('titulo', 'Análisis de colas de reservas')
        @section('subtitulo', 'Modelo M/M/c/K ...')            (opcional)
        @section('acciones') ...botones del encabezado... @endsection  (opcional)
        @section('contenido') ... @endsection
        @push('scripts') <script>...</script> @endpush           (opcional)
    Variables opcionales: $demo (bool) muestra el aviso de datos de demostración.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Investigación Operativa II') | Pipocas Admin</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
</head>
<body class="min-h-screen bg-pipocas-fondo font-cuerpo text-pipocas-texto antialiased" x-data="{ menu: false }">

    @php
        $base = request()->is('admin/io/preview*') ? 'admin/io/preview' : 'admin/io';
        $rutaColas = url("$base/colas");
        $rutaCiclo = url("$base/ciclo-vida");
        $enlaces = [
            ['url' => url('/admin/dashboard'), 'icono' => 'fa-chart-pie', 'texto' => 'Dashboard', 'activo' => false],
            ['url' => $rutaColas, 'icono' => 'fa-people-line', 'texto' => 'Análisis de colas', 'activo' => request()->is('admin/io/colas*', 'admin/io/preview/colas*')],
            ['url' => $rutaCiclo, 'icono' => 'fa-arrows-spin', 'texto' => 'Ciclo de vida del cliente', 'activo' => request()->is('admin/io/ciclo-vida*', 'admin/io/preview/ciclo-vida*')],
        ];
    @endphp

    {{-- Fondo del menú en móvil --}}
    <div x-cloak x-show="menu" x-transition.opacity @click="menu = false" class="fixed inset-0 z-40 bg-pipocas-hover/60 lg:hidden"></div>

    <div class="min-h-screen lg:flex">
        {{-- Menú lateral --}}
        <aside class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-pipocas-hover text-white transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
               :class="menu ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex items-center justify-between border-b border-white/10 px-6 py-6">
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-pipocas-acento text-pipocas-calido">
                        <i class="fa-solid fa-mountain-sun text-xl"></i>
                    </span>
                    <span>
                        <span class="block text-lg font-extrabold">Pipocas</span>
                        <span class="block text-xs text-white/60">Administración</span>
                    </span>
                </a>
                <button @click="menu = false" class="text-white/60 hover:text-white lg:hidden" aria-label="Cerrar menú">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 space-y-1 px-4 py-6 text-sm font-semibold">
                <p class="mb-3 px-3 text-[11px] font-bold uppercase tracking-widest text-white/40">Investigación Operativa II</p>
                @foreach ($enlaces as $e)
                    <a href="{{ $e['url'] }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 transition {{ $e['activo'] ? 'bg-pipocas-acento text-pipocas-calido shadow-lg' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                        <i class="fa-solid {{ $e['icono'] }} w-5"></i> {{ $e['texto'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-4 text-xs text-white/50">
                <p class="px-3">Modelos: M/M/c/K · Cadena de Markov absorbente · EOQ</p>
                <a href="{{ url('/') }}" class="mt-3 flex items-center gap-3 rounded-xl px-3 py-2 font-semibold text-pipocas-acento hover:bg-white/10">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Volver al portal
                </a>
            </div>
        </aside>

        {{-- Contenido --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-black/5 bg-white/90 backdrop-blur">
                <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-8">
                    <div class="flex items-center gap-3">
                        <button @click="menu = true" class="grid h-10 w-10 place-items-center rounded-xl bg-pipocas-fondo text-pipocas-principal lg:hidden" aria-label="Abrir menú">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div>
                            <h1 class="font-titulo text-2xl font-semibold text-pipocas-principal sm:text-[28px]">@yield('titulo')</h1>
                            @hasSection('subtitulo')
                                <p class="text-sm text-pipocas-texto-2">@yield('subtitulo')</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">@yield('acciones')</div>
                </div>
            </header>

            <main class="flex-1 space-y-6 px-4 py-6 sm:px-8 sm:py-8">
                @if (!empty($demo))
                    <div class="flex items-start gap-3 rounded-2xl border border-pipocas-acento/60 bg-pipocas-acento/15 px-4 py-3 text-sm text-pipocas-calido">
                        <i class="fa-solid fa-flask mt-0.5"></i>
                        <p><strong>Datos de demostración.</strong> {{ $avisoDemo ?? 'Los valores provienen de los parámetros iniciales del seeder; se reemplazan por los datos reales al registrar llegadas y cambios de estado.' }}</p>
                    </div>
                @endif

                @yield('contenido')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
