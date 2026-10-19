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
@endphp

@section('titulo', 'Elige tu salida · ' . $tour->nom)

@section('stepper')
    <x-reserva.stepper :actual="1" />
@endsection

@section('contenido')
<form method="POST" action="{{ $accionForm }}"
      x-data="{ sal: {{ (int) old('sal_tur_id', 0) }}, cnt: {{ (int) old('cnt_ptc', 1) }}, disp: 0 }"
      class="grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start lg:gap-8">
    @csrf

    <div class="space-y-6">
        <div>
            <a href="{{ Route::has('tours.show') && isset($tour->id) ? route('tours.show', $tour) : url('/') }}"
               class="mb-3 inline-flex items-center gap-2 text-sm font-semibold text-pipocas-principal hover:text-pipocas-hover">
                <i class="fa-solid fa-arrow-left"></i> Volver al tour
            </a>
            <h1 class="font-titulo text-3xl font-semibold leading-tight text-pipocas-texto sm:text-4xl">¿Cuándo quieres ir?</h1>
            <p class="mt-2 text-pipocas-texto-2">Elige una de las salidas programadas para <b class="text-pipocas-texto">{{ $tour->nom }}</b>. Los cupos se actualizan en tiempo real.</p>
        </div>

        @error('sal_tur_id')
            <p class="rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
        @enderror

        @forelse ($porFecha as $fecha => $lista)
            @php $f = \Carbon\Carbon::parse($fecha)->locale('es'); @endphp
            <section class="rsv-tarjeta">
                <header class="mb-4 flex items-center gap-4">
                    <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-pipocas-principal/10 text-center leading-none text-pipocas-principal">
                        <span class="text-[11px] font-bold uppercase">{{ $f->translatedFormat('M') }}</span>
                        <span class="font-titulo text-2xl font-semibold">{{ $f->format('j') }}</span>
                    </div>
                    <div>
                        <h2 class="font-bold">{{ ucfirst($f->translatedFormat('l j \d\e F')) }}</h2>
                        <p class="text-sm text-pipocas-texto-2">{{ $f->diffForHumans(['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW]) }} · {{ $lista->count() }} {{ $lista->count() == 1 ? 'horario' : 'horarios' }}</p>
                    </div>
                </header>

                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($lista as $s)
                        @php
                            $libres = max(0, $s['cap_max'] - $s['cup_ocu']);
                            $pct = $s['cap_max'] ? round($s['cup_ocu'] * 100 / $s['cap_max']) : 100;
                            $agotada = $libres === 0;
                            $pocos = ! $agotada && $libres <= 3;
                        @endphp
                        <label @class([
                            'relative block rounded-2xl border-2 p-4 transition',
                            'cursor-not-allowed border-gray-200 bg-gray-50 opacity-70' => $agotada,
                            'cursor-pointer border-gray-200 hover:border-pipocas-complementario' => ! $agotada,
                        ])
                            @unless ($agotada)
                                :class="sal === {{ $s['id'] }} && '!border-pipocas-principal bg-pipocas-principal/5 ring-4 ring-pipocas-principal/10'"
                            @endunless>
                            <input type="radio" name="sal_tur_id" value="{{ $s['id'] }}" class="sr-only"
                                   x-model.number="sal" @change="disp = {{ $libres }}; cnt = Math.min(cnt, {{ $libres }})"
                                   @disabled($agotada)>

                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-titulo text-2xl font-semibold text-pipocas-texto">{{ substr($s['hra_sal'], 0, 5) }}</p>
                                    <p class="text-xs text-pipocas-texto-2">Salida desde La Paz</p>
                                </div>
                                @if ($agotada)
                                    <span class="rounded-full bg-gray-200 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wide text-gray-600">Agotado</span>
                                @elseif ($pocos)
                                    <span class="rounded-full bg-pipocas-acento/30 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wide text-pipocas-calido">¡Últimos {{ $libres }}!</span>
                                @else
                                    <span class="grid h-6 w-6 place-items-center rounded-full border-2 border-gray-300 text-[10px] text-white"
                                          :class="sal === {{ $s['id'] }} && '!border-pipocas-principal bg-pipocas-principal'">
                                        <i class="fa-solid fa-check" x-show="sal === {{ $s['id'] }}" x-cloak></i>
                                    </span>
                                @endif
                            </div>

                            <div class="mt-4">
                                <div class="mb-1.5 flex justify-between text-xs">
                                    <span class="font-semibold {{ $agotada ? 'text-gray-500' : 'text-pipocas-texto' }}">
                                        {{ $agotada ? 'Sin cupos' : $libres . ' ' . ($libres == 1 ? 'cupo disponible' : 'cupos disponibles') }}
                                    </span>
                                    <span class="text-pipocas-texto-2">{{ $s['cup_ocu'] }}/{{ $s['cap_max'] }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-gray-200" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Ocupación">
                                    <div @class([
                                        'h-full rounded-full',
                                        'bg-gray-400' => $agotada,
                                        'bg-pipocas-secundario' => $pocos,
                                        'bg-pipocas-complementario' => ! $agotada && ! $pocos,
                                    ]) style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="rsv-tarjeta text-center">
                <i class="fa-regular fa-calendar-xmark text-4xl text-pipocas-secundario"></i>
                <h2 class="rsv-titulo mt-3">No hay salidas programadas</h2>
                <p class="mt-1 text-pipocas-texto-2">Vuelve pronto o escríbenos para armar una salida privada.</p>
            </div>
        @endforelse

        {{-- Cantidad de personas --}}
        <section class="rsv-tarjeta">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="rsv-titulo">¿Cuántas personas?</h2>
                    <p class="text-sm text-pipocas-texto-2" x-show="!sal">Primero elige un horario.</p>
                    <p class="text-sm text-pipocas-texto-2" x-show="sal" x-cloak>Máximo <span x-text="disp"></span> en esta salida.</p>
                </div>
                <div class="flex items-center gap-3 self-start sm:self-auto">
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-xl border border-gray-300 bg-white text-pipocas-principal transition hover:bg-pipocas-fondo disabled:opacity-40"
                            @click="cnt = Math.max(1, cnt - 1)" :disabled="cnt <= 1" aria-label="Quitar una persona">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <input type="number" name="cnt_ptc" min="1" max="{{ $maxCupo }}" x-model.number="cnt" :max="disp || {{ $maxCupo }}"
                           class="rsv-campo w-16 text-center text-lg font-bold" aria-label="Cantidad de participantes">
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-xl border border-gray-300 bg-white text-pipocas-principal transition hover:bg-pipocas-fondo disabled:opacity-40"
                            @click="cnt = Math.min(disp || {{ $maxCupo }}, cnt + 1)" :disabled="sal && cnt >= disp" aria-label="Agregar una persona">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
            @error('cnt_ptc') <p class="rsv-error">{{ $message }}</p> @enderror
        </section>
    </div>

    {{-- Resumen: abajo en móvil, columna sticky en escritorio --}}
    <aside class="lg:sticky lg:top-6">
        <x-reserva.resumen-tour :tour="$tour" cnt-expr="cnt">
            <button type="submit" class="rsv-btn w-full" :disabled="!sal">
                Continuar con los participantes <i class="fa-solid fa-arrow-right"></i>
            </button>
            <p class="text-center text-xs text-pipocas-texto-2"><i class="fa-solid fa-shield-halved mr-1 text-pipocas-complementario"></i> Aún no se cobra nada</p>
        </x-reserva.resumen-tour>
    </aside>
</form>
@endsection
