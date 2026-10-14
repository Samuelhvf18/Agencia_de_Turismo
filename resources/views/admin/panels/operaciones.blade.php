<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Operaciones | Pipocas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<header class="bg-slate-900 text-white p-5 flex flex-wrap justify-between gap-4">
    <a href="{{ route('dashboard') }}" class="font-black text-xl"><i class="fa-solid fa-mountain-sun text-amber-400 mr-2"></i>Pipocas Admin</a>
    <span>{{ auth()->user()->name }}</span>
    <form method="POST" action="{{ route('logout') }}">@csrf
        <button class="text-rose-300 hover:text-white"><i class="fa-solid fa-right-from-bracket mr-2"></i>Cerrar sesión</button>
    </form>
</header>
<main class="max-w-7xl mx-auto p-6">
    <a href="{{ route('dashboard') }}" class="text-amber-700 hover:underline">← Volver al dashboard</a>
    <h1 class="text-3xl font-black mt-6">Operaciones y salidas</h1>
    <p class="text-slate-500 mt-2">Planificación y coordinación de los recorridos turísticos.</p>
    <section class="grid gap-5 md:grid-cols-3 mt-8">
        <article class="bg-white p-6 rounded-2xl border shadow-sm"><i class="fa-solid fa-calendar-days text-amber-600 text-2xl"></i><h2 class="font-bold mt-3">Programación de salidas</h2><p class="text-sm text-slate-500 mt-2">Organización de fechas y horarios.</p></article>
        <article class="bg-white p-6 rounded-2xl border shadow-sm"><i class="fa-solid fa-user-check text-amber-600 text-2xl"></i><h2 class="font-bold mt-3">Asignación de guías</h2><p class="text-sm text-slate-500 mt-2">Coordinación del personal responsable.</p></article>
        <article class="bg-white p-6 rounded-2xl border shadow-sm"><i class="fa-solid fa-clipboard-check text-amber-600 text-2xl"></i><h2 class="font-bold mt-3">Control operativo</h2><p class="text-sm text-slate-500 mt-2">Seguimiento del estado de las salidas.</p></article>
    </section>
    <section class="bg-white rounded-2xl border shadow-sm p-6 mt-6"><h2 class="font-bold text-lg">Programación operativa</h2><p class="text-sm text-slate-500 mt-2">Aquí se integrarán las salidas y asignaciones registradas en la base de datos.</p></section>
</main>
</body>
</html>