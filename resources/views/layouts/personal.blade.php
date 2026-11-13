<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pipocas') }} - Portal Operativo</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.css'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900 min-h-screen flex flex-col justify-between">

    <header class="bg-[#1F5F8B] shadow-sm border-b border-white/10 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#F4C95D] flex items-center justify-center text-[#1F5F8B] font-extrabold shadow-inner">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-sm tracking-wide">Pipocas · Portal Operativo</h1>
                    <p class="text-[10px] text-white/70 uppercase">Panel de Control de Guías y Choferes</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-white">{{ auth()->user()->name ?? auth()->user()->nom }}</p>
                    <p class="text-[10px] text-[#F4C95D] font-semibold capitalize">Rol: {{ auth()->user()->getRoleNames()->first() ?? 'Operativo' }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-white/10 hover:bg-red-500/80 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-2 border border-white/20">
                        <i class="fa-solid fa-power-off"></i>
                        <span class="hidden sm:inline">Cerrar sesión</span>
                    </button>
                </form>
            </div>

        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} Pipocas · Sistema de Gestión Turística Operativa
    </footer>

</body>
</html>