<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Check-in (Guía) | Pipocas La Paz</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-3">
            <a href="/" class="h-10 w-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-bold shadow">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold">Panel de Check-in • Guía Turístico</h1>
                <p class="text-xs text-amber-400">Punto de Encuentro: Calle Sagárnaga #234</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full bg-emerald-500/25 text-emerald-300 text-xs font-semibold border border-emerald-500/30">
            <i class="fa-solid fa-signal"></i> Conectado (Offline Sync Active)
        </span>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-3xl w-full mx-auto p-4 sm:p-6 space-y-6" x-data="{
        salidaActiva: 'SAL-501 (Biking Camino de la Muerte)',
        recorridoIniciado: false,
        horaInicio: '-',
        horaFin: '-',
        escanearQR() {
            Swal.fire({
                title: 'Escáner QR Activo',
                text: 'Simulando escaneo del ticket digital QR del participante...',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        },
        iniciarTour() {
            this.recorridoIniciado = true;
            this.horaInicio = new Date().toLocaleTimeString();
            Swal.fire('¡Recorrido Iniciado!', 'Se ha registrado la hora de inicio oficial en el sistema.', 'success');
        },
        finalizarTour() {
            this.recorridoIniciado = false;
            this.horaFin = new Date().toLocaleTimeString();
            Swal.fire('¡Recorrido Finalizado!', 'El tour ha concluido con éxito. Se enviaron las estadísticas a operaciones.', 'success');
        }
    }">
        
        <!-- Salida Asignada Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block mb-1">Salida Asignada de Hoy</span>
            <h2 class="text-xl font-extrabold text-slate-900 mb-4" x-text="salidaActiva"></h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6 text-sm">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <span class="text-slate-400 block text-xs">Participantes</span>
                    <span class="font-bold text-slate-900 text-base">12 / 12 confirmados</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <span class="text-slate-400 block text-xs">Hora Inicio Real</span>
                    <span class="font-bold text-emerald-600 text-base" x-text="horaInicio"></span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 col-span-2 sm:col-span-1">
                    <span class="text-slate-400 block text-xs">Hora Fin Real</span>
                    <span class="font-bold text-rose-600 text-base" x-text="horaFin"></span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button @click="iniciarTour()" x-show="!recorridoIniciado" class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition shadow">
                    <i class="fa-solid fa-play"></i> Registrar Inicio de Recorrido
                </button>
                <button @click="finalizarTour()" x-show="recorridoIniciado" class="flex-1 py-3 px-4 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700 transition shadow">
                    <i class="fa-solid fa-stop"></i> Registrar Fin de Recorrido
                </button>
                <button @click="escanearQR()" class="py-3 px-6 rounded-xl bg-slate-900 text-white font-semibold hover:bg-amber-600 transition shadow">
                    <i class="fa-solid fa-qrcode"></i> Escanear QR
                </button>
            </div>
        </div>

        <!-- Participants List -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Participantes Registrados (Validación QR)</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div>
                        <p class="font-bold text-slate-900 text-sm">Hans Gruber <span class="text-xs font-normal text-slate-500">(Pasaporte: DE88912)</span></p>
                        <p class="text-xs text-amber-600">Alquiler: Bicicleta Doble + Casco (Talla L)</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">Validado QR</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div>
                        <p class="font-bold text-slate-900 text-sm">Marie Curie <span class="text-xs font-normal text-slate-500">(Pasaporte: FR44102)</span></p>
                        <p class="text-xs text-amber-600">Alquiler: Bicicleta Doble + Casco (Talla S)</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-slate-200 text-slate-700 text-xs font-bold">Pendiente Scan</span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
