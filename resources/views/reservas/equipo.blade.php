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

@section('stepper')
    <x-reserva.stepper :actual="3" :enlaces="[url('/reservas/preview/salida'), $urlAnterior]" />
@endsection

@section('contenido')
<form method="POST" action="{{ $accionForm }}"
      x-data="equipoForm(@js($sel), @js($items))"
      class="grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start lg:gap-8">
    @csrf

    <div class="space-y-6">
        <div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-pipocas-complementario/10 px-3 py-1 text-xs font-bold text-pipocas-complementario">
                <i class="fa-solid fa-circle-info"></i> Paso opcional
            </span>
            <h1 class="mt-3 font-titulo text-3xl font-semibold leading-tight sm:text-4xl">¿Necesitan equipo?</h1>
            <p class="mt-2 text-pipocas-texto-2">Alquila lo que te falte y lo recibes al iniciar el tour. Si ya tienes tu equipo, continúa sin elegir nada.</p>
        </div>

        @error('alquileres') <p class="rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ $message }}</p> @enderror

        @foreach ($participantes as $n => $p)
            <section class="rsv-tarjeta">
                <header class="mb-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-pipocas-principal font-bold text-white">{{ $n + 1 }}</span>
                        <h2 class="font-bold">{{ $p['nom_cpl'] }}</h2>
                    </div>
                    <span class="text-sm font-semibold text-pipocas-principal" x-text="'Bs. ' + fmt(subtotalDe({{ $p['id'] }}))"></span>
                </header>

                <ul class="divide-y divide-gray-100">
                    @foreach ($equipos as $e)
                        @php
                            $clave = $p['id'] . '-' . $e['tip_eqp_id'];
                            $campo = "alquileres.{$p['id']}.{$e['tip_eqp_id']}";
                            $sinStock = collect($e['items'])->sum('cnt_dis') === 0;
                        @endphp
                        <li class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-3 py-4 sm:grid-cols-[auto_1fr_auto] sm:items-center">
                            <span class="grid h-11 w-11 place-items-center rounded-xl bg-pipocas-fondo text-pipocas-secundario">
                                <i class="{{ $e['ico'] ?? 'fa-solid fa-toolbox' }}"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="flex flex-wrap items-center gap-2 font-semibold">
                                    {{ $e['nom'] }}
                                    @if ($e['eqp_cri'])
                                        <span class="rounded-full bg-pipocas-secundario/15 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-pipocas-secundario" title="Equipo de seguridad recomendado">Recomendado</span>
                                    @endif
                                </p>
                                <p class="text-sm text-pipocas-texto-2">{{ $e['des'] }} · <b class="text-pipocas-texto">Bs. {{ number_format($e['pre'], 2, ',', '.') }}</b></p>
                            </div>
                            <div class="col-span-2 sm:col-span-1 sm:w-48">
                                <label for="eq-{{ $clave }}" class="sr-only">Talla de {{ $e['nom'] }} para {{ $p['nom_cpl'] }}</label>
                                <select id="eq-{{ $clave }}" name="alquileres[{{ $p['id'] }}][{{ $e['tip_eqp_id'] }}]"
                                        x-model="sel['{{ $clave }}']" class="rsv-campo" @disabled($sinStock)>
                                    <option value="">{{ $sinStock ? 'Sin stock' : 'No alquilar' }}</option>
                                    @foreach ($e['items'] as $it)
                                        <option value="{{ $it['id'] }}" :disabled="libres({{ $it['id'] }}, '{{ $clave }}') <= 0">
                                            Talla {{ $it['tal'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs" x-show="sel['{{ $clave }}']" x-cloak>
                                    <span :class="libres(sel['{{ $clave }}'], '{{ $clave }}') <= 1 ? 'text-pipocas-secundario font-semibold' : 'text-pipocas-texto-2'"
                                          x-text="'Quedan ' + (libres(sel['{{ $clave }}'], '{{ $clave }}') - 1) + ' en esta talla'"></span>
                                </p>
                                @error($campo) <p class="rsv-error">{{ $message }}</p> @enderror
                            </div>
                        </li>
                    @endforeach
                </ul>
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

    <aside class="space-y-4 lg:sticky lg:top-6">
        <x-reserva.resumen-tour :tour="$tour" :salida="$salida" :cnt="count($participantes)" extra-expr="total()" />
    </aside>
</form>
@endsection

@push('scripts')
<script>
    // Alquiler de equipo: precio y stock en vivo (el stock real lo valida el controlador)
    function equipoForm(sel, items) {
        return {
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
            total() {
                return Object.values(this.sel).reduce((s, v) => s + (v ? (this.items[v]?.pre || 0) : 0), 0);
            },
            fmt(n) { return n.toLocaleString('es-BO', { minimumFractionDigits: 2 }); },
        };
    }
</script>
@endpush
