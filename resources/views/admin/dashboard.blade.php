<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administración y Operaciones | Pipocas La Paz</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans flex h-screen overflow-hidden" x-data="{ tab: 'resumen' }">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-300 hidden md:flex flex-col justify-between border-r border-slate-800">
        <div>
            <div class="h-20 flex items-center px-6 gap-3 border-b border-slate-800">
                <div class="h-10 w-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-bold">
                    <i class="fa-solid fa-mountain-sun"></i>
                </div>
                <div>
                    <span class="text-white font-black tracking-tight text-lg">Pipocas Admin</span>
                    <p class="text-[10px] text-amber-400 font-medium">Sagárnaga, La Paz</p>
                </div>
            </div>
            
            <nav class="p-4 space-y-1.5 text-sm font-medium">
                <button @click="tab = 'resumen'" :class="tab === 'resumen' ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition">
                    <i class="fa-solid fa-chart-pie w-5"></i> Resumen & Dashboard
                </button>
                <button @click="tab = 'operaciones'" :class="tab === 'operaciones' ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition">
                    <i class="fa-solid fa-users-gear w-5"></i> Operaciones & Salidas
                </button>
                <button @click="tab = 'inventario'" :class="tab === 'inventario' ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition">
                    <i class="fa-solid fa-boxes-stacked w-5"></i> Inventario & EOQ
                </button>
                <button @click="tab = 'auditoria'" :class="tab === 'auditoria' ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition">
                    <i class="fa-solid fa-shield-halved w-5"></i> Bitácora de Auditoría
                </button>
            </nav>
        </div>
        
        <div class="p-4 border-t border-slate-800">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 text-rose-400 text-sm font-semibold transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Volver al Portal
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- Topbar -->
        <header class="h-20 bg-white border-b border-slate-200 px-6 flex items-center justify-between shadow-sm">
            <h2 class="text-xl font-extrabold text-slate-900" x-text="tab === 'resumen' ? 'Dashboard Estadístico y Predictivo' : (tab === 'operaciones' ? 'Gestión de Operaciones, Guías y Flota' : (tab === 'inventario' ? 'Control de Inventario y Modelo EOQ' : 'Bitácora de Auditoría de Acciones'))"></h2>
            
            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold text-slate-700">Administrador (Sagárnaga)</span>
                <div class="h-10 w-10 rounded-xl bg-amber-600 text-white font-bold flex items-center justify-center shadow">AD</div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            
            <!-- Tab 1: Resumen -->
            <div x-show="tab === 'resumen'" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Reservas del Mes</p>
                        <p class="text-3xl font-black text-slate-900 mt-2">142</p>
                        <span class="text-xs text-emerald-600 font-semibold mt-1 block"><i class="fa-solid fa-arrow-up"></i> +18% vs mes anterior</span>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tours Más Solicitados</p>
                        <p class="text-xl font-black text-slate-900 mt-2">Biking Camino Muerte</p>
                        <span class="text-xs text-amber-600 font-semibold mt-1 block">64 reservas este mes</span>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ingresos Totales (Bs)</p>
                        <p class="text-3xl font-black text-slate-900 mt-2">Bs. 48,600</p>
                        <span class="text-xs text-emerald-600 font-semibold mt-1 block">Facturación automática activa</span>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nacionalidad Líder</p>
                        <p class="text-2xl font-black text-slate-900 mt-2">🇩🇪 🇫🇷 🇺🇸</p>
                        <span class="text-xs text-slate-500 font-semibold mt-1 block">Alemania, Francia, EE.UU.</span>
                    </div>
                </div>

                <!-- Proyección de Demanda -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Proyección de Demanda por Tour (Próximos Meses)</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                                <tr>
                                    <th class="p-3 rounded-l-xl">Tour / Ruta</th>
                                    <th class="p-3">Mes</th>
                                    <th class="p-3">Demanda Proyectada</th>
                                    <th class="p-3 rounded-r-xl">Versión del Modelo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                <tr>
                                    <td class="p-3">Biking Camino de la Muerte (TUR-2026-LPZ01)</td>
                                    <td class="p-3">Mayo 2026</td>
                                    <td class="p-3 text-amber-600 font-bold">85 reservas</td>
                                    <td class="p-3 text-slate-400 text-xs">ARIMA-v2.1</td>
                                </tr>
                                <tr>
                                    <td class="p-3">Glaciar Huayna Potosí (TUR-2026-LPZ02)</td>
                                    <td class="p-3">Mayo 2026</td>
                                    <td class="p-3 text-amber-600 font-bold">42 reservas</td>
                                    <td class="p-3 text-slate-400 text-xs">ARIMA-v2.1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Operaciones -->
            <div x-show="tab === 'operaciones'" class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-slate-900">Programación de Salidas & Asignación Óptima (Investigación Operativa II)</h3>
                        <button onclick="Swal.fire('Optimización Ejecutada', 'El modelo asignó guías equilibrando horas semanales y vehículos maximizando ocupación.', 'success')" class="px-4 py-2 rounded-xl bg-amber-600 text-white font-semibold text-sm hover:bg-amber-700 transition shadow">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Ejecutar Modelo de Asignación
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                                <tr>
                                    <th class="p-3 rounded-l-xl">Salida #</th>
                                    <th class="p-3">Tour</th>
                                    <th class="p-3">Fecha / Hora</th>
                                    <th class="p-3">Guía Asignado</th>
                                    <th class="p-3">Vehículo (Flota)</th>
                                    <th class="p-3">Ocupación</th>
                                    <th class="p-3 rounded-r-xl">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                <tr>
                                    <td class="p-3 font-mono font-bold">SAL-501</td>
                                    <td class="p-3">Biking Camino de la Muerte</td>
                                    <td class="p-3">05/04/2026 • 07:30</td>
                                    <td class="p-3 text-emerald-600">Juan Pérez (Exp: 6 años)</td>
                                    <td class="p-3">Minibus 15 (28-LPZ)</td>
                                    <td class="p-3">12/12 (100%)</td>
                                    <td class="p-3"><span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">Programada</span></td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-mono font-bold">SAL-502</td>
                                    <td class="p-3">Huayna Potosí Glacier</td>
                                    <td class="p-3">05/04/2026 • 05:00</td>
                                    <td class="p-3 text-emerald-600">Carlos Quispe (Exp: 9 años)</td>
                                    <td class="p-3">Van 4x4 (09-LPZ)</td>
                                    <td class="p-3">6/8 (75%)</td>
                                    <td class="p-3"><span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">Programada</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Inventario y EOQ -->
            <div x-show="tab === 'inventario'" class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Gestión de Inventario Crítico & Modelo EOQ (Cantidad Económica de Pedido)</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                                <tr>
                                    <th class="p-3 rounded-l-xl">Tipo de Equipo</th>
                                    <th class="p-3">Stock Actual</th>
                                    <th class="p-3">Costo Orden (S)</th>
                                    <th class="p-3">Costo Mantener (H)</th>
                                    <th class="p-3">EOQ Calculado</th>
                                    <th class="p-3 rounded-r-xl">Punto Reorden</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                <tr>
                                    <td class="p-3 font-bold">Bicicletas Doble Suspensión</td>
                                    <td class="p-3 text-emerald-600 font-bold">24 unidades</td>
                                    <td class="p-3">Bs. 250</td>
                                    <td class="p-3">Bs. 80 / año</td>
                                    <td class="p-3 text-amber-600 font-black">18 unidades</td>
                                    <td class="p-3">8 unidades</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-bold">Cascos Certificados EN 1078</td>
                                    <td class="p-3 text-emerald-600 font-bold">35 unidades</td>
                                    <td class="p-3">Bs. 120</td>
                                    <td class="p-3">Bs. 25 / año</td>
                                    <td class="p-3 text-amber-600 font-black">22 unidades</td>
                                    <td class="p-3">10 unidades</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Bitácora de Auditoría -->
            <div x-show="tab === 'auditoria'" class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Bitácora de Auditoría de Acciones del Sistema (Observers Laravel)</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                                <tr>
                                    <th class="p-3 rounded-l-xl">Fecha / Hora</th>
                                    <th class="p-3">Usuario</th>
                                    <th class="p-3">Tabla Afectada</th>
                                    <th class="p-3">Acción</th>
                                    <th class="p-3">Dirección IP</th>
                                    <th class="p-3 rounded-r-xl">Detalle JSON</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium font-mono text-xs">
                                <tr>
                                    <td class="p-3">05/04/2026 10:42:15</td>
                                    <td class="p-3">Samuel Villca (Admin)</td>
                                    <td class="p-3 text-amber-600">tours</td>
                                    <td class="p-3"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">CREATE</span></td>
                                    <td class="p-3">192.168.1.45</td>
                                    <td class="p-3 text-slate-500">{"cod": "TUR-2026-LPZ01", "nom": "Biking..."}</td>
                                </tr>
                                <tr>
                                    <td class="p-3">05/04/2026 09:15:22</td>
                                    <td class="p-3">Andres Cordero (Operaciones)</td>
                                    <td class="p-3 text-amber-600">reservas</td>
                                    <td class="p-3"><span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">UPDATE</span></td>
                                    <td class="p-3">192.168.1.12</td>
                                    <td class="p-3 text-slate-500">{"est": "pendiente" -> "confirmada"}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>
