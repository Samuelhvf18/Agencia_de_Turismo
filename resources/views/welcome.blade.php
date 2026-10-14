<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pipocas - Agencia de Viajes y Turismo | Sagárnaga, La Paz</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl bg-amber-500 flex items-center justify-center shadow-md shadow-amber-500/20 text-white text-xl font-bold">
                    <i class="fa-solid fa-mountain-sun"></i>
                </div>
                <div>
                    <span class="text-2xl font-black tracking-tight text-slate-900">Pipocas<span class="text-amber-600">.</span></span>
                    <p class="text-xs font-medium text-slate-500">Calle Sagárnaga #234, La Paz</p>
                </div>
            </div>
            
            <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600">
                <a href="#catalogo" class="hover:text-amber-600 transition">Catálogo de Tours</a>
                <a href="#experiencias" class="hover:text-amber-600 transition">Actividades</a>
                <a href="#verificar" class="hover:text-amber-600 transition">Verificar Reserva</a>
                <a href="/chatbot" class="hover:text-amber-600 transition flex items-center gap-1.5"><i class="fa-solid fa-robot text-amber-500"></i> Asistente IA</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold shadow hover:bg-slate-800 transition">
                        <i class="fa-solid fa-gauge"></i> Panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-amber-600 px-3 py-2 transition">
                        Iniciar sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 text-white text-sm font-semibold shadow-md shadow-amber-600/20 hover:bg-amber-700 transition">
                            Registrarse
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-slate-900 text-white overflow-hidden py-24 lg:py-32">
        <div class="absolute inset-0 opacity-40 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1526772662000-3f88f10405ff?q=80&w=1920&auto=format&fit=crop');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-400 text-xs font-bold uppercase tracking-wider mb-6 border border-amber-500/30">
                    <i class="fa-solid fa-location-dot"></i> Corazón de los Andes • La Paz, Bolivia
                </span>
                <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-none mb-6">
                    Aventuras inolvidables desde la <span class="text-amber-400">Calle Sagárnaga</span>
                </h1>
                <p class="text-lg text-slate-300 mb-8 font-normal leading-relaxed">
                    Explora rutas georreferenciadas de Biking, Hiking, Trekking y Climbing en los paisajes más imponentes de La Paz. Reserva en línea con código QR y equipamiento garantizado.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#catalogo" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/30 hover:bg-amber-400 transition">
                        <i class="fa-solid fa-compass"></i> Explorar Tours
                    </a>
                    <a href="#verificar" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/10 backdrop-blur-md text-white font-semibold border border-white/20 hover:bg-white/20 transition">
                        <i class="fa-solid fa-ticket"></i> Consultar Reserva
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Stats Bar -->
    <section class="bg-amber-600 text-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <p class="text-3xl font-black">15+</p>
                <p class="text-xs font-medium text-amber-100 uppercase tracking-wider mt-1">Rutas Georreferenciadas</p>
            </div>
            <div>
                <p class="text-3xl font-black">4,700m</p>
                <p class="text-xs font-medium text-amber-100 uppercase tracking-wider mt-1">Altitud Máxima (Huayna Potosí)</p>
            </div>
            <div>
                <p class="text-3xl font-black">100%</p>
                <p class="text-xs font-medium text-amber-100 uppercase tracking-wider mt-1">Equipamiento Certificado</p>
            </div>
            <div>
                <p class="text-3xl font-black">24/7</p>
                <p class="text-xs font-medium text-amber-100 uppercase tracking-wider mt-1">Asistencia con IA</p>
            </div>
        </div>
    </section>

    <!-- Catalog Section with Alpine.js filtering -->
    <section id="catalogo" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ categoria: 'todos', dificultad: 'todos' }">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-amber-600 font-bold text-sm tracking-widest uppercase">Catálogo Oficial</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-1">Nuestras Rutas y Experiencias</h2>
            </div>
            
            <!-- Filters -->
            <div class="mt-6 md:mt-0 flex flex-wrap gap-3">
                <button @click="categoria = 'todos'" :class="categoria === 'todos' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200'" class="px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">Todos</button>
                <button @click="categoria = 'biking'" :class="categoria === 'biking' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200'" class="px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">Biking</button>
                <button @click="categoria = 'hiking'" :class="categoria === 'hiking' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200'" class="px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">Hiking</button>
                <button @click="categoria = 'trekking'" :class="categoria === 'trekking' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200'" class="px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">Trekking</button>
                <button @click="categoria = 'climbing'" :class="categoria === 'climbing' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200'" class="px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">Climbing</button>
                <button @click="categoria = 'city'" :class="categoria === 'city' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200'" class="px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">City Tour</button>
            </div>
        </div>

        <!-- Tours Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Tour 1: Biking Camino de la Muerte -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition border border-slate-200 flex flex-col" x-show="categoria === 'todos' || categoria === 'biking'">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1544198365-f5d60b6d8190?q=80&w=800&auto=format&fit=crop" alt="Biking Camino de la Muerte" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-lg bg-amber-500 text-slate-950 font-bold text-xs uppercase shadow">Biking</span>
                    <span class="absolute top-4 right-4 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white font-semibold text-xs shadow">TUR-2026-LPZ01</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-2">
                            <span><i class="fa-solid fa-clock text-amber-600"></i> 7.5 hrs</span>
                            <span><i class="fa-solid fa-mountain text-amber-600"></i> Máx 4,700m</span>
                            <span class="text-rose-600 font-bold">Difícil (Alto Riesgo)</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Descenso Camino de la Muerte (Yungas)</h3>
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2">Desafía tus límites descendiendo desde La Cumbre hasta Coroico en bicicleta de doble suspensión con guías expertos.</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400 block">Precio base</span>
                            <span class="text-lg font-black text-slate-900">Bs. 350 <span class="text-xs font-normal text-slate-500">/ pers</span></span>
                        </div>
                        <a href="#" onclick="Swal.fire({title: 'Detalle de Tour', text: 'Redirigiendo al mapa interactivo y reserva...', icon: 'info'}); return false;" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-amber-600 transition shadow">
                            Ver Ruta & Reservar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tour 2: Huayna Potosi Day Trip -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition border border-slate-200 flex flex-col" x-show="categoria === 'todos' || categoria === 'climbing'">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=800&auto=format&fit=crop" alt="Huayna Potosi" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-lg bg-indigo-600 text-white font-bold text-xs uppercase shadow">Climbing</span>
                    <span class="absolute top-4 right-4 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white font-semibold text-xs shadow">TUR-2026-LPZ02</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-2">
                            <span><i class="fa-solid fa-clock text-amber-600"></i> 8.0 hrs</span>
                            <span><i class="fa-solid fa-mountain text-amber-600"></i> 5,100m</span>
                            <span class="text-rose-600 font-bold">Experto (Alto Riesgo)</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Glaciar Huayna Potosí (Práctica en Hielo)</h3>
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2">Iniciación al montañismo glaciar en la Cordillera Real con crampones, piolet y guía de alta montaña certificado.</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400 block">Precio base</span>
                            <span class="text-lg font-black text-slate-900">Bs. 480 <span class="text-xs font-normal text-slate-500">/ pers</span></span>
                        </div>
                        <a href="#" onclick="Swal.fire({title: 'Detalle de Tour', text: 'Redirigiendo al mapa interactivo y reserva con Ficha Médica...', icon: 'info'}); return false;" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-amber-600 transition shadow">
                            Ver Ruta & Reservar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tour 3: Valle de la Luna y Miradores -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition border border-slate-200 flex flex-col" x-show="categoria === 'todos' || categoria === 'city'">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1518638150340-f706e86654de?q=80&w=800&auto=format&fit=crop" alt="Valle de la Luna" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-lg bg-emerald-600 text-white font-bold text-xs uppercase shadow">City Tour</span>
                    <span class="absolute top-4 right-4 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white font-semibold text-xs shadow">TUR-2026-LPZ03</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-2">
                            <span><i class="fa-solid fa-clock text-amber-600"></i> 4.0 hrs</span>
                            <span><i class="fa-solid fa-mountain text-amber-600"></i> 3,600m</span>
                            <span class="text-emerald-600 font-bold">Fácil</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Valle de la Luna & Teleféricos de La Paz</h3>
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2">Recorrido por formaciones geológicas lunares y tour panorámico en el sistema de teleférico más alto del mundo.</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400 block">Precio base</span>
                            <span class="text-lg font-black text-slate-900">Bs. 180 <span class="text-xs font-normal text-slate-500">/ pers</span></span>
                        </div>
                        <a href="#" onclick="Swal.fire({title: 'Detalle de Tour', text: 'Redirigiendo al catálogo interactivo...', icon: 'info'}); return false;" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-amber-600 transition shadow">
                            Ver Ruta & Reservar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tour 4: Trekking Tuni Condoriri -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition border border-slate-200 flex flex-col" x-show="categoria === 'todos' || categoria === 'trekking'">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=800&auto=format&fit=crop" alt="Tuni Condoriri" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-lg bg-blue-600 text-white font-bold text-xs uppercase shadow">Trekking</span>
                    <span class="absolute top-4 right-4 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white font-semibold text-xs shadow">TUR-2026-LPZ04</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-2">
                            <span><i class="fa-solid fa-clock text-amber-600"></i> 6.5 hrs</span>
                            <span><i class="fa-solid fa-mountain text-amber-600"></i> 4,500m</span>
                            <span class="text-amber-600 font-bold">Moderado</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Laguna Chiar Khota & Condoriri</h3>
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2">Caminata de altura entre lagunas glaciales color turquesa rodeados por picos nevados imponentes de la Cordillera.</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400 block">Precio base</span>
                            <span class="text-lg font-black text-slate-900">Bs. 260 <span class="text-xs font-normal text-slate-500">/ pers</span></span>
                        </div>
                        <a href="#" onclick="Swal.fire({title: 'Detalle de Tour', text: 'Redirigiendo al catálogo interactivo...', icon: 'info'}); return false;" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-amber-600 transition shadow">
                            Ver Ruta & Reservar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tour 5: Hiking El Choro -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition border border-slate-200 flex flex-col" x-show="categoria === 'todos' || categoria === 'hiking'">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=800&auto=format&fit=crop" alt="El Choro" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-lg bg-teal-600 text-white font-bold text-xs uppercase shadow">Hiking</span>
                    <span class="absolute top-4 right-4 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white font-semibold text-xs shadow">TUR-2026-LPZ05</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-2">
                            <span><i class="fa-solid fa-clock text-amber-600"></i> 7.0 hrs</span>
                            <span><i class="fa-solid fa-mountain text-amber-600"></i> 4,200m</span>
                            <span class="text-amber-600 font-bold">Moderado</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Camino Prehispánico El Choro (Tramo Alto)</h3>
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2">Recorre tramos empedrados incas originales desde Apacheta hasta Challapampa con vistas fascinantes al valle subtropical.</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400 block">Precio base</span>
                            <span class="text-lg font-black text-slate-900">Bs. 290 <span class="text-xs font-normal text-slate-500">/ pers</span></span>
                        </div>
                        <a href="#" onclick="Swal.fire({title: 'Detalle de Tour', text: 'Redirigiendo al catálogo interactivo...', icon: 'info'}); return false;" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-amber-600 transition shadow">
                            Ver Ruta & Reservar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tour 6: Tiwanaku Arqueológico -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition border border-slate-200 flex flex-col" x-show="categoria === 'todos' || categoria === 'city'">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?q=80&w=800&auto=format&fit=crop" alt="Tiwanaku" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-lg bg-emerald-600 text-white font-bold text-xs uppercase shadow">City Tour</span>
                    <span class="absolute top-4 right-4 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md text-white font-semibold text-xs shadow">TUR-2026-LPZ06</span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-2">
                            <span><i class="fa-solid fa-clock text-amber-600"></i> 6.0 hrs</span>
                            <span><i class="fa-solid fa-mountain text-amber-600"></i> 3,850m</span>
                            <span class="text-emerald-600 font-bold">Fácil</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Complejo Arqueológico de Tiwanaku & Puma Punku</h3>
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2">Visita la cuna de la civilización andina milenaria con guía arqueológico especializado, monolitos y Puerta del Sol.</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400 block">Precio base</span>
                            <span class="text-lg font-black text-slate-900">Bs. 220 <span class="text-xs font-normal text-slate-500">/ pers</span></span>
                        </div>
                        <a href="#" onclick="Swal.fire({title: 'Detalle de Tour', text: 'Redirigiendo al catálogo interactivo...', icon: 'info'}); return false;" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-amber-600 transition shadow">
                            Ver Ruta & Reservar
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Reservation Verification Section -->
    <section id="verificar" class="bg-slate-100 py-20 border-y border-slate-200">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <span class="inline-block p-3 rounded-2xl bg-amber-500 text-slate-950 mb-4 shadow-md">
                <i class="fa-solid fa-ticket text-2xl"></i>
            </span>
            <h2 class="text-3xl font-extrabold text-slate-900 mb-3">Consulta tu Reserva al Instante</h2>
            <p class="text-slate-600 mb-8">Ingresa el código alfanumérico único (ej. <code class="bg-white px-2 py-1 rounded text-amber-600 font-bold border">RES-88A9F2</code>) para verificar tu estado de pago, salida y ticket QR.</p>
            
            <form onsubmit="event.preventDefault(); Swal.fire({title: 'Estado de Reserva', html: '<b>Código:</b> ' + document.getElementById('codigoRsv').value + '<br><b>Estado:</b> Confirmada <br><b>Pago:</b> Pagado (QR) <br><span class=\"text-emerald-600 font-bold\">¡Listo para el tour!</span>', icon: 'success'});" class="flex flex-col sm:flex-row gap-3 justify-center">
                <input type="text" id="codigoRsv" placeholder="Ej. RES-88A9F2" required class="px-5 py-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-center font-mono font-bold uppercase tracking-wider w-full sm:w-80 shadow-sm">
                <button type="submit" class="px-7 py-3.5 rounded-xl bg-slate-900 text-white font-semibold hover:bg-amber-600 transition shadow">
                    <i class="fa-solid fa-magnifying-glass"></i> Consultar
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-bold">
                    <i class="fa-solid fa-mountain-sun"></i>
                </div>
                <span class="text-xl font-black tracking-tight">Pipocas Agencia de Turismo</span>
            </div>
            <p class="text-sm text-slate-400">
                Calle Sagárnaga #234, Zona Central • La Paz, Bolivia • © {{ date('Y') }} Todos los derechos reservados.
            </p>
            <div class="flex gap-4 text-slate-400 text-lg">
                <a href="#" class="hover:text-amber-400 transition"><i class="fa-brands fa-whatsapp"></i></a>
                <a href="#" class="hover:text-amber-400 transition"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="hover:text-amber-400 transition"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>
