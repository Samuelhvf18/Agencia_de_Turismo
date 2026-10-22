<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1F5F8B">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asistente Turístico | Pipocas La Paz</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --pipocas-azul: #1F5F8B;
            --pipocas-terracota: #C96F4A;
            --pipocas-amarillo: #F4C95D;
            --pipocas-fondo: #F0F0F1;
            --pipocas-texto: #343A40;
        }
        body {
            background: var(--pipocas-fondo);
            color: var(--pipocas-texto);
        }
        .chat-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .chat-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .chat-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .mensaje-usuario {
            background: var(--pipocas-azul);
            color: white;
            border-radius: 18px 18px 4px 18px;
        }
        .mensaje-bot {
            background: white;
            color: var(--pipocas-texto);
            border: 1px solid #e2e8f0;
            border-radius: 18px 18px 18px 4px;
        }
        .boton-pregunta {
            border: 1px solid #dbe3e9;
            background: white;
            transition: all .2s ease;
        }
        .boton-pregunta:hover {
            border-color: var(--pipocas-azul);
            background: #edf5fa;
            transform: translateY(-1px);
        }
        .boton-enviar {
            background: var(--pipocas-azul);
            transition: background .2s ease;
        }
        .boton-enviar:hover {
            background: #174870;
        }
        .punto {
            animation: latido 1.2s infinite ease-in-out;
        }
        .punto:nth-child(2) { animation-delay: .15s; }
        .punto:nth-child(3) { animation-delay: .3s; }
        @keyframes latido {
            0%, 60%, 100% { opacity: .35; transform: translateY(0); }
            30% { opacity: 1; transform: translateY(-3px); }
        }
    </style>
</head>

<body class="antialiased font-sans min-h-screen flex flex-col"
      x-data="chatPipocas()"
      x-init="iniciar()">
    <!-- Encabezado -->
    <header class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="/"
                   aria-label="Volver al inicio"
                   class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center text-white shadow-sm"
                   style="background: var(--pipocas-azul)">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="min-w-0">
                    <h1 class="font-bold text-base sm:text-lg text-slate-900">
                        Asistente Turístico
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Pipocas · Sagárnaga, La Paz
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-600">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>Asistente disponible</span>
            </div>
        </div>
    </header>
    <!-- Contenido principal -->
    <main class="flex-1 w-full max-w-6xl mx-auto p-3 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-4">
        <!-- Panel de preguntas frecuentes -->
        <aside class="lg:col-span-4 space-y-4">
            <section class="rounded-2xl p-5 sm:p-6 text-white shadow-sm"
                     style="background: linear-gradient(135deg, #1F5F8B, #457699)">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4 text-2xl"
                     style="background: rgba(255,255,255,.16)">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <h2 class="text-xl font-bold mb-2">
                    ¡Explora La Paz!
                </h2>
                <p class="text-sm leading-6 text-white/90">
                    Descubre lugares increíbles, organiza tus recorridos y conoce
                    consejos para disfrutar tu visita.
                </p>
                <div class="mt-4 text-xs text-white/80 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    Asistente turístico con inteligencia artificial
                </div>
            </section>Asistente con IA · Los datos de tours se integrarán próximamente
            <section class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">
                <h2 class="font-bold text-slate-800 mb-1">
                    <i class="fa-solid fa-bolt mr-2" style="color: var(--pipocas-terracota)"></i>
                    Preguntas rápidas
                </h2>
                <p class="text-xs text-slate-500 mb-4">
                    Selecciona un tema para conversar.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
                    <template x-for="pregunta in preguntasRapidas" :key="pregunta.texto">
                        <button type="button"
                                @click="preguntar(pregunta.texto)"
                                class="boton-pregunta w-full rounded-xl px-3 py-3 text-left text-sm flex items-center gap-3">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                  style="background: #F0F0F1; color: var(--pipocas-azul)">
                                <i :class="pregunta.icono"></i>
                            </span>
                            <span class="font-medium text-slate-700"
                                  x-text="pregunta.texto"></span>
                        </button>
                    </template>
                </div>
            </section>
            <section class="rounded-2xl p-4 border"
                     style="background: #fff9e8; border-color: #f4df9d">
                <div class="flex gap-3">
                    <i class="fa-solid fa-lightbulb mt-1"
                       style="color: #9a7010"></i>
                    <div>
                        <h3 class="font-bold text-sm text-slate-800">
                            Consejo del viajero
                        </h3>
                        <p class="text-sm text-slate-600 mt-1 leading-5">
                            La Paz está a gran altitud. Tómate tu tiempo para
                            aclimatarte, bebe agua y evita exigirte demasiado
                            durante las primeras horas.
                        </p>
                    </div>
                </div>
            </section>
        </aside>
        <!-- Chat -->
        <section class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col"
                 style="min-height: 570px; max-height: 850px">
            <!-- Cabecera del chat -->
            <div class="px-4 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full flex items-center justify-center text-white"
                         style="background: var(--pipocas-terracota)">
                        <i class="fa-solid fa-headset text-lg"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900">
                            Asistente Pipocas
                        </h2>
                        <p class="text-xs text-slate-500">
                            Turismo y ayuda para visitantes
                        </p>
                    </div>
                </div>
                <button type="button"
                        @click="limpiarChat()"
                        class="text-xs sm:text-sm text-slate-500 hover:text-red-600 px-2 py-2 rounded-lg"
                        title="Reiniciar conversación">
                    <i class="fa-solid fa-rotate-right mr-1"></i>
                    <span class="hidden sm:inline">Reiniciar</span>
                </button>
            </div>
            <!-- Mensajes -->
            <div x-ref="contenedorMensajes"
                 role="log"
                 aria-label="Conversación con el asistente"
                 aria-live="polite"
                 class="chat-scroll flex-1 overflow-y-auto p-4 sm:p-6 space-y-4"
                 style="background: #f8fafc">
                <template x-for="(mensaje, indice) in mensajes" :key="indice">
                    <div class="flex gap-2 sm:gap-3"
                         :class="mensaje.remitente === 'usuario' ? 'justify-end' : 'justify-start'">
                        <template x-if="mensaje.remitente === 'bot'">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white text-xs mt-1"
                                 style="background: var(--pipocas-terracota)">
                                <i class="fa-solid fa-compass"></i>
                            </div>
                        </template>
                        <div class="max-w-[88%] sm:max-w-[80%] px-4 py-3 shadow-sm"
                             :class="mensaje.remitente === 'usuario' ? 'mensaje-usuario' : 'mensaje-bot'">
                            <p class="text-sm leading-6 whitespace-pre-line break-words"
                               x-text="mensaje.texto"></p>
                            <div class="text-right text-[10px] mt-2 opacity-70"
                                 x-text="mensaje.hora"></div>
                        </div>
                    </div>
                </template>
                <!-- Indicador de respuesta -->
                <div x-show="escribiendo"
                     x-cloak
                     class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs"
                         style="background: var(--pipocas-terracota)">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                    <div class="mensaje-bot px-4 py-3 flex items-center gap-1">
                        <span class="punto w-2 h-2 bg-slate-500 rounded-full"></span>
                        <span class="punto w-2 h-2 bg-slate-500 rounded-full"></span>
                        <span class="punto w-2 h-2 bg-slate-500 rounded-full"></span>
                    </div>
                </div>
            </div>

            <!-- Entrada -->
            <div class="border-t border-slate-200 p-3 sm:p-4 bg-white">
                <form @submit.prevent="enviar()"
                      class="flex items-end gap-2 sm:gap-3">
                    <label for="mensaje"
                           class="sr-only">Escribe tu pregunta</label>
                    <textarea id="mensaje"
                              x-model="nuevoMensaje"
                              @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); enviar(); }"
                              rows="1"
                              maxlength="500"
                              placeholder="Pregunta sobre turismo, transporte o Pipocas..."
                              class="flex-1 resize-none px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                              style="--tw-ring-color: var(--pipocas-azul); max-height: 110px"
                              autocomplete="off"></textarea>
                    <button type="submit"
                            :disabled="!nuevoMensaje.trim() || escribiendo"
                            class="boton-enviar text-white rounded-xl px-4 sm:px-5 py-3 font-semibold text-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span class="hidden sm:inline">Enviar</span>
                    </button>
                </form>
                <p class="text-[11px] text-slate-400 mt-2 text-center">
                    Asistente con IA · Los datos de tours se integrarán próximamente
                </p>
            </div>
        </section>
    </main>
    <footer class="text-center text-xs text-slate-500 py-4 px-4">
        © {{ date('Y') }} Pipocas La Paz · Agencia de viajes y turismo
    </footer>
    <script>
        function chatPipocas() {
            return {
                nuevoMensaje: '',
                mensajes: [],
                escribiendo: false,
                preguntasRapidas: [
                    { texto: '¿Qué lugares visitar en La Paz?', icono: 'fa-solid fa-mountain-sun' },
                    { texto: '¿Cuál es la mejor hora para explorar?', icono: 'fa-solid fa-clock' },
                    { texto: '¿Es mejor ir a pie o en movilidad?', icono: 'fa-solid fa-route' },
                    { texto: '¿Cómo funciona Pipocas?', icono: 'fa-solid fa-circle-question' },
                    { texto: '¿Qué ropa debo llevar?', icono: 'fa-solid fa-shirt' },
                    { texto: '¿Cómo llego al Valle de la Luna?', icono: 'fa-solid fa-map-location-dot' },
                    { texto: '¿Qué puedo hacer si tengo poco tiempo?', icono: 'fa-solid fa-hourglass-half' },
                { texto: '¿Qué debo saber sobre la altura?', icono: 'fa-solid fa-heart-pulse' }
            ],
            iniciar() {
                if (this.mensajes.length > 0) return;
                this.mensajeBot(
                    '¡Hola! Bienvenido a Pipocas, tu agencia de viajes y turismo en La Paz.'
                );
                this.mensajeBot(
                    'Ahora puedes conversar conmigo mediante inteligencia artificial. Pregúntame sobre destinos, actividades, transporte y consejos para tu visita.'
                );
            this.desplazarAbajo();
        },
        horaActual() {
            return new Date().toLocaleTimeString('es-BO', {
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        mensajeBot(texto) {
            this.mensajes.push({
                remitente: 'bot',
                texto: texto,
                hora: this.horaActual()
            });
        },
        desplazarAbajo() {
            this.$nextTick(() => {
                const contenedor = this.$refs.contenedorMensajes;

                if (contenedor) {
                    contenedor.scrollTop = contenedor.scrollHeight;
                }
            });
        },
        preguntar(texto) {
            this.nuevoMensaje = texto;
            this.enviar();
        },
        async enviar() {
            const texto = this.nuevoMensaje.trim();
            if (!texto || this.escribiendo) return;

            this.mensajes.push({
                remitente: 'usuario',
                texto: texto,
                hora: this.horaActual()
            });

            this.nuevoMensaje = '';
            this.escribiendo = true;
            this.desplazarAbajo();

            try {
                const respuesta = await fetch('/chatbot/mensaje', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]'
                        )?.content || ''
                    },
                    body: JSON.stringify({
                        mensaje: texto
                    })
                });

                const datos = await respuesta.json().catch(() => ({}));

                if (!respuesta.ok) {
                    throw new Error(
                        datos.error || 'No se pudo obtener una respuesta del asistente.'
                    );
                }

                if (!datos.respuesta) {
                    throw new Error('El asistente devolvió una respuesta vacía.');
                }

                this.mensajeBot(datos.respuesta);

            } catch (error) {
                console.error('Error del chatbot:', error);

                this.mensajeBot(
                    error.message ||
                    'No pude conectarme con el asistente. Inténtalo nuevamente.'
                );

            } finally {
                this.escribiendo = false;
                this.desplazarAbajo();
            }
        },

        limpiarChat() {
            this.mensajes = [];
            this.nuevoMensaje = '';
            this.escribiendo = false;
            this.iniciar();
        }
    };
}
    </script>
</body>
</html>