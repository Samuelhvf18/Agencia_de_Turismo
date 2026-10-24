<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Panel Administrativo - Pipocas</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

<body class="bg-gray-100 text-gray-900 font-sans antialiased min-h-screen">
    <div class="flex h-screen overflow-hidden relative">

        <div id="mobile-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-[#102C40]/60 z-20 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

        <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-30 w-64 flex flex-col justify-between bg-gradient-to-b from-[#1F5F8B] to-[#174870] text-white shadow-xl overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-5">

                <div class="flex items-center gap-3 pb-5 border-b border-white/10">
                    <div class="w-10 h-10 rounded-xl bg-[#F4C95D] text-[#1F5F8B] grid place-items-center shadow-md">
                        <i class="fa-solid fa-mountain-sun text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-extrabold tracking-tight">Pipocas<span class="text-[#F4C95D]">.</span></h1>
                        <p class="text-[9px] font-bold tracking-[.16em] text-white/65 uppercase">Agencia de turismo</p>
                    </div>
                    <button onclick="toggleSidebar()" class="md:hidden ml-auto text-white/80 hover:text-[#F4C95D] p-2">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="mt-6 mb-3 px-3">
                    <p class="text-[9px] uppercase tracking-[.2em] font-bold text-white/45">Administración</p>
                </div>

                <x-sidebar-menu />

            </div>

            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 mb-4 px-1">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-[#F4C95D]">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold">Vive La Paz.</p>
                        <p class="text-[10px] text-white/55">Descubre Bolivia.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 rounded-xl bg-[#C96F4A]/20 border border-[#F4C95D]/20 text-white text-xs font-bold hover:bg-[#C96F4A] transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión
                    </button>
                </form>

                <p class="text-center text-[9px] text-white/35 mt-4">© {{ date('Y') }} Pipocas · Turismo</p>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto w-full">
            <header class="bg-white/90 backdrop-blur-md border-b border-[#1F5F8B]/10 min-h-16 flex items-center justify-between gap-4 px-4 sm:px-8 py-3 sticky top-0 z-10">
                <div class="flex items-center gap-3 min-w-0">
                    <button onclick="toggleSidebar()" class="md:hidden text-[#1F5F8B] hover:text-[#C96F4A] p-2 rounded-xl border border-[#1F5F8B]/10 bg-white">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div class="min-w-0">
                        <p class="text-[9px] sm:text-[10px] font-bold uppercase tracking-[.16em] text-[#457699] mb-0.5">Pipocas · Administración</p>
                        <h2 class="font-display text-lg sm:text-xl font-semibold truncate text-[#343A40]">
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
                <div class="w-full max-w-[1800px] mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('mobile-overlay').classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>