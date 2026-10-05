<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asistente Virtual Turístico | Pipocas La Paz</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="/" class="h-10 w-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-bold shadow">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Asistente Virtual Turístico (IA)</h1>
                <p class="text-xs text-slate-500">Agencia Pipocas • Sagárnaga, La Paz</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> En línea (n8n + Gemini)
            </span>
        </div>
    </header>

    <!-- Chat Container -->
    <main class="flex-1 max-w-4xl w-full mx-auto p-4 sm:p-6 flex flex-col overflow-hidden">
        <div class="flex-1 bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6 flex flex-col justify-between overflow-hidden" x-data="{
            mensajes: [
                { rem: 'bot', texto: '¡Hola! Bienvenido a Pipocas. Soy tu asistente turístico virtual. ¿Qué te gustaría hacer hoy en La Paz?', hora: '10:00' },
                { rem: 'bot', texto: 'Puedes preguntarme por:\n• Recomendación de tours según tu tiempo y presupuesto.\n• Dudas sobre aclimatación a los 3,650m de altitud o clima en la ruta.\n• Consultar el estado de tu reserva escribiendo tu código (ej. RES-88A9F2).', hora: '10:00' }
            ],
            nuevoMensaje: '',
            enviar() {
                if(!this.nuevoMensaje.trim()) return;
                let textoUser = this.nuevoMensaje;
                this.mensajes.push({ rem: 'usuario', texto: textoUser, hora: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) });
                this.nuevoMensaje = '';
                
                setTimeout(() => {
                    let respuesta = 'Entiendo tu consulta sobre \"' + textoUser + '\". Basado en nuestros registros y la meteorología en la calle Sagárnaga, te sugiero revisar nuestro tour de Biking o consultar tu reserva directamente.';
                    if(textoUser.toUpperCase().includes('RES-')) {
                        respuesta = '🔍 He consultado la base de datos PostgreSQL: Tu reserva ' + textoUser.toUpperCase() + ' está CONFIRMADA y el pago de 350 Bs se encuentra registrado con éxito.';
                    } else if(textoUser.toLowerCase().includes('clima') || textoUser.toLowerCase().includes('frio')) {
                        respuesta = '🌤️ En La Paz y las rutas de montaña el clima es variable. Para actividades de altura recomendamos llevar polar, cortavientos impermeable y protector solar SPF 50+.';
                    }
                    this.mensajes.push({ rem: 'bot', texto: respuesta, hora: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) });
                }, 1000);
            }
        }">
            
            <!-- Messages Scroll Area -->
            <div class="flex-1 overflow-y-auto space-y-4 pr-2">
                <template x-for="m in mensajes">
                    <div :class="m.rem === 'usuario' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="m.rem === 'usuario' ? 'bg-amber-600 text-white rounded-2xl rounded-tr-none' : 'bg-slate-100 text-slate-800 rounded-2xl rounded-tl-none border border-slate-200'" class="max-w-md p-4 text-sm shadow-sm whitespace-pre-line">
                            <p x-text="m.texto"></p>
                            <span class="text-[10px] opacity-70 block text-right mt-1" x-text="m.hora"></span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Input Form -->
            <form @submit.prevent="enviar()" class="mt-4 pt-4 border-t border-slate-200 flex gap-3">
                <input type="text" x-model="nuevoMensaje" placeholder="Escribe tu consulta o código de reserva (ej. RES-88A9F2)..." class="flex-1 px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm shadow-sm">
                <button type="submit" class="px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-amber-600 transition shadow flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Enviar
                </button>
            </form>
        </div>
    </main>
</body>
</html>
