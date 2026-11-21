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
    @auth<meta name="offline-account" content="{{ auth()->id() }}">@endauth
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Reserva') | Andinist Bolivia - Agencia de Viajes y Turismo</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>window.rsvText = key => (@json(app()->getLocale() === 'en' ? json_decode(file_get_contents(lang_path('en.json')), true) : (object) []))[key] ?? key;</script>
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col bg-andinist-fondo font-cuerpo text-andinist-texto antialiased">

    {{-- Navbar --}}
    <header class="bg-white shadow-[0_1px_0_rgba(0,0,0,.06)]">
        <div class="mx-auto flex min-h-[64px] max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-3 sm:min-h-[70px] sm:px-6 lg:px-8">
            <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="flex items-center gap-2.5 font-extrabold text-andinist-principal">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-andinist-acento text-andinist-calido">
                    <i class="fa-solid fa-mountain-sun"></i>
                </span>
                <span class="text-lg">Andinist Bolivia</span>
            </a>

            <nav class="flex max-w-full flex-wrap items-center justify-end gap-1 text-sm font-semibold sm:gap-2">
                <span class="flex gap-1 px-2 text-xs font-bold text-andinist-texto-2">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'es']) }}" @class(['underline text-andinist-principal' => app()->getLocale() === 'es'])>ES</a>|<a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" @class(['underline text-andinist-principal' => app()->getLocale() === 'en'])>EN</a>
                </span>
                <a href="{{ route('reservas.consultar') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-andinist-principal transition hover:bg-andinist-fondo hover:text-andinist-hover">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span class="hidden sm:inline">{{ __('Consultar reserva') }}</span>
                </a>
                @auth
                    <a href="{{ route('reservas.historial') }}">{{ __('Mis reservas') }}</a>
                    <a href="{{ auth()->user()->tieneRol('turista') ? route('turista.cuenta') : route('profile.show') }}" class="inline-flex items-center gap-2 rounded-xl bg-andinist-fondo px-3 py-2 text-andinist-texto">
                        <i class="fa-regular fa-user"></i> {{ __('Mi cuenta') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-red-700 transition hover:bg-red-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                            <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                            <span>{{ __('Cerrar sesión') }}</span>
                        </button>
                    </form>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-andinist-texto-2 transition hover:bg-andinist-fondo hover:text-andinist-principal">
                            <i class="fa-regular fa-user"></i>
                            <span class="hidden sm:inline">{{ __('Iniciar sesión') }}</span>
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
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-6 text-sm text-andinist-texto-2 sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} Andinist Bolivia · {{ __('Agencia de Viajes y Turismo · La Paz, Bolivia') }}<a class="mt-2 block text-xs underline" href="{{ route('imagenes.creditos') }}">Créditos de imágenes</a></p>
            <p class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-lock text-andinist-complementario"></i> {{ __('Pago seguro') }}</span>
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-qrcode text-andinist-secundario"></i> {{ __('Ticket QR') }}</span>
            </p>
        </div>
    </footer>

    {{-- Los scripts de las vistas van antes de Livewire/Alpine para que sus funciones x-data existan al iniciar Alpine --}}
    @stack('scripts')
    @livewireScripts
@include('partials.consentimiento-cookies')
</body>
</html>
