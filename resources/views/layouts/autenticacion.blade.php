<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Pipocas'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
          rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    @livewireStyles
</head>
<body class="min-h-screen">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden bg-[#1F5F8B]">
        <img
            src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=2200&q=90"
            alt=""
            class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-[#123C5B]/85 via-[#1F5F8B]/65 to-[#102F49]/90"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(240,240,241,0.42)_0%,rgba(69,118,153,0.20)_35%,transparent_72%)]"></div>
        <div class="absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-[#F4C95D]/20 blur-3xl"></div>
        <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-[#C96F4A]/15 blur-3xl"></div>
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full border-[35px] border-white/10"></div>
        <div class="absolute -bottom-40 -left-32 h-[28rem] w-[28rem] rounded-full border-[30px] border-white/10"></div>
    </div>
    <a
        href="{{ url('/') }}"
        aria-label="Volver a la página principal de Pipocas"
        title="Volver al inicio"
        class="fixed left-4 top-4 z-50 flex h-11 w-11 items-center justify-center rounded-2xl border border-white/30 bg-white/15 text-white shadow-lg backdrop-blur-xl transition hover:-translate-y-0.5 hover:bg-white/25 focus:outline-none focus:ring-4 focus:ring-white/30 sm:left-6 sm:top-6">
        <i class="fa-solid fa-arrow-left text-lg" aria-hidden="true"></i>
    </a>
    <div class="relative z-10 font-sans text-gray-900 antialiased">
        @yield('content')
    </div>
    @livewireScripts
    @stack('scripts')
</body>
</html>