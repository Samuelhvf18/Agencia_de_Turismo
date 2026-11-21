<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asistente turístico | Andinist Bolivia La Paz</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans h-screen flex flex-col">
    <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="h-10 w-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-bold shadow" aria-label="Volver al inicio">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Asistente turístico</h1>
                <p class="text-xs text-slate-500">Andinist Bolivia · Sagárnaga, La Paz · Español / English</p>
            </div>
        </div>
    </header>

    <livewire:asistente-turistico />
    @livewireScripts
</body>
</html>
