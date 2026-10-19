@extends('layouts.reserva')

@php
    /*
     | DATOS DE EJEMPLO (solo frontend).
     | El controlador debe pasar:
     |   $tour          -> Tour presentado (nom, cod, img, dur_txt, dif_txt, pre_bas, categoria->nom)
     |   $salida        -> salidas_tour elegida (id, fec_sal, hra_sal)
     |   $participantes -> participantes_reserva ya registrados (id, nom_cpl)
     |   $equipos       -> tipos_equipo habilitados para el tour (equipo_por_tour.act = true) con:
     |                     tip_eqp_id, nom, des, eqp_cri, ico (opcional, clase Font Awesome),
     |                     pre = equipo_por_tour.pre_alq_esp ?? tipos_equipo.pre_alq_bas,
     |                     items = items_equipo del tipo: id (itm_eqp_id), tal, cnt_dis
     |   $seleccion     -> alquileres ya elegidos: [ptc_rsv_id => [tip_eqp_id => itm_eqp_id]]
     |   $accionForm    -> route('reservas.equipo.store')
     | Se envía: alquileres[ptc_rsv_id][tip_eqp_id] = itm_eqp_id (vacío = no alquila). El precio (pre) lo fija el controlador.
     */
    $tour ??= (object) [
        'nom' => 'Valle de la Luna', 'cod' => 'TUR002', 'img' => asset('images/tours/valle-de-la-luna.jpg'),
        'img_alt' => 'Valle de la Luna', 'dur_txt' => '5', 'dif_txt' => 'Moderado', 'pre_bas' => 130.00,
        'categoria' => (object) ['nom' => 'Hiking'],
    ];
    $salida ??= ['id' => 11, 'fec_sal' => now()->addDays(2)->toDateString(), 'hra_sal' => '08:00:00'];
    $participantes ??= [
        ['id' => 101, 'nom_cpl' => 'Ana Quispe Mamani'],
        ['id' => 102, 'nom_cpl' => 'Lucas Martin'],
    ];
    $equipos ??= [
        ['tip_eqp_id' => 1, 'nom' => 'Mochila de excursión', 'des' => 'Mochila de 30 L para el recorrido', 'pre' => 20.00, 'eqp_cri' => false, 'ico' => 'fa-solid fa-suitcase-rolling',
         'items' => [['id' => 1, 'tal' => 'Única', 'cnt_dis' => 6]]],
        ['tip_eqp_id' => 2, 'nom' => 'Bastón de trekking', 'des' => 'Par de bastones regulables', 'pre' => 10.00, 'eqp_cri' => false, 'ico' => 'fa-solid fa-person-hiking',
         'items' => [['id' => 2, 'tal' => 'Regulable', 'cnt_dis' => 10]]],
        ['tip_eqp_id' => 3, 'nom' => 'Casco de seguridad', 'des' => 'Protección certificada para actividades', 'pre' => 15.00, 'eqp_cri' => true, 'ico' => 'fa-solid fa-helmet-safety',
         'items' => [['id' => 3, 'tal' => 'S', 'cnt_dis' => 1], ['id' => 4, 'tal' => 'M', 'cnt_dis' => 4], ['id' => 5, 'tal' => 'L', 'cnt_dis' => 0]]],
        ['tip_eqp_id' => 6, 'nom' => 'Chaqueta impermeable', 'des' => 'Cortavientos para el frío del altiplano', 'pre' => 25.00, 'eqp_cri' => false, 'ico' => 'fa-solid fa-shirt',
         'items' => [['id' => 6, 'tal' => 'S', 'cnt_dis' => 2], ['id' => 7, 'tal' => 'M', 'cnt_dis' => 3], ['id' => 8, 'tal' => 'L', 'cnt_dis' => 2], ['id' => 9, 'tal' => 'XL', 'cnt_dis' => 0]]],
    ];
    $seleccion ??= [];
    $accionForm ??= url('/reservas/preview/ficha'); // controlador: route('reservas.equipo.store')
    $urlAnterior ??= url('/reservas/preview/participantes'); // controlador: route('reservas.participantes')

    // Estado inicial para Alpine: "ptc-tipo" => itm_eqp_id
    $sel = [];
    foreach ($participantes as $p) {
        foreach ($equipos as $e) {
            $sel[$p['id'] . '-' . $e['tip_eqp_id']] = (string) old("alquileres.{$p['id']}.{$e['tip_eqp_id']}", $seleccion[$p['id']][$e['tip_eqp_id']] ?? '');
        }
    }
    // Catálogo de ítems para calcular precio y stock en vivo: itm_eqp_id => [pre, cnt_dis]
    $items = [];
    foreach ($equipos as $e) {
        foreach ($e['items'] as $it) {
            $items[$it['id']] = ['pre' => (float) $e['pre'], 'dis' => (int) $it['cnt_dis']];
        }
    }
@endphp

@section('titulo', 'Alquiler de equipo · ' . $tour->nom)

@section('hero')
    <x-reserva.hero :tour="$tour" etiqueta="Paso 3 · Opcional" titulo="¿Necesitan equipo?"
                    subtitulo="Alquila lo que te falte y lo recibes al iniciar el tour. Si ya tienes tu equipo, continúa sin elegir nada." />
@endsection

@section('stepper')
    <x-reserva.stepper :actual="3" :enlaces="[url('/reservas/preview/salida'), $urlAnterior]" />
@endsection

@section('contenido')
<form method="POST" action="{{ $accionForm }}"
      x-data="equipoForm(@js($sel), @js($items), {{ (int) ($participantes[0]['id'] ?? 0) }})"
      class="grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start lg:gap-8">
    @csrf

    <div class="min-w-0 space-y-6">
        @error('alquileres') <p class="rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ $message }}</p> @enderror

        {{-- Pestañas por participante --}}
        <div class="rsv-sin-barra -mx-4 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0" role="tablist">
            @foreach ($participantes as $n => $p)
                <button type="button" role="tab" @click="tab = {{ $p['id'] }}" :aria-selected="tab === {{ $p['id'] }}"
                        class="flex shrink-0 items-center gap-2.5 rounded-2xl border-2 px-4 py-2.5 text-left text-sm transition"
                        :class="tab === {{ $p['id'] }} ? 'border-pipocas-principal bg-pipocas-principal text-white shadow-lg shadow-pipocas-principal/20' : 'border-transparent bg-white text-pipocas-texto shadow-sm hover:border-pipocas-complementario/40'">
                    <span class="grid h-8 w-8 place-items-center rounded-xl text-xs font-bold"
                          :class="tab === {{ $p['id'] }} ? 'bg-white/20' : 'bg-pipocas-fondo text-pipocas-principal'">
                        {{ collect(explode(' ', $p['nom_cpl']))->take(2)->map(fn ($x) => mb_substr($x, 0, 1))->join('') }}
                    </span>
                    <span class="leading-tight">
                        <span class="block font-semibold">{{ \Illuminate\Support\Str::before($p['nom_cpl'] . ' ', ' ') }}</span>
                        <span class="block text-xs opacity-75" x-text="cuenta({{ $p['id'] }}) ? cuenta({{ $p['id'] }}) + ' artículo(s) · Bs. ' + fmt(subtotalDe({{ $p['id'] }})) : 'Sin equipo'"></span>
                    </span>
                </button>
            @endforeach
        </div>

        @foreach ($participantes as $n => $p)
            <section class="rsv-tarjeta" x-show="tab === {{ $p['id'] }}" @if ($n > 0) x-cloak @endif role="tabpanel">
                <header class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <span class="rsv-icono"><i class="fa-solid fa-person-hiking"></i></span>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-pipocas-texto-2">Equipo para</p>
                            <h2 class="font-titulo text-xl font-semibold">{{ $p['nom_cpl'] }}</h2>
                        </div>
                    </div>
                    <span class="rsv-chip bg-pipocas-fondo text-sm text-pipocas-principal" x-text="'Subtotal Bs. ' + fmt(subtotalDe({{ $p['id'] }}))"></span>
                </header>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($equipos as $e)
                        @php
                            $clave = $p['id'] . '-' . $e['tip_eqp_id'];
                            $campo = "alquileres.{$p['id']}.{$e['tip_eqp_id']}";
                            $nombre = "alquileres[{$p['id']}][{$e['tip_eqp_id']}]";
                            $sinStock = collect($e['items'])->sum('cnt_dis') === 0;
                        @endphp
                        <fieldset class="flex flex-col rounded-2xl border-2 p-4 transition"
                                  :class="sel['{{ $clave }}'] ? 'border-pipocas-principal bg-pipocas-principal/[0.03] shadow-md' : 'border-gray-200'">
                            <legend class="sr-only">{{ $e['nom'] }} para {{ $p['nom_cpl'] }}</legend>
                            <div class="flex items-start gap-3">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl text-lg transition"
                                      :class="sel['{{ $clave }}'] ? 'bg-pipocas-principal text-white' : 'bg-pipocas-secundario/10 text-pipocas-secundario'">
                                    <i class="{{ $e['ico'] ?? 'fa-solid fa-toolbox' }}"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold leading-tight">{{ $e['nom'] }}</p>
                                    <p class="mt-0.5 text-xs text-pipocas-texto-2">{{ $e['des'] }}</p>
                                </div>
                                <span class="shrink-0 text-right leading-tight">
                                    <b class="block font-titulo text-lg text-pipocas-principal">Bs. {{ number_format($e['pre'], 0, ',', '.') }}</b>
                                    <span class="text-[10px] text-pipocas-texto-2">por tour</span>
                                </span>
                            </div>
                            @if ($e['eqp_cri'])
                                <p class="mt-3 flex items-center gap-1.5 text-xs font-semibold text-pipocas-secundario"><i class="fa-solid fa-shield-heart"></i> Recomendado por seguridad</p>
                            @endif

                            <div class="mt-4 flex flex-wrap gap-2">
                                @if ($sinStock)
                                    <span class="rsv-chip bg-gray-100 text-gray-500"><i class="fa-solid fa-box-open"></i> Sin stock por ahora</span>
                                @else
                                    <label class="rsv-pastilla" :class="!sel['{{ $clave }}'] && 'rsv-pastilla-activa'">
                                        <input type="radio" name="{{ $nombre }}" value="" x-model="sel['{{ $clave }}']" class="sr-only"> No
                                    </label>
                                    @foreach ($e['items'] as $it)
                                        <label class="rsv-pastilla min-w-[3rem]"
                                               :class="{ 'rsv-pastilla-activa': sel['{{ $clave }}'] == {{ $it['id'] }}, '!cursor-not-allowed !border-dashed opacity-40 line-through': libres({{ $it['id'] }}, '{{ $clave }}') <= 0 }"
                                               title="{{ $it['cnt_dis'] }} disponibles">
                                            <input type="radio" name="{{ $nombre }}" value="{{ $it['id'] }}" x-model="sel['{{ $clave }}']" class="sr-only"
                                                   :disabled="libres({{ $it['id'] }}, '{{ $clave }}') <= 0">
                                            {{ $it['tal'] }}
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                            <p class="mt-2 text-xs" x-show="sel['{{ $clave }}']" x-cloak>
                                <span :class="libres(sel['{{ $clave }}'], '{{ $clave }}') <= 1 ? 'font-semibold text-pipocas-secundario' : 'text-pipocas-texto-2'"
                                      x-text="'Quedan ' + (libres(sel['{{ $clave }}'], '{{ $clave }}') - 1) + ' en esta talla'"></span>
                            </p>
                            @error($campo) <p class="rsv-error">{{ $message }}</p> @enderror
                        </fieldset>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
            <a href="{{ $urlAnterior }}" class="rsv-btn-sec"><i class="fa-solid fa-arrow-left"></i> Participantes</a>
            <button type="submit" class="rsv-btn">
                <span x-text="total() > 0 ? 'Continuar a la ficha médica' : 'Continuar sin equipo'">Continuar a la ficha médica</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <aside class="space-y-4 lg:sticky lg:top-24">
        <x-reserva.resumen-tour :tour="$tour" :salida="$salida" :cnt="count($participantes)" extra-expr="total()" />
        <div class="flex gap-3 rounded-3xl border border-pipocas-principal/10 bg-white/70 p-5 text-sm">
            <i class="fa-solid fa-box-open mt-0.5 text-lg text-pipocas-secundario"></i>
            <p class="text-pipocas-texto-2"><b class="text-pipocas-texto">Entrega y devolución.</b> El guía entrega el equipo al inicio del tour y lo recoge al finalizar.</p>
        </div>
    </aside>
</form>
@endsection

@push('scripts')
<script>
    // Alquiler de equipo: precio y stock en vivo (el stock real lo valida el controlador)
    function equipoForm(sel, items, primero) {
        return {
            tab: primero,
            sel: sel,
            items: items,
            usados(id, excepto) {
                return Object.entries(this.sel).filter(([k, v]) => k !== excepto && String(v) === String(id)).length;
            },
            libres(id, clave) {
                const it = this.items[id];
                return it ? it.dis - this.usados(id, clave) : 0;
            },
            subtotalDe(ptc) {
                return Object.entries(this.sel)
                    .filter(([k, v]) => v && k.startsWith(ptc + '-'))
                    .reduce((s, [, v]) => s + (this.items[v]?.pre || 0), 0);
            },
            cuenta(ptc) {
                return Object.entries(this.sel).filter(([k, v]) => v && k.startsWith(ptc + "-")).length;
            },
            total() {
                return Object.values(this.sel).reduce((s, v) => s + (v ? (this.items[v]?.pre || 0) : 0), 0);
            },
            fmt(n) { return n.toLocaleString('es-BO', { minimumFractionDigits: 2 }); },
        };
    }
</script>
@endpush
