{{--
    Layout del Motor de Reservas (Módulo 05).
    Uso en las vistas:
        @extends('layouts.reserva')
        @section('titulo', 'Elige tu salida')
        @section('stepper') <x-reserva.stepper :actual="1" /> @endsection   (opcional)
        @section('contenido') ... @endsection
        @push('scripts') <script>...</script> @endpush                      (opcional)
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Reserva') | Pipocas - Agencia de Viajes y Turismo</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col bg-pipocas-fondo font-cuerpo text-pipocas-texto antialiased">

    {{-- Navbar --}}
    <header class="bg-white shadow-[0_1px_0_rgba(0,0,0,.06)]">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:h-[70px] sm:px-6 lg:px-8">
            <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="flex items-center gap-2.5 font-extrabold text-pipocas-principal">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-pipocas-acento text-pipocas-calido">
                    <i class="fa-solid fa-mountain-sun"></i>
                </span>
                <span class="text-lg">Pipocas</span>
            </a>

            <nav class="flex items-center gap-1 text-sm font-semibold sm:gap-2">
                <a href="{{ url('/reservas/preview/consultar') }}" {{-- controlador: route('reservas.consultar') --}}
                   class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-pipocas-principal transition hover:bg-pipocas-fondo hover:text-pipocas-hover">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span class="hidden sm:inline">Consultar reserva</span>
                </a>
                @auth
                    <span class="hidden items-center gap-2 rounded-xl bg-pipocas-fondo px-3 py-2 text-pipocas-texto md:inline-flex">
                        <i class="fa-regular fa-user"></i> {{ auth()->user()->name }}
                    </span>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-pipocas-texto-2 transition hover:bg-pipocas-fondo hover:text-pipocas-principal">
                            <i class="fa-regular fa-user"></i>
                            <span class="hidden sm:inline">Iniciar sesión</span>
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    {{-- Stepper (solo en los pasos del flujo) --}}
    @hasSection('stepper')
        <div class="border-b border-gray-200/70 bg-white/60">
            <div class="mx-auto max-w-6xl px-4 py-4 sm:px-6 lg:px-8">
                @yield('stepper')
            </div>
        </div>
    @endif

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:px-6 sm:py-10 lg:px-8">
        @yield('contenido')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-6 text-sm text-pipocas-texto-2 sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} Pipocas · Agencia de Viajes y Turismo · La Paz, Bolivia</p>
            <p class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-lock text-pipocas-complementario"></i> Pago seguro</span>
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-qrcode text-pipocas-secundario"></i> Ticket QR</span>
            </p>
        </div>
    </footer>

    {{-- Los scripts de las vistas van antes de Livewire/Alpine para que sus funciones x-data existan al iniciar Alpine --}}
    @stack('scripts')
    @livewireScripts
</body>
</html>
