<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de turista') | Pipocas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        h1, h2, h3, .font-display {
            font-family: 'Fraunces', serif;
        }
        .tourist-shell {
            background:
                radial-gradient(
                    ellipse at 85% 0%,
                    rgba(69,118,153,.08),
                    transparent 36%
                ),
                var(--fondo);
        }
        .tourist-sidebar {
            background: linear-gradient(
                165deg,
                #1F5F8B 0%,
                #174870 100%
            );
            box-shadow: 10px 0 40px rgba(23,72,112,.10);
        }
        .tourist-brand-icon {
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
        .tourist-link {
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
            transition:
                background .2s ease,
                color .2s ease,
                transform .2s ease,
                border-color .2s ease;
        }
        .tourist-link i {
            width: 17px;
            text-align: center;
            font-size: 14px;
            opacity: .9;
        }
        .tourist-link:hover {
            background: rgba(255,255,255,.09);
            border-color: rgba(255,255,255,.08);
            color: #fff;
            transform: translateX(2px);
        }
        .tourist-link.active {
            color: #fff;
            background: linear-gradient(
                110deg,
                #C96F4A,
                #b85c3b
            );
            border-color: rgba(255,255,255,.12);
            box-shadow: 0 6px 16px rgba(10,35,55,.18);
        }
        .tourist-link.active i {
            color: var(--acento);
        }
        .tourist-logout {
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
        .tourist-logout:hover {
            background: var(--secundario);
        }
        .tourist-topbar {
            background: rgba(255,255,255,.88);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(31,95,139,.10);
        }
        .tourist-card {
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(31,95,139,.10);
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(31,58,80,.035);
            transition: box-shadow .25s ease;
        }
        .tourist-card:hover {
            box-shadow: 0 12px 30px rgba(31,58,80,.075);
        }
        .page-title {
            color: var(--hover);
            font-family: 'Fraunces', serif;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -.025em;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 18px;
            border-radius: 10px;
            background: var(--principal);
            color: white;
            font-weight: 700;
            transition: .2s ease;
        }
        .btn-primary:hover {
            background: var(--hover);
        }
        .field {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #DDE3E8;
            border-radius: 11px;
            background: white;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .field:focus {
            border-color: var(--complementario);
            box-shadow: 0 0 0 3px rgba(69,118,153,.13);
        }
        .tourist-mobile-link {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 13px;
            border-radius: 12px;
            color: var(--complementario);
            background: rgba(31,95,139,.05);
            border: 1px solid rgba(31,95,139,.08);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            transition: .2s ease;
        }
        .tourist-mobile-link:hover {
            background: rgba(31,95,139,.10);
        }
        .tourist-mobile-link.active {
            color: white;
            background: linear-gradient(
                110deg,
                #C96F4A,
                #b85c3b
            );
            border-color: rgba(201,111,74,.2);
        }
        .tourist-mobile-link.active i {
            color: var(--acento);
        }
        @media (max-width: 767px) {
            .page-title {
                font-size: 1.6rem;
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
    <div class="tourist-shell min-h-screen flex">
        <aside class="tourist-sidebar hidden md:flex flex-col justify-between w-64 min-h-screen text-white">
            <div class="p-5">
                <a href="{{ route('turista.dashboard') }}"
                   class="flex items-center gap-3 pb-5 border-b border-white/10">
                    <div class="tourist-brand-icon">
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
                </a>
                <div class="mt-6 mb-3 px-3">
                    <p class="text-[9px] uppercase tracking-[.2em] font-bold text-white/45">
                        Mi espacio
                    </p>
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('turista.dashboard') }}"
                       class="tourist-link {{ request()->routeIs('turista.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house"></i>
                        <span>Inicio</span>
                    </a>
                    <a href="{{ route('turista.tours.index') }}"
                       class="tourist-link {{ request()->routeIs('turista.tours.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-compass"></i>
                        <span>Explorar tours</span>
                    </a>
                    <a href="{{ route('turista.reservas.index') }}"
                       class="tourist-link {{ request()->routeIs('turista.reservas.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-ticket"></i>
                        <span>Mis reservas</span>
                    </a>
                    <div class="mt-7 mb-3 px-3">
                        <p class="text-[9px] uppercase tracking-[.2em] font-bold text-white/45">
                            Mi cuenta
                        </p>
                    </div>
                    <a href="{{ route('turista.perfil') }}"
                       class="tourist-link {{ request()->routeIs('turista.perfil') ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i>
                        <span>Mi perfil</span>
                    </a>
                </nav>
            </div>
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 mb-4 px-1">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-[#F4C95D]">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-white truncate">
                            {{ auth()->user()->nom }}
                        </p>
                        <p class="text-[10px] text-white/55 truncate">
                            {{ auth()->user()->cor }}
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="tourist-logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Cerrar sesión
                    </button>
                </form>
                <p class="text-center text-[9px] text-white/35 mt-4">
                    © {{ date('Y') }} Pipocas · Turismo
                </p>
            </div>
        </aside>
        <div class="flex-1 flex flex-col min-w-0 w-full">
            <header class="tourist-topbar min-h-16 px-5 md:px-8 py-4 sticky top-0 z-10">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[9px] sm:text-[10px] font-bold uppercase tracking-[.16em] text-[#457699] mb-0.5">
                            Pipocas · Mi espacio
                        </p>
                        <p class="font-bold text-[#343A40] truncate">
                            Bienvenido, {{ auth()->user()->nom }}
                        </p>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 rounded-full bg-[#1F5F8B]/5 border border-[#1F5F8B]/10 px-3 py-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-[10px] font-bold text-[#457699]">
                            Panel de turista
                        </span>
                    </div>
                </div>
                <nav class="flex md:hidden gap-2 overflow-x-auto pt-4 pb-1">
                    <a href="{{ route('turista.dashboard') }}"
                       class="tourist-mobile-link {{ request()->routeIs('turista.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house"></i>
                        Inicio
                    </a>
                    <a href="{{ route('turista.tours.index') }}"
                       class="tourist-mobile-link {{ request()->routeIs('turista.tours.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-compass"></i>
                        Tours
                    </a>
                    <a href="{{ route('turista.reservas.index') }}"
                       class="tourist-mobile-link {{ request()->routeIs('turista.reservas.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-ticket"></i>
                        Reservas
                    </a>
                    <a href="{{ route('turista.perfil') }}"
                       class="tourist-mobile-link {{ request()->routeIs('turista.perfil') ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i>
                        Perfil
                    </a>
                </nav>
            </header>
            <main class="p-5 md:p-8 flex-1 w-full">
                <div class="w-full max-w-[1800px] mx-auto">
                    @yield('content')
                </div>
            </main>
            <footer class="px-5 md:px-8 py-5 text-center text-xs text-[#6B6F72]">
                © {{ date('Y') }} Pipocas · Turismo.
                Todos los derechos reservados.
            </footer>
        </div>
    </div>
    @stack('scripts')
</body>
</html>