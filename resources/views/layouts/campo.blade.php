{{-- Layout móvil para guías y choferes (check-in en campo). --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('titulo', 'Check-in') | Pipocas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.5"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    @stack('head')
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans min-h-screen flex flex-col">
    <header class="bg-slate-900 text-white px-4 sm:px-6 py-4 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-3 min-w-0">
            <a href="@yield('volver', route('guias.checkin'))" class="h-10 w-10 shrink-0 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-bold shadow" aria-label="Volver">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div class="min-w-0">
                <h1 class="text-base sm:text-lg font-bold truncate">@yield('titulo', 'Check-in')</h1>
                <p class="text-xs text-amber-400 truncate">{{ auth()->user()->nom }} · {{ auth()->user()->nombreRol() }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-xs text-slate-300 hover:text-white"><i class="fa-solid fa-right-from-bracket"></i></button></form>
    </header>
    <main class="flex-1 max-w-3xl w-full mx-auto p-4 sm:p-6 space-y-6">
        @if (session('estado'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('estado') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif
        @yield('contenido')
    </main>
    @stack('scripts')
</body>
</html>
