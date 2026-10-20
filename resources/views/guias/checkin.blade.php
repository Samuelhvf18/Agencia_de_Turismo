@extends('layouts.campo')

@section('titulo', $salida->tour->nom)

@push('head')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endpush

@section('contenido')
<div x-data="checkin(@js([
        'salida' => $salida->id,
        'pasajeros' => $manifiesto,
        'urlValidar' => route('guias.validar', $salida),
        'urlSincronizar' => route('guias.sincronizar', $salida),
        'urlManifiesto' => route('guias.manifiesto', $salida),
    ]))" class="space-y-6">

    {{-- Estado de conexión --}}
    <div class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold"
         :class="online ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200'">
        <span><i class="fa-solid" :class="online ? 'fa-signal' : 'fa-plane'"></i>
            <span x-text="online ? 'Conectado' : 'Sin conexión: los escaneos se guardan en el teléfono'"></span></span>
        <span x-show="cola.length" x-cloak>
            <span x-text="cola.length"></span> por sincronizar
            <button class="ml-2 underline" @click="sincronizar()" x-show="online">Sincronizar</button>
        </span>
    </div>

    {{-- Salida --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block mb-1">{{ $salida->fec_sal->locale('es')->translatedFormat('l j \d\e F') }} · {{ substr($salida->hra_sal, 0, 5) }}</span>
        <h2 class="text-xl font-extrabold text-slate-900 mb-4">{{ $salida->tour->nom }}</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6 text-sm">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-xs">Abordaron</span>
                <span class="font-bold text-slate-900 text-base"><span x-text="validados()"></span> / <span x-text="pasajeros.length"></span></span>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-xs">Inicio real</span>
                <span class="font-bold text-emerald-600 text-base">{{ $salida->hra_ini_rea?->format('H:i') ?? '—' }}</span>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 col-span-2 sm:col-span-1">
                <span class="text-slate-400 block text-xs">Vehículo / chofer</span>
                <span class="font-bold text-slate-900 text-sm">{{ $salida->vehiculo?->pla ?? '—' }} · {{ $salida->chofer?->usuario?->nom ?? '—' }}</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            @if ($salida->est === 'programada')
                <form method="POST" action="{{ route('guias.iniciar', $salida) }}" class="flex-1">
                    @csrf
                    <button class="w-full py-3 px-4 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition shadow"><i class="fa-solid fa-play"></i> Registrar inicio del recorrido</button>
                </form>
            @elseif ($salida->est === 'en_curso')
                <form method="POST" action="{{ route('guias.finalizar', $salida) }}" class="flex-1 space-y-2" onsubmit="return confirm('¿Finalizar el recorrido?')">
                    @csrf
                    @if ($salida->veh_id)
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" step="0.1" min="0" name="dst_km" placeholder="Km recorridos" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <input type="number" step="0.01" min="0" name="cos_cmb" placeholder="Combustible (Bs.)" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        </div>
                    @endif
                    <button class="w-full py-3 px-4 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700 transition shadow"><i class="fa-solid fa-stop"></i> Registrar fin del recorrido</button>
                </form>
            @endif
            <button type="button" @click="escaneando ? detener() : escanear()" class="py-3 px-6 rounded-xl bg-slate-900 text-white font-semibold hover:bg-amber-600 transition shadow">
                <i class="fa-solid fa-qrcode"></i> <span x-text="escaneando ? 'Detener cámara' : 'Escanear QR'"></span>
            </button>
        </div>
    </div>

    {{-- Lector QR --}}
    <div x-show="escaneando" x-cloak class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <div id="lector" class="overflow-hidden rounded-xl"></div>
    </div>

    {{-- Resultado del último escaneo --}}
    <div x-show="ultimo" x-cloak class="rounded-2xl p-4 text-sm font-semibold"
         :class="ultimo?.ok ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'">
        <i class="fa-solid" :class="ultimo?.ok ? 'fa-circle-check' : 'fa-circle-xmark'"></i>
        <span x-text="(ultimo?.participante ? ultimo.participante + ' — ' : '') + ultimo?.mensaje"></span>
    </div>

    {{-- Ingreso manual --}}
    <form @submit.prevent="procesar(manual); manual = ''" class="flex gap-2">
        <input x-model="manual" placeholder="Código del boleto (BOL-…)" class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm font-mono uppercase">
        <button class="rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white">Validar</button>
    </form>

    {{-- Manifiesto --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Pasajeros</h3>
        <div class="space-y-3">
            <template x-for="p in pasajeros" :key="p.boleto ?? p.nom_cpl">
                <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900 text-sm"><span x-text="p.nom_cpl"></span> <span class="text-xs font-normal text-slate-500" x-text="'(' + p.documento + ')'"></span></p>
                        <p class="text-xs text-amber-600" x-show="p.equipo" x-text="'Equipo: ' + p.equipo"></p>
                        <p class="text-xs text-rose-600 font-semibold" x-show="p.alerta_medica"><i class="fa-solid fa-heart-pulse"></i> <span x-text="p.alerta_medica"></span></p>
                        <p class="text-xs text-slate-400 font-mono" x-text="p.boleto ?? 'Sin boleto'"></p>
                    </div>
                    <span class="shrink-0 px-3 py-1 rounded-full text-xs font-bold"
                          :class="{
                            'bg-emerald-100 text-emerald-800': p.est === 'validado',
                            'bg-amber-100 text-amber-800': p.est === 'pendiente_sync',
                            'bg-slate-200 text-slate-700': p.est === 'emitido',
                            'bg-rose-100 text-rose-700': p.est === 'sin_boleto' || p.est === 'cancelado'
                          }"
                          x-text="{validado: 'Abordó ' + (p.vld_en ?? ''), pendiente_sync: 'Abordó (sin sincronizar)', emitido: 'Pendiente', sin_boleto: 'Sin boleto', cancelado: 'Cancelado'}[p.est] ?? p.est"></span>
                </div>
            </template>
            <p x-show="!pasajeros.length" class="text-sm text-slate-500">No hay pasajeros confirmados.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // IndexedDB mínima: manifiesto por salida y cola de escaneos sin conexión (RNF-15).
    const bd = {
        abrir() {
            return new Promise((ok, mal) => {
                const r = indexedDB.open('pipocas-checkin', 1);
                r.onupgradeneeded = () => {
                    r.result.createObjectStore('manifiestos');
                    r.result.createObjectStore('cola');
                };
                r.onsuccess = () => ok(r.result);
                r.onerror = () => mal(r.error);
            });
        },
        async poner(almacen, clave, valor) {
            const db = await this.abrir();
            return new Promise(ok => { const t = db.transaction(almacen, 'readwrite'); t.objectStore(almacen).put(valor, clave); t.oncomplete = ok; });
        },
        async leer(almacen, clave) {
            const db = await this.abrir();
            return new Promise(ok => { const r = db.transaction(almacen).objectStore(almacen).get(clave); r.onsuccess = () => ok(r.result); r.onerror = () => ok(undefined); });
        },
    };

    function checkin(cfg) {
        return {
            pasajeros: cfg.pasajeros,
            cola: [],
            online: navigator.onLine,
            escaneando: false,
            ultimo: null,
            manual: '',
            lector: null,
            recientes: {},
            token: document.querySelector('meta[name="csrf-token"]').content,

            async init() {
                const claveCola = 'salida-' + cfg.salida;
                if (this.online) {
                    await bd.poner('manifiestos', claveCola, JSON.parse(JSON.stringify(this.pasajeros)));
                } else {
                    this.pasajeros = (await bd.leer('manifiestos', claveCola)) || this.pasajeros;
                }
                this.cola = (await bd.leer('cola', claveCola)) || [];
                this.cola.forEach(e => this.marcar(e.cod, 'pendiente_sync'));

                window.addEventListener('online', () => { this.online = true; this.sincronizar(); });
                window.addEventListener('offline', () => { this.online = false; });
                if (this.online && this.cola.length) this.sincronizar();

                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.register('/sw-checkin.js', { scope: '/guias/' }).catch(() => {});
                }
            },

            validados() { return this.pasajeros.filter(p => ['validado', 'pendiente_sync'].includes(p.est)).length; },

            marcar(cod, est, hora = null) {
                const p = this.pasajeros.find(x => x.boleto === cod);
                if (p) { p.est = est; if (hora) p.vld_en = hora; }
                return p;
            },

            async procesar(cod) {
                cod = (cod || '').trim().toUpperCase();
                if (!cod) return;
                // Evita leer dos veces el mismo QR mientras está frente a la cámara.
                if (this.recientes[cod] && Date.now() - this.recientes[cod] < 3000) return;
                this.recientes[cod] = Date.now();

                if (!this.online) return this.validarSinConexion(cod);

                try {
                    const r = await fetch(cfg.urlValidar, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.token },
                        body: JSON.stringify({ cod }),
                    });
                    const datos = await r.json();
                    this.ultimo = datos;
                    if (datos.ok) this.marcar(cod, 'validado', new Date().toTimeString().slice(0, 5));
                    this.vibrar(datos.ok);
                } catch (e) {
                    this.online = false;
                    this.validarSinConexion(cod);
                }
            },

            async validarSinConexion(cod) {
                const p = this.pasajeros.find(x => x.boleto === cod);
                if (!p) { this.ultimo = { ok: false, mensaje: 'El boleto no está en el manifiesto de esta salida.' }; return this.vibrar(false); }
                if (p.est === 'validado' || p.est === 'pendiente_sync') { this.ultimo = { ok: true, participante: p.nom_cpl, mensaje: 'Ya estaba registrado.' }; return; }
                if (p.est !== 'emitido') { this.ultimo = { ok: false, participante: p.nom_cpl, mensaje: 'Boleto no válido.' }; return this.vibrar(false); }

                this.cola.push({ cod, vld_en: new Date().toISOString() });
                await bd.poner('cola', 'salida-' + cfg.salida, JSON.parse(JSON.stringify(this.cola)));
                this.marcar(cod, 'pendiente_sync', new Date().toTimeString().slice(0, 5));
                this.ultimo = { ok: true, participante: p.nom_cpl, mensaje: 'Registrado sin conexión; se sincronizará.' };
                this.vibrar(true);
            },

            async sincronizar() {
                if (!this.cola.length) return;
                try {
                    const r = await fetch(cfg.urlSincronizar, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.token },
                        body: JSON.stringify({ escaneos: this.cola }),
                    });
                    if (!r.ok) throw new Error('sync');
                    const { resultados } = await r.json();
                    resultados.forEach(res => this.marcar(res.cod, res.ok ? 'validado' : 'emitido'));
                    const fallidos = resultados.filter(res => !res.ok);
                    this.ultimo = fallidos.length
                        ? { ok: false, mensaje: fallidos.length + ' boleto(s) no se pudieron validar: ' + fallidos.map(f => f.cod + ' (' + f.mensaje + ')').join(', ') }
                        : { ok: true, mensaje: resultados.length + ' escaneo(s) sincronizados.' };
                    this.cola = [];
                    await bd.poner('cola', 'salida-' + cfg.salida, []);
                } catch (e) { /* se reintenta al volver la conexión */ }
            },

            async escanear() {
                if (typeof Html5Qrcode === 'undefined') { this.ultimo = { ok: false, mensaje: 'No se pudo cargar el lector QR. Usa el ingreso manual.' }; return; }
                this.escaneando = true;
                await this.$nextTick();
                this.lector = new Html5Qrcode('lector');
                this.lector.start({ facingMode: 'environment' }, { fps: 10, qrbox: 240 }, texto => this.procesar(texto))
                    .catch(() => { this.escaneando = false; this.ultimo = { ok: false, mensaje: 'No se pudo abrir la cámara. Revisa los permisos o usa el ingreso manual.' }; });
            },

            async detener() {
                if (this.lector) { try { await this.lector.stop(); } catch (e) {} }
                this.escaneando = false;
            },

            vibrar(ok) { if (navigator.vibrate) navigator.vibrate(ok ? 80 : [80, 60, 80]); },
        };
    }
</script>
@endpush
