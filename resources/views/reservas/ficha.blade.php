@extends('layouts.reserva')

@php
    /*
     | DATOS DE EJEMPLO (solo frontend).
     | El controlador debe pasar:
     |   $tour          -> Tour presentado (nom, req_fis, alt_max_m)
     |   $participantes -> participantes_reserva de la reserva (id, nom_cpl, eda)
     |   $fichas        -> fichas_medicas existentes indexadas por ptc_rsv_id (con_med, nom_ctc_eme, tel_ctc_eme)
     |   $accionForm    -> route('reservas.ficha.store')
     | Se envía por participante: fichas[ptc_rsv_id][con_med|nom_ctc_eme|tel_ctc_eme],
     |   fichas[ptc_rsv_id][acp_ver] y fichas[ptc_rsv_id][acp_rie] (casillas, solo para validar 'accepted'),
     |   fichas[ptc_rsv_id][fir_b64] = firma en PNG base64 (data:image/png;base64,...).
     | El controlador guarda la imagen en storage y pone su ruta en fichas_medicas.rta_fir,
     | además de fir_en = now() y dir_ip = $request->ip().
     */
    $tour ??= (object) [
        'nom' => 'Valle de la Luna', 'alt_max_m' => 3500,
        'req_fis' => 'Condición física moderada. Caminata de 2 a 3 horas con ascensos cortos a más de 3.000 m s. n. m.',
    ];
    $participantes ??= [
        ['id' => 101, 'nom_cpl' => 'Ana Quispe Mamani', 'eda' => 29],
        ['id' => 102, 'nom_cpl' => 'Lucas Martin', 'eda' => 34],
    ];
    $fichas ??= [];
    $accionForm ??= url('/reservas/preview/resumen'); // controlador: route('reservas.ficha.store')
    $urlAnterior ??= url('/reservas/preview/equipo'); // controlador: route('reservas.equipo')
@endphp

@section('titulo', 'Ficha médica')

@section('hero')
    <x-reserva.hero :tour="$tour" etiqueta="Paso 4" titulo="Ficha médica y consentimiento"
                    subtitulo="Cada participante completa y firma su ficha. Esta información es confidencial y solo la ve el guía de la salida." />
@endsection

@section('stepper')
    <x-reserva.stepper :actual="4" :enlaces="[url('/reservas/preview/salida'), url('/reservas/preview/participantes'), $urlAnterior]" />
@endsection

@section('contenido')
<form method="POST" action="{{ $accionForm }}" class="grid gap-6 lg:grid-cols-[1fr_300px] lg:items-start lg:gap-8">
    @csrf

    <aside class="space-y-4 lg:sticky lg:top-24 lg:order-last">
        @if (! empty($tour->req_fis))
            <div class="rounded-3xl bg-gradient-to-br from-pipocas-acento/40 to-pipocas-acento/10 p-5 text-sm ring-1 ring-pipocas-acento/50">
                <p class="flex items-center gap-2 font-bold text-pipocas-calido"><i class="fa-solid fa-heart-pulse text-lg text-pipocas-secundario"></i> Requisitos físicos</p>
                <p class="mt-2 text-pipocas-calido/90">{{ $tour->req_fis }}</p>
                @if (! empty($tour->alt_max_m))
                    <p class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/70 px-3 py-1 text-xs font-bold text-pipocas-calido"><i class="fa-solid fa-mountain"></i> Hasta {{ number_format($tour->alt_max_m, 0, ',', '.') }} m s. n. m.</p>
                @endif
            </div>
        @endif
        <div class="hidden rounded-3xl border border-pipocas-principal/10 bg-white/70 p-5 text-sm lg:block">
            <p class="mb-3 font-bold">Consejos para la altura</p>
            <ul class="space-y-2.5 text-pipocas-texto-2">
                <li class="flex gap-2.5"><i class="fa-solid fa-glass-water mt-0.5 w-4 text-pipocas-complementario"></i> Hidrátate bien el día anterior.</li>
                <li class="flex gap-2.5"><i class="fa-solid fa-mug-hot mt-0.5 w-4 text-pipocas-complementario"></i> Un mate de coca ayuda a aclimatarte.</li>
                <li class="flex gap-2.5"><i class="fa-solid fa-bed mt-0.5 w-4 text-pipocas-complementario"></i> Descansa y evita el alcohol.</li>
            </ul>
        </div>
    </aside>

    <div class="min-w-0 space-y-6">

    @foreach ($participantes as $n => $p)
        @php
            $id = $p['id'];
            $f = $fichas[$id] ?? [];
        @endphp
        <section class="rsv-tarjeta">
            <header class="mb-6 flex items-center gap-4 border-b border-gray-100 pb-5">
                <span class="rsv-icono text-sm font-bold">{{ collect(explode(' ', $p['nom_cpl']))->take(2)->map(fn ($x) => mb_substr($x, 0, 1))->join('') }}</span>
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-pipocas-texto-2">Ficha {{ $n + 1 }} de {{ count($participantes) }}</p>
                    <h2 class="truncate font-titulo text-xl font-semibold">{{ $p['nom_cpl'] }}</h2>
                </div>
                @if (! empty($p['eda']))<span class="rsv-chip bg-pipocas-fondo text-pipocas-texto-2">{{ $p['eda'] }} años</span>@endif
            </header>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2" x-data="{ txt: @js(old("fichas.$id.con_med", $f['con_med'] ?? '')), add(v) { this.txt = (v === 'Ninguna' || !this.txt || this.txt === 'Ninguna') ? v : (this.txt.includes(v) ? this.txt : this.txt + ', ' + v) } }">
                    <label for="con_med_{{ $id }}" class="rsv-etiqueta"><i class="fa-solid fa-notes-medical mr-1 text-pipocas-secundario"></i> Condiciones médicas, alergias o medicación</label>
                    <div class="mb-2 flex flex-wrap gap-2">
                        @foreach (['Ninguna', 'Asma', 'Hipertensión', 'Diabetes', 'Alergias', 'Problemas cardíacos', 'Embarazo'] as $chip)
                            <button type="button" @click="add(@js($chip))"
                                    class="rsv-chip border transition"
                                    :class="txt.includes(@js($chip)) ? 'border-pipocas-principal bg-pipocas-principal text-white' : 'border-gray-200 bg-white text-pipocas-texto-2 hover:border-pipocas-complementario'">
                                {{ $chip }}
                            </button>
                        @endforeach
                    </div>
                    <textarea id="con_med_{{ $id }}" name="fichas[{{ $id }}][con_med]" rows="3" class="rsv-campo" x-model="txt"
                              placeholder="Detalla aquí cualquier condición, alergia o medicación. Escribe «Ninguna» si no aplica."></textarea>
                    @error("fichas.$id.con_med") <p class="rsv-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nom_ctc_eme_{{ $id }}" class="rsv-etiqueta">Contacto de emergencia</label>
                    <input id="nom_ctc_eme_{{ $id }}" name="fichas[{{ $id }}][nom_ctc_eme]" type="text" maxlength="150" class="rsv-campo"
                           value="{{ old("fichas.$id.nom_ctc_eme", $f['nom_ctc_eme'] ?? '') }}" placeholder="Nombre y parentesco">
                    @error("fichas.$id.nom_ctc_eme") <p class="rsv-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tel_ctc_eme_{{ $id }}" class="rsv-etiqueta">Teléfono de emergencia</label>
                    <input id="tel_ctc_eme_{{ $id }}" name="fichas[{{ $id }}][tel_ctc_eme]" type="tel" maxlength="20" class="rsv-campo"
                           value="{{ old("fichas.$id.tel_ctc_eme", $f['tel_ctc_eme'] ?? '') }}" placeholder="+591 70000000">
                    @error("fichas.$id.tel_ctc_eme") <p class="rsv-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 space-y-3 rounded-2xl border border-pipocas-principal/10 bg-pipocas-fondo/70 p-4 text-sm">
                <p class="flex items-center gap-2 font-bold text-pipocas-principal"><i class="fa-solid fa-file-shield"></i> Consentimiento</p>
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="checkbox" name="fichas[{{ $id }}][acp_ver]" value="1" class="rsv-check mt-0.5" @checked(old("fichas.$id.acp_ver"))>
                    <span>Declaro que la información médica es verdadera y que estoy en condiciones de realizar esta actividad a la altura indicada.</span>
                </label>
                @error("fichas.$id.acp_ver") <p class="rsv-error pl-8">{{ $message }}</p> @enderror
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="checkbox" name="fichas[{{ $id }}][acp_rie]" value="1" class="rsv-check mt-0.5" @checked(old("fichas.$id.acp_rie"))>
                    <span>Conozco los riesgos de la actividad (mal de altura, clima, terreno) y autorizo al guía a solicitar atención médica si fuera necesario.</span>
                </label>
                @error("fichas.$id.acp_rie") <p class="rsv-error pl-8">{{ $message }}</p> @enderror
            </div>

            {{-- Firma digital --}}
            <div class="mt-5" x-data="firmaPad(@js(old("fichas.$id.fir_b64", '')))">
                <div class="mb-1.5 flex items-center justify-between">
                    <span class="rsv-etiqueta mb-0 flex items-center gap-2"><i class="fa-solid fa-signature text-pipocas-secundario"></i> Firma digital <span x-show="firmado" x-cloak class="rsv-chip bg-emerald-50 text-emerald-700"><i class="fa-solid fa-check"></i> Firmado</span></span>
                    <button type="button" @click="limpiar()" x-show="firmado" x-cloak
                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-sm font-semibold text-pipocas-secundario transition hover:bg-pipocas-secundario/10">
                        <i class="fa-solid fa-eraser"></i> Limpiar
                    </button>
                </div>
                <div class="relative overflow-hidden rounded-2xl border-2 border-dashed bg-white transition"
                     :class="firmado ? 'border-pipocas-principal' : 'border-gray-300'">
                    <canvas x-ref="lienzo" class="block h-44 w-full cursor-crosshair touch-none sm:h-48" aria-label="Área para firmar"></canvas>
                    <div x-show="!firmado" class="pointer-events-none absolute inset-0 grid place-items-center text-sm text-gray-400">
                        <span><i class="fa-solid fa-signature mr-1.5"></i> Firma aquí con el dedo o el mouse</span>
                    </div>
                    <div class="pointer-events-none absolute inset-x-6 bottom-8 border-b border-gray-200"></div>
                </div>
                <input type="hidden" name="fichas[{{ $id }}][fir_b64]" x-ref="campo" :value="valor">
                @error("fichas.$id.fir_b64") <p class="rsv-error">{{ $message }}</p> @enderror
                <p class="mt-1.5 text-xs text-pipocas-texto-2">Se registrará la fecha, hora y dirección IP de la firma.</p>
            </div>
        </section>
    @endforeach

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
        <a href="{{ $urlAnterior }}" class="rsv-btn-sec"><i class="fa-solid fa-arrow-left"></i> Equipo</a>
        <button type="submit" class="rsv-btn">Revisar mi reserva <i class="fa-solid fa-arrow-right"></i></button>
    </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Firma digital en <canvas> sin librerías: mouse, lápiz y táctil (Pointer Events)
    function firmaPad(inicial) {
        return {
            valor: inicial || '',
            firmado: !!inicial,
            ctx: null,
            dibujando: false,
            init() {
                const c = this.$refs.lienzo;
                this.ctx = c.getContext('2d');
                this.ajustar();
                window.addEventListener('resize', () => this.ajustar());

                c.addEventListener('pointerdown', e => {
                    this.dibujando = true;
                    c.setPointerCapture(e.pointerId);
                    const p = this.punto(e);
                    this.ctx.beginPath();
                    this.ctx.moveTo(p.x, p.y);
                    this.ctx.lineTo(p.x + 0.1, p.y + 0.1);
                    this.ctx.stroke();
                });
                c.addEventListener('pointermove', e => {
                    if (!this.dibujando) return;
                    const p = this.punto(e);
                    this.ctx.lineTo(p.x, p.y);
                    this.ctx.stroke();
                });
                const fin = () => {
                    if (!this.dibujando) return;
                    this.dibujando = false;
                    this.firmado = true;
                    this.valor = c.toDataURL('image/png');
                };
                c.addEventListener('pointerup', fin);
                c.addEventListener('pointercancel', fin);
            },
            // Ajusta el lienzo al ancho real (y a pantallas retina) conservando la firma
            ajustar() {
                const c = this.$refs.lienzo, r = window.devicePixelRatio || 1;
                const previa = this.valor;
                c.width = c.offsetWidth * r;
                c.height = c.offsetHeight * r;
                this.ctx.setTransform(r, 0, 0, r, 0, 0);
                this.ctx.lineWidth = 2.4;
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';
                this.ctx.strokeStyle = '#1F5F8B';
                if (previa) {
                    const img = new Image();
                    img.onload = () => this.ctx.drawImage(img, 0, 0, c.offsetWidth, c.offsetHeight);
                    img.src = previa;
                }
            },
            punto(e) {
                const b = this.$refs.lienzo.getBoundingClientRect();
                return { x: e.clientX - b.left, y: e.clientY - b.top };
            },
            limpiar() {
                const c = this.$refs.lienzo;
                this.ctx.clearRect(0, 0, c.width, c.height);
                this.valor = '';
                this.firmado = false;
            },
        };
    }
</script>
@endpush
