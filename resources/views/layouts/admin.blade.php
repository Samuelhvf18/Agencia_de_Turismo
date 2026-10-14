<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Agencia de Turismo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>
<body style="background-color: #F0F0F1; color: #343A40;" class="font-sans antialiased">
    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Overlay oscuro para dispositivos móviles (cierra el menú al tocar fuera) -->
        <div id="mobile-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

        <!-- Sidebar Fijo en Desktop y Desplegable en Móvil -->
        <aside id="sidebar" style="background-color: #1F5F8B;" class="fixed md:static inset-y-0 left-0 z-30 w-64 border-r border-[#174870] flex flex-col justify-between text-white shadow-xl overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold tracking-wider text-[#F4C95D]">ADMINISTRADOR AGENCIA TURISMO</h1>
                    <!-- Botón para cerrar menú en móviles -->
                    <button onclick="toggleSidebar()" class="md:hidden text-white hover:text-[#F4C95D] focus:outline-none text-lg">
                        ✕
                    </button>
                </div>
                <p class="text-xs text-[#F0F0F1]/80 mt-1">GRUPO PIPOCAS</p>
                
                <nav class="mt-6 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'bg-[#C96F4A] text-white shadow' : 'text-white/90 hover:bg-[#174870]' }}">
                        Dashboard General
                    </a>

                    <a href="{{ route('admin.operaciones') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.operaciones') ? 'bg-[#C96F4A] text-white shadow' : 'text-white/90 hover:bg-[#174870]' }}">
                        Operaciones & Salidas
                    </a>

                    <a href="{{ route('admin.inventario') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.inventario') ? 'bg-[#C96F4A] text-white shadow' : 'text-white/90 hover:bg-[#174870]' }}">
                        Inventario & Equipos
                    </a>

                    <a href="{{ route('admin.transporte') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.transporte') ? 'bg-[#C96F4A] text-white shadow' : 'text-white/90 hover:bg-[#174870]' }}">
                        Flota de Transporte
                    </a>

                    <a href="{{ route('admin.tours') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.tours') ? 'bg-[#C96F4A] text-white shadow' : 'text-white/90 hover:bg-[#174870]' }}">
                        Catálogo & Rutas
                    </a>

                    <a href="{{ route('admin.reservas') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.reservas') ? 'bg-[#C96F4A] text-white shadow' : 'text-white/90 hover:bg-[#174870]' }}">
                        Reservas & Pasajeros
                    </a>

                    <a href="{{ route('admin.finanzas') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.finanzas') ? 'bg-[#C96F4A] text-white shadow' : 'text-white/90 hover:bg-[#174870]' }}">
                        Finanzas & Facturación
                    </a>

                    <a href="{{ route('admin.analitica') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.analitica') ? 'bg-[#C96F4A] text-white shadow' : 'text-white/90 hover:bg-[#174870]' }}">
                        Analítica & Reportes
                    </a>

                    <a href="{{ route('admin.seguridad') }}" class="block px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.seguridad') ? 'bg-[#C96F4A] text-white shadow' : 'text-white/90 hover:bg-[#174870]' }}">
                        Configuración & Seguridad
                    </a>
                </nav>
            </div>
            
            <div class="p-4 border-t border-[#174870] flex justify-between items-center text-xs text-[#F0F0F1]/80">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Cerrar Sesión" class="p-1.5 bg-[#C96F4A] hover:bg-red-600 text-white rounded-lg transition font-medium text-xs flex items-center gap-1">
                        Cerrar Sesion / Salir
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto w-full">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center gap-3">
                    <!-- Botón Hamburguesa para Móvil -->
                    <button onclick="toggleSidebar()" class="md:hidden text-[#1F5F8B] hover:text-[#C96F4A] focus:outline-none p-1.5 rounded-lg border border-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-base sm:text-lg font-semibold truncate" style="color: #343A40;">@yield('header-title', 'Panel de Control')</h2>
                </div>
            </header>

            <main class="p-4 sm:p-8">
                @yield('content')
            </main>
        </div>

    </div>

    <!-- Script de alternancia para el menú en teléfonos y tablets -->
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