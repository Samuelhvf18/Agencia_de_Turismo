<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asistente turístico | Pipocas La Paz</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
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
                <p class="text-xs text-slate-500">Pipocas · Sagárnaga, La Paz · Español / English</p>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-4xl w-full mx-auto p-3 sm:p-6 flex flex-col overflow-hidden"
          x-data="chat(@js($mensajes), @js($derivado))">
        <div class="flex-1 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
            <div x-show="derivado" x-cloak class="border-b border-amber-200 bg-amber-50 px-4 py-2 text-xs font-semibold text-amber-800">
                <i class="fa-solid fa-headset"></i> Un asesor de Pipocas continúa la conversación. / An advisor is now handling this chat.
            </div>

            <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4" x-ref="lista" aria-live="polite">
                <div class="flex justify-start">
                    <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-slate-100 px-4 py-3 text-sm">
                        ¡Hola! Soy el asistente de Pipocas. Puedo recomendarte tours según tu tiempo, condición física y presupuesto, responder dudas y consultar tu reserva (código + correo). <i>Hi! I also speak English.</i>
                    </div>
                </div>
                <template x-for="m in mensajes" :key="m.id">
                    <div class="flex" :class="m.rem === 'usuario' ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm whitespace-pre-line break-words"
                             :class="m.rem === 'usuario' ? 'rounded-tr-sm bg-[#1F5F8B] text-white' : (m.humano ? 'rounded-tl-sm bg-amber-100 text-slate-800' : 'rounded-tl-sm bg-slate-100 text-slate-800')">
                            <p x-show="m.humano" class="mb-1 text-[11px] font-bold uppercase text-amber-700"><i class="fa-solid fa-user-tie"></i> Asesor</p>
                            <span x-html="enlazar(m.txt)"></span>
                            <span class="mt-1 block text-[10px] opacity-60" x-text="m.hora"></span>
                        </div>
                    </div>
                </template>
                <div x-show="escribiendo" x-cloak class="flex justify-start">
                    <div class="rounded-2xl bg-slate-100 px-4 py-3 text-sm text-slate-500"><i class="fa-solid fa-ellipsis fa-fade"></i></div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-slate-100 px-4 pt-3" x-show="!mensajes.length">
                @foreach (['Recomiéndame un tour fácil de medio día', '¿Qué llevo para el Huayna Potosí?', '¿Cómo funcionan los pagos?', 'I want an adventure tour under 400 Bs'] as $s)
                    <button type="button" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:border-[#1F5F8B] hover:text-[#1F5F8B]" @click="texto = @js($s); enviar()">{{ $s }}</button>
                @endforeach
            </div>

            <form class="flex gap-2 p-3 sm:p-4" @submit.prevent="enviar()">
                <label for="mensaje" class="sr-only">Mensaje</label>
                <input id="mensaje" x-model="texto" maxlength="500" autocomplete="off" placeholder="Escribe tu pregunta… / Type your question…"
                       class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-[#1F5F8B] focus:ring-[#1F5F8B]" :disabled="escribiendo">
                <button class="rounded-xl bg-[#1F5F8B] px-4 text-white disabled:opacity-50" :disabled="escribiendo || !texto.trim()" aria-label="Enviar"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
            <p x-show="error" x-cloak class="px-4 pb-3 text-xs text-red-600" x-text="error"></p>
        </div>
    </main>

    <script>
        function chat(iniciales, derivado) {
            return {
                mensajes: iniciales,
                derivado: derivado,
                texto: '',
                escribiendo: false,
                error: '',
                token: document.querySelector('meta[name="csrf-token"]').content,
                init() {
                    this.bajar();
                    // Sondeo de respuestas del asesor cuando la conversación está derivada.
                    setInterval(() => { if (this.derivado) this.actualizar(); }, 6000);
                },
                ultimo() { return this.mensajes.length ? this.mensajes[this.mensajes.length - 1].id : 0; },
                async enviar() {
                    const texto = this.texto.trim();
                    if (!texto || this.escribiendo) return;
                    this.texto = '';
                    this.error = '';
                    this.escribiendo = true;
                    this.mensajes.push({ id: 'tmp' + Date.now(), rem: 'usuario', txt: texto, hora: new Date().toTimeString().slice(0, 5) });
                    this.bajar();
                    try {
                        const r = await fetch(@js(route('chatbot.enviar')), {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.token },
                            body: JSON.stringify({ mensaje: texto, ultimo: this.ultimoReal() }),
                        });
                        if (r.status === 429) throw new Error('Vas muy rápido; espera unos segundos.');
                        if (!r.ok) throw new Error('No pude responder ahora. Intenta de nuevo.');
                        const datos = await r.json();
                        this.mensajes = this.mensajes.filter(m => !String(m.id).startsWith('tmp')).concat(datos.mensajes);
                        this.derivado = datos.derivado;
                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.escribiendo = false;
                        this.bajar();
                    }
                },
                ultimoReal() { const reales = this.mensajes.filter(m => !String(m.id).startsWith('tmp')); return reales.length ? reales[reales.length - 1].id : 0; },
                async actualizar() {
                    try {
                        const r = await fetch(@js(route('chatbot.mensajes')) + '?desde=' + this.ultimoReal(), { headers: { 'Accept': 'application/json' } });
                        const datos = await r.json();
                        if (datos.mensajes.length) { this.mensajes.push(...datos.mensajes); this.bajar(); }
                        this.derivado = datos.derivado;
                    } catch (e) {}
                },
                bajar() { this.$nextTick(() => { this.$refs.lista.scrollTop = this.$refs.lista.scrollHeight; }); },
                // Escapa el texto y convierte las URL en enlaces.
                enlazar(t) {
                    const seguro = String(t).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
                    return seguro.replace(/(https?:\/\/[^\s<]+)/g, '<a href="$1" class="font-semibold underline" target="_blank" rel="noopener">$1</a>');
                },
            };
        }
    </script>
</body>
</html>
