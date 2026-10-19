{{--
    Layout del Motor de Reservas (Módulo 05).
    Uso en las vistas:
        @extends('layouts.reserva')
        @section('titulo', 'Elige tu salida')
        @section('hero') <x-reserva.hero ... /> @endsection                 (cabecera con imagen, opcional)
        @section('stepper') <x-reserva.stepper :actual="1" /> @endsection   (opcional; va dentro de la cabecera)
        @section('contenido') ... @endsection
        @push('scripts') <script>...</script> @endpush                      (opcional)
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1F5F8B">
    <title>@yield('titulo', 'Reserva') | Pipocas - Agencia de Viajes y Turismo</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col bg-pipocas-fondo font-cuerpo text-pipocas-texto antialiased selection:bg-pipocas-acento/60">

    {{-- Navbar --}}
    <header class="sticky top-0 z-40 border-b border-gray-200/70 bg-white/90 backdrop-blur" x-data="{ menu: false }">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:h-[70px] sm:px-6 lg:px-8">
            <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="flex items-center gap-2.5 font-extrabold text-pipocas-principal">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-pipocas-acento text-pipocas-calido shadow-sm">
                    <i class="fa-solid fa-mountain-sun"></i>
                </span>
                <span class="leading-tight">
                    <span class="block text-lg">Pipocas</span>
                    <span class="hidden text-[10px] font-semibold uppercase tracking-[0.18em] text-pipocas-texto-2 sm:block">Viajes y turismo · La Paz</span>
                </span>
            </a>

            <nav class="hidden items-center gap-1 text-sm font-semibold md:flex">
                <a href="{{ Route::has('home') ? route('home') . '#catalogo' : url('/') }}" class="rounded-xl px-3 py-2 text-pipocas-texto-2 transition hover:bg-pipocas-fondo hover:text-pipocas-principal">Tours</a>
                <a href="{{ url('/reservas/preview/consultar') }}" {{-- controlador: route('reservas.consultar') --}}
                   class="rounded-xl px-3 py-2 text-pipocas-texto-2 transition hover:bg-pipocas-fondo hover:text-pipocas-principal">Mi reserva</a>
                <a href="{{ url('/chatbot') }}" class="rounded-xl px-3 py-2 text-pipocas-texto-2 transition hover:bg-pipocas-fondo hover:text-pipocas-principal">Ayuda</a>
                <span class="mx-2 h-6 w-px bg-gray-200"></span>
                @auth
                    <span class="inline-flex items-center gap-2 rounded-xl bg-pipocas-fondo px-3 py-2 text-pipocas-texto">
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-pipocas-principal text-[11px] font-bold text-white">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                        {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}
                    </span>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-pipocas-principal px-4 py-2 text-white transition hover:bg-pipocas-hover">
                            <i class="fa-regular fa-user"></i> Iniciar sesión
                        </a>
                    @endif
                @endauth
            </nav>

            <button type="button" class="grid h-10 w-10 place-items-center rounded-xl text-pipocas-principal transition hover:bg-pipocas-fondo md:hidden"
                    @click="menu = !menu" :aria-expanded="menu" aria-label="Abrir menú">
                <i class="fa-solid" :class="menu ? 'fa-xmark' : 'fa-bars'"></i>
            </button>
        </div>

        {{-- Menú móvil --}}
        <div x-show="menu" x-cloak x-transition.origin.top class="border-t border-gray-100 bg-white md:hidden">
            <nav class="mx-auto grid max-w-6xl gap-1 px-4 py-3 text-sm font-semibold">
                <a href="{{ Route::has('home') ? route('home') . '#catalogo' : url('/') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 hover:bg-pipocas-fondo"><i class="fa-solid fa-compass w-5 text-pipocas-secundario"></i> Tours</a>
                <a href="{{ url('/reservas/preview/consultar') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 hover:bg-pipocas-fondo"><i class="fa-solid fa-ticket w-5 text-pipocas-secundario"></i> Mi reserva</a>
                <a href="{{ url('/chatbot') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 hover:bg-pipocas-fondo"><i class="fa-regular fa-comments w-5 text-pipocas-secundario"></i> Ayuda</a>
                @guest
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="rsv-btn mt-2 py-3"><i class="fa-regular fa-user"></i> Iniciar sesión</a>
                    @endif
                @endguest
            </nav>
        </div>
    </header>

    {{-- Cabecera de la página: hero con imagen (si existe) y stepper --}}
    @hasSection('hero')
        @yield('hero')
    @elseif ($__env->hasSection('stepper'))
        <div class="rsv-topo h-16 bg-pipocas-principal"></div>
    @endif

    @hasSection('stepper')
        <div class="relative z-10 mx-auto -mt-10 w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white px-4 py-4 shadow-tarjeta ring-1 ring-black/5 sm:px-6">
                @yield('stepper')
            </div>
        </div>
    @endif

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        @yield('contenido')
    </main>

    {{-- Franja de confianza --}}
    <section class="border-t border-gray-200 bg-white">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-4 px-4 py-6 text-sm sm:px-6 md:grid-cols-4 lg:px-8">
            @foreach ([
                ['fa-solid fa-shield-halved', 'Pago seguro', 'Tarjeta o QR bancario'],
                ['fa-solid fa-user-tie', 'Guías certificados', 'Expertos en altura'],
                ['fa-solid fa-qrcode', 'Ticket digital', 'Código QR por persona'],
                ['fa-solid fa-headset', 'Soporte 24/7', 'Chat y WhatsApp'],
            ] as [$ico, $tit, $sub])
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-pipocas-secundario/10 text-pipocas-secundario"><i class="{{ $ico }}"></i></span>
                    <div class="leading-tight"><p class="font-bold">{{ $tit }}</p><p class="text-xs text-pipocas-texto-2">{{ $sub }}</p></div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Footer --}}
    <footer class="rsv-topo border-t-4 border-pipocas-acento bg-pipocas-hover text-white/80">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div class="lg:col-span-2">
                <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="flex items-center gap-2.5 text-lg font-extrabold text-white">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-pipocas-acento text-pipocas-calido"><i class="fa-solid fa-mountain-sun"></i></span>
                    Pipocas
                </a>
                <p class="mt-3 max-w-sm text-sm">Tours de aventura y cultura desde La Paz: rutas georreferenciadas, guías locales y reservas en línea con ticket QR.</p>
            </div>
            <div>
                <h3 class="mb-3 font-titulo text-lg text-white">Tu reserva</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/reservas/preview/consultar') }}" class="hover:text-pipocas-acento">Consultar reserva</a></li>
                    <li><a href="{{ url('/chatbot') }}" class="hover:text-pipocas-acento">Asistente virtual</a></li>
                    <li><a href="{{ Route::has('terms.show') ? route('terms.show') : '#' }}" class="hover:text-pipocas-acento">Términos y condiciones</a></li>
                </ul>
            </div>
            <div>
                <h3 class="mb-3 font-titulo text-lg text-white">Contacto</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-location-dot w-4 text-pipocas-acento"></i> Sopocachi, La Paz</li>
                    <li class="flex items-center gap-2"><i class="fa-brands fa-whatsapp w-4 text-pipocas-acento"></i> +591 700 00000</li>
                    <li class="flex items-center gap-2"><i class="fa-regular fa-envelope w-4 text-pipocas-acento"></i> reservas@pipocas.bo</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-4 text-xs sm:flex-row sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} Pipocas · Agencia de Viajes y Turismo</p>
                <p class="flex items-center gap-3 text-lg text-white/60">
                    <i class="fa-brands fa-cc-visa"></i><i class="fa-brands fa-cc-mastercard"></i><i class="fa-solid fa-qrcode"></i>
                </p>
            </div>
        </div>
    </footer>

    {{-- Los scripts de las vistas van antes de Livewire/Alpine para que sus funciones x-data existan al iniciar Alpine --}}
    @stack('scripts')
    @livewireScripts
</body>
</html>
