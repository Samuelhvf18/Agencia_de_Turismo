@extends('layouts.reserva')

@php
    /*
     | DATOS DE EJEMPLO (solo frontend).
     | El controlador debe pasar:
     |   $tour    -> Tour "presentado" como en CatalogoController::presentar() (nom, cod, rta_amg, img, img_alt,
     |               dur_txt, dif_txt, pre_bas, cap_max, categoria->nom)
     |   $salidas -> salidas_tour del tour con est = 'programada' y fec_sal >= hoy, ordenadas por fec_sal, hra_sal.
     |               Cada una con: id, fec_sal, hra_sal, cap_max, est y 'cup_ocu' (suma de reservas.cnt_ptc
     |               de reservas no canceladas de esa salida, calculado en el controlador).
     |   $accionForm -> route('reservas.salida.store', $tour)  (POST con sal_tur_id y cnt_ptc)
     */
    $tour ??= (object) [
        'nom' => 'Valle de la Luna', 'cod' => 'TUR002', 'rta_amg' => 'valle-de-la-luna',
        'img' => asset('images/tours/valle-de-la-luna.jpg'), 'img_alt' => 'Valle de la Luna, La Paz',
        'dur_txt' => '5', 'dif_txt' => 'Moderado', 'pre_bas' => 130.00, 'cap_max' => 12,
        'categoria' => (object) ['nom' => 'Hiking'],
    ];
    $salidas ??= [
        ['id' => 11, 'fec_sal' => now()->addDays(2)->toDateString(),  'hra_sal' => '08:00:00', 'cap_max' => 12, 'est' => 'programada', 'cup_ocu' => 4],
        ['id' => 12, 'fec_sal' => now()->addDays(2)->toDateString(),  'hra_sal' => '14:30:00', 'cap_max' => 12, 'est' => 'programada', 'cup_ocu' => 12],
        ['id' => 13, 'fec_sal' => now()->addDays(4)->toDateString(),  'hra_sal' => '08:00:00', 'cap_max' => 12, 'est' => 'programada', 'cup_ocu' => 10],
        ['id' => 14, 'fec_sal' => now()->addDays(6)->toDateString(),  'hra_sal' => '09:00:00', 'cap_max' => 10, 'est' => 'programada', 'cup_ocu' => 1],
        ['id' => 15, 'fec_sal' => now()->addDays(9)->toDateString(),  'hra_sal' => '08:00:00', 'cap_max' => 12, 'est' => 'programada', 'cup_ocu' => 0],
        ['id' => 16, 'fec_sal' => now()->addDays(12)->toDateString(), 'hra_sal' => '07:30:00', 'cap_max' => 12, 'est' => 'programada', 'cup_ocu' => 7],
    ];
    $accionForm ??= url('/reservas/preview/participantes'); // controlador: route('reservas.salida.store', $tour)

    // Salidas agrupadas por fecha para la vista
    $porFecha = collect($salidas)->groupBy(fn ($s) => $s['fec_sal']);
    $maxCupo = (int) collect($salidas)->map(fn ($s) => $s['cap_max'] - $s['cup_ocu'])->max();
    $urlTour = ! empty($tour->rta_amg) ? url('/tours/' . $tour->rta_amg) : url('/'); // controlador: route('tours.show', $tour)
@endphp

@section('titulo', 'Elige tu salida · ' . $tour->nom)

@section('hero')
    <x-reserva.hero :tour="$tour" etiqueta="Reserva" titulo="¿Cuándo quieres ir?"
                    subtitulo="Elige una de las salidas programadas. Los cupos se actualizan en tiempo real y aún no se cobra nada."
                    :volver="['url' => $urlTour, 'texto' => 'Volver al tour']">
        <ul class="flex flex-wrap gap-2 text-sm">
            <li class="rsv-chip bg-white/15 py-1.5 text-white backdrop-blur"><i class="fa-regular fa-clock text-pipocas-acento"></i> {{ $tour->dur_txt }} h</li>
            <li class="rsv-chip bg-white/15 py-1.5 text-white backdrop-blur"><i class="fa-solid fa-signal text-pipocas-acento"></i> {{ $tour->dif_txt }}</li>
            <li class="rsv-chip bg-pipocas-acento py-1.5 font-bold text-pipocas-calido">Desde Bs. {{ number_format($tour->pre_bas, 0, ',', '.') }}</li>
        </ul>
    </x-reserva.hero>
@endsection

@section('stepper')
    <x-reserva.stepper :actual="1" />
@endsection

@section('contenido')
<form method="POST" action="{{ $accionForm }}"
      x-data="{ sal: {{ (int) old('sal_tur_id', 0) }}, cnt: {{ (int) old('cnt_ptc', 1) }}, disp: 0, dia: '' }"
      class="grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start lg:gap-8">
    @csrf

    <div class="min-w-0 space-y-6">
        @error('sal_tur_id')
            <p class="rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
        @enderror

        {{-- Tira de fechas (filtro rápido) --}}
        @if ($porFecha->count() > 1)
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="flex items-center gap-2 font-bold"><i class="fa-regular fa-calendar text-pipocas-secundario"></i> Próximas fechas</h2>
                    <button type="button" x-show="dia" x-cloak @click="dia = ''" class="text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover">Ver todas</button>
                </div>
                <div class="rsv-sin-barra -mx-4 flex snap-x gap-3 overflow-x-auto px-4 pb-3 pt-1 sm:mx-0 sm:px-1">
                    @foreach ($porFecha as $fecha => $lista)
                        @php
                            $f = \Carbon\Carbon::parse($fecha)->locale('es');
                            $libresDia = $lista->sum(fn ($s) => max(0, $s['cap_max'] - $s['cup_ocu']));
                        @endphp
                        <button type="button" @click="dia = dia === '{{ $fecha }}' ? '' : '{{ $fecha }}'"
                                class="w-20 shrink-0 snap-start rounded-2xl border-2 px-2 py-3 text-center transition hover:-translate-y-0.5"
                                :class="dia === '{{ $fecha }}' ? 'border-pipocas-principal bg-pipocas-principal text-white shadow-lg shadow-pipocas-principal/25' : 'border-transparent bg-white shadow-tarjeta'">
                            <span class="block text-[11px] font-bold uppercase opacity-70">{{ $f->translatedFormat('D') }}</span>
                            <span class="block font-titulo text-2xl font-semibold leading-tight">{{ $f->format('j') }}</span>
                            <span class="block text-[11px] font-semibold uppercase opacity-70">{{ $f->translatedFormat('M') }}</span>
                            <span @class(['mx-auto mt-1.5 block h-1.5 w-1.5 rounded-full', 'bg-gray-300' => $libresDia === 0, 'bg-pipocas-secundario' => $libresDia > 0 && $libresDia <= 3, 'bg-emerald-500' => $libresDia > 3])></span>
                        </button>
                    @endforeach
                </div>
                <p class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-pipocas-texto-2">
                    <span class="inline-flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Disponible</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-pipocas-secundario"></span> Últimos cupos</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-gray-300"></span> Agotado</span>
                </p>
            </div>
        @endif

        @forelse ($porFecha as $fecha => $lista)
            @php $f = \Carbon\Carbon::parse($fecha)->locale('es'); @endphp
            <section class="rsv-tarjeta" x-show="!dia || dia === '{{ $fecha }}'" x-transition.opacity>
                <header class="mb-5 flex items-center gap-4">
                    <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-pipocas-principal to-pipocas-complementario text-center leading-none text-white shadow-md shadow-pipocas-principal/20">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-pipocas-acento">{{ $f->translatedFormat('M') }}</span>
                        <span class="font-titulo text-2xl font-semibold">{{ $f->format('j') }}</span>
                    </div>
                    <div>
                        <h2 class="font-titulo text-xl font-semibold">{{ ucfirst($f->translatedFormat('l j \d\e F')) }}</h2>
                        <p class="text-sm text-pipocas-texto-2">{{ ucfirst($f->diffForHumans(['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW])) }} · {{ $lista->count() }} {{ $lista->count() == 1 ? 'horario' : 'horarios' }}</p>
                    </div>
                </header>

                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($lista as $s)
                        @php
                            $libres = max(0, $s['cap_max'] - $s['cup_ocu']);
                            $pct = $s['cap_max'] ? round($s['cup_ocu'] * 100 / $s['cap_max']) : 100;
                            $agotada = $libres === 0;
                            $pocos = ! $agotada && $libres <= 3;
                            $hora = (int) substr($s['hra_sal'], 0, 2);
                            $turno = $hora < 12 ? ['Mañana', 'fa-solid fa-sun'] : ($hora < 18 ? ['Tarde', 'fa-solid fa-cloud-sun'] : ['Noche', 'fa-solid fa-moon']);
                        @endphp
                        <label @class([
                            'group relative block overflow-hidden rounded-2xl border-2 p-4 transition',
                            'cursor-not-allowed border-dashed border-gray-200 bg-gray-50' => $agotada,
                            'cursor-pointer border-gray-200 bg-white hover:-translate-y-0.5 hover:border-pipocas-complementario hover:shadow-lg' => ! $agotada,
                        ])
                            @unless ($agotada)
                                :class="sal === {{ $s['id'] }} && '!border-pipocas-principal bg-pipocas-principal/[0.04] shadow-lg ring-4 ring-pipocas-principal/10'"
                            @endunless>
                            <input type="radio" name="sal_tur_id" value="{{ $s['id'] }}" class="sr-only"
                                   x-model.number="sal" @change="disp = {{ $libres }}; cnt = Math.min(cnt, {{ $libres }})"
                                   @disabled($agotada)>

                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider {{ $agotada ? 'text-gray-400' : 'text-pipocas-secundario' }}">
                                        <i class="{{ $turno[1] }}"></i> {{ $turno[0] }}
                                    </p>
                                    <p @class(['font-titulo text-3xl font-semibold', 'text-gray-400 line-through decoration-1' => $agotada, 'text-pipocas-texto' => ! $agotada])>{{ substr($s['hra_sal'], 0, 5) }}</p>
                                </div>
                                @if ($agotada)
                                    <span class="rsv-chip bg-gray-200 font-extrabold uppercase tracking-wide text-gray-600"><i class="fa-solid fa-lock"></i> Agotado</span>
                                @elseif ($pocos)
                                    <span class="rsv-chip bg-pipocas-acento font-extrabold uppercase tracking-wide text-pipocas-calido"><i class="fa-solid fa-fire"></i> ¡Últimos {{ $libres }}!</span>
                                @else
                                    <span class="grid h-7 w-7 place-items-center rounded-full border-2 border-gray-300 text-xs text-white transition"
                                          :class="sal === {{ $s['id'] }} && '!border-pipocas-principal bg-pipocas-principal'">
                                        <i class="fa-solid fa-check" x-show="sal === {{ $s['id'] }}" x-cloak></i>
                                    </span>
                                @endif
                            </div>

                            <ul class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-pipocas-texto-2">
                                <li><i class="fa-solid fa-location-dot mr-1 text-pipocas-complementario"></i> Salida desde La Paz</li>
                                @if (! empty($s['gui_nom']))<li><i class="fa-solid fa-user-tie mr-1 text-pipocas-complementario"></i> {{ $s['gui_nom'] }}</li>@endif
                            </ul>

                            <div class="mt-4">
                                <div class="mb-1.5 flex justify-between text-xs">
                                    <span class="font-semibold {{ $agotada ? 'text-gray-500' : 'text-pipocas-texto' }}">
                                        <i class="fa-solid fa-user-group mr-1 opacity-60"></i>
                                        {{ $agotada ? 'Sin cupos' : $libres . ' ' . ($libres == 1 ? 'cupo libre' : 'cupos libres') }}
                                    </span>
                                    <span class="text-pipocas-texto-2">{{ $s['cup_ocu'] }}/{{ $s['cap_max'] }} reservados</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-gray-200" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Ocupación">
                                    <div @class([
                                        'h-full rounded-full',
                                        'bg-gray-400' => $agotada,
                                        'bg-gradient-to-r from-pipocas-acento to-pipocas-secundario' => $pocos,
                                        'bg-gradient-to-r from-pipocas-complementario to-pipocas-principal' => ! $agotada && ! $pocos,
                                    ]) style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="rsv-tarjeta py-12 text-center">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-pipocas-secundario/10 text-3xl text-pipocas-secundario"><i class="fa-regular fa-calendar-xmark"></i></span>
                <h2 class="rsv-titulo mt-4">No hay salidas programadas</h2>
                <p class="mx-auto mt-1 max-w-sm text-pipocas-texto-2">Vuelve pronto o escríbenos para armar una salida privada para tu grupo.</p>
                <a href="{{ url('/chatbot') }}" class="rsv-btn-sec mt-5"><i class="fa-regular fa-comments"></i> Hablar con un asesor</a>
            </div>
        @endforelse

        {{-- Cantidad de personas --}}
        <section class="rsv-tarjeta">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span class="rsv-icono"><i class="fa-solid fa-users"></i></span>
                    <div>
                        <h2 class="rsv-titulo">¿Cuántas personas?</h2>
                        <p class="text-sm text-pipocas-texto-2" x-show="!sal">Primero elige un horario.</p>
                        <p class="text-sm text-pipocas-texto-2" x-show="sal" x-cloak>Máximo <b x-text="disp"></b> en esta salida.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-start rounded-2xl bg-pipocas-fondo p-1.5 sm:self-auto">
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-xl bg-white text-pipocas-principal shadow-sm transition hover:bg-pipocas-principal hover:text-white disabled:pointer-events-none disabled:opacity-40"
                            @click="cnt = Math.max(1, cnt - 1)" :disabled="cnt <= 1" aria-label="Quitar una persona">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <input type="number" name="cnt_ptc" min="1" max="{{ $maxCupo }}" x-model.number="cnt" :max="disp || {{ $maxCupo }}"
                           class="w-14 border-0 bg-transparent text-center font-titulo text-2xl font-semibold focus:ring-0" aria-label="Cantidad de participantes">
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-xl bg-white text-pipocas-principal shadow-sm transition hover:bg-pipocas-principal hover:text-white disabled:pointer-events-none disabled:opacity-40"
                            @click="cnt = Math.min(disp || {{ $maxCupo }}, cnt + 1)" :disabled="sal && cnt >= disp" aria-label="Agregar una persona">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
            @error('cnt_ptc') <p class="rsv-error">{{ $message }}</p> @enderror
        </section>
    </div>

    {{-- Resumen: abajo en móvil, columna sticky en escritorio --}}
    <aside class="space-y-4 lg:sticky lg:top-24">
        <x-reserva.resumen-tour :tour="$tour" cnt-expr="cnt">
            <button type="submit" class="rsv-btn w-full" :disabled="!sal">
                Continuar con los participantes <i class="fa-solid fa-arrow-right"></i>
            </button>
            <p class="text-center text-xs text-pipocas-texto-2" x-show="!sal">Elige un horario para continuar</p>
        </x-reserva.resumen-tour>

        <x-reserva.incluye />
    </aside>
</form>
@endsection
