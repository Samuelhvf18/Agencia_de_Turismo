@extends('layouts.reserva')

@php
    /*
     | DATOS DE EJEMPLO (solo frontend).
     | El controlador debe pasar:
     |   $tour          -> Tour presentado (nom, cod, img, dur_txt, dif_txt, pre_bas, categoria->nom)
     |   $salida        -> salidas_tour elegida (id, fec_sal, hra_sal) + 'gui_nom' (nombre del guía, opcional)
     |   $reserva       -> datos de contacto (nom_ctc, cor_ctc, tel_ctc, cnt_ptc)
     |   $participantes -> participantes_reserva (id, nom_cpl, tip_doc, num_doc, ci_cmp, eda, pais (nom), es_ctc_pri,
     |                     ficha_ok (bool: tiene fichas_medicas firmada),
     |                     alquileres: [ ['nom' => tipos_equipo.nom, 'tal' => items_equipo.tal, 'pre' => alquileres_equipo_reserva.pre] ])
     |   $accionForm    -> route('reservas.confirmar')  (POST; crea la reserva con pre_tot y est = 'pendiente')
     | Se envía: acp_ter (casilla de términos, validar 'accepted').
     */
    $tour ??= (object) [
        'nom' => 'Valle de la Luna', 'cod' => 'TUR002', 'img' => asset('images/tours/valle-de-la-luna.jpg'),
        'img_alt' => 'Valle de la Luna', 'dur_txt' => '5', 'dif_txt' => 'Moderado', 'pre_bas' => 130.00,
        'categoria' => (object) ['nom' => 'Hiking'],
    ];
    $salida ??= ['id' => 11, 'fec_sal' => now()->addDays(2)->toDateString(), 'hra_sal' => '08:00:00', 'gui_nom' => 'Rubén Choque'];
    $reserva ??= ['nom_ctc' => 'Ana Quispe Mamani', 'cor_ctc' => 'ana.quispe@ejemplo.com', 'tel_ctc' => '+591 71234567', 'cnt_ptc' => 2];
    $participantes ??= [
        ['id' => 101, 'nom_cpl' => 'Ana Quispe Mamani', 'tip_doc' => 'CI', 'num_doc' => '7894561', 'ci_cmp' => '', 'eda' => 29, 'pais' => 'Bolivia', 'es_ctc_pri' => true, 'ficha_ok' => true,
         'alquileres' => [['nom' => 'Casco de seguridad', 'tal' => 'M', 'pre' => 15.00], ['nom' => 'Bastón de trekking', 'tal' => 'Regulable', 'pre' => 10.00]]],
        ['id' => 102, 'nom_cpl' => 'Lucas Martin', 'tip_doc' => 'Pasaporte', 'num_doc' => 'XK482910', 'ci_cmp' => '', 'eda' => 34, 'pais' => 'Francia', 'es_ctc_pri' => false, 'ficha_ok' => true,
         'alquileres' => [['nom' => 'Chaqueta impermeable', 'tal' => 'L', 'pre' => 25.00]]],
    ];
    $accionForm ??= url('/reservas/preview/confirmada'); // controlador: route('reservas.confirmar')

    $f = \Carbon\Carbon::parse($salida['fec_sal'])->locale('es');
    $subtotalTour = (float) $tour->pre_bas * count($participantes);
    $subtotalEquipo = (float) collect($participantes)->flatMap(fn ($p) => $p['alquileres'] ?? [])->sum('pre');
    $total = $subtotalTour + $subtotalEquipo; // => reservas.pre_tot
    $bs = fn ($n) => 'Bs. ' . number_format($n, 2, ',', '.');
    $editar = [
        'salida' => url('/reservas/preview/salida'),
        'participantes' => url('/reservas/preview/participantes'),
        'equipo' => url('/reservas/preview/equipo'),
        'ficha' => url('/reservas/preview/ficha'),
    ];
@endphp

@section('titulo', 'Revisa tu reserva')

@section('hero')
    <x-reserva.hero :tour="$tour" etiqueta="Paso 5" titulo="Revisa tu reserva"
                    subtitulo="Verifica que todo esté correcto antes de confirmar. Después podrás pagar en línea con tarjeta o QR." />
@endsection

@section('stepper')
    <x-reserva.stepper :actual="5" :enlaces="array_values($editar)" />
@endsection

@section('contenido')
<form method="POST" action="{{ $accionForm }}" class="grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start lg:gap-8">
    @csrf

    <div class="min-w-0 space-y-6">
        {{-- Ticket de la salida --}}
        <section class="rounded-3xl bg-white shadow-tarjeta">
            <div class="relative h-28 overflow-hidden rounded-t-3xl bg-gradient-to-br from-pipocas-principal to-pipocas-complementario sm:h-32">
                @if (! empty($tour->img))<img src="{{ $tour->img }}" alt="{{ $tour->img_alt ?? $tour->nom }}" class="h-full w-full object-cover">@endif
                <div class="absolute inset-0 bg-gradient-to-r from-[#0f2f4a]/90 to-transparent"></div>
                <div class="absolute inset-y-0 left-0 flex flex-col justify-center px-5 text-white sm:px-7">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-pipocas-acento">{{ $tour->categoria->nom ?? 'Tour' }} · {{ $tour->cod }}</span>
                    <h2 class="font-titulo text-2xl font-semibold">{{ $tour->nom }}</h2>
                </div>
                <a href="{{ $editar['salida'] }}" class="absolute right-4 top-4 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur hover:bg-white/25"><i class="fa-regular fa-pen-to-square"></i> Cambiar</a>
            </div>

            <dl class="grid grid-cols-2 gap-4 p-5 text-sm sm:p-7 xl:grid-cols-4">
                @foreach ([
                    ['fa-regular fa-calendar', 'Fecha', ucfirst($f->translatedFormat('D j M Y'))],
                    ['fa-regular fa-clock', 'Hora', substr($salida['hra_sal'], 0, 5)],
                    ['fa-solid fa-user-tie', 'Guía', $salida['gui_nom'] ?? 'Por asignar'],
                    ['fa-solid fa-users', 'Personas', count($participantes)],
                ] as [$ico, $dt, $dd])
                    <div class="flex items-start gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-pipocas-secundario/10 text-pipocas-secundario"><i class="{{ $ico }}"></i></span>
                        <div class="min-w-0"><dt class="text-xs text-pipocas-texto-2">{{ $dt }}</dt><dd class="break-words font-bold">{{ $dd }}</dd></div>
                    </div>
                @endforeach
            </dl>

            <div class="rsv-ticket-corte"></div>

            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-7">
                <div class="flex items-center gap-3">
                    <span class="rsv-icono"><i class="fa-regular fa-address-card"></i></span>
                    <div class="min-w-0 text-sm">
                        <p class="text-xs text-pipocas-texto-2">Contacto de la reserva</p>
                        <p class="font-bold">{{ $reserva['nom_ctc'] }}</p>
                        <p class="break-all text-pipocas-texto-2">{{ $reserva['cor_ctc'] }} · {{ $reserva['tel_ctc'] }}</p>
                    </div>
                </div>
                <a href="{{ $editar['participantes'] }}" class="text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover"><i class="fa-regular fa-pen-to-square"></i> Editar</a>
            </div>
        </section>

        {{-- Participantes y equipo --}}
        <section class="rsv-tarjeta">
            <header class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <span class="rsv-icono"><i class="fa-solid fa-users"></i></span>
                    <h2 class="rsv-titulo">Participantes <span class="text-base font-normal text-pipocas-texto-2">({{ count($participantes) }})</span></h2>
                </div>
                <a href="{{ $editar['equipo'] }}" class="text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover"><i class="fa-regular fa-pen-to-square"></i> Equipo</a>
            </header>
            <ul class="space-y-3">
                @foreach ($participantes as $n => $p)
                    @php $sub = $tour->pre_bas + collect($p['alquileres'] ?? [])->sum('pre'); @endphp
                    <li class="rounded-2xl border border-gray-100 bg-pipocas-fondo/50 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span @class([
                                    'grid h-11 w-11 shrink-0 place-items-center rounded-2xl text-sm font-bold text-white',
                                    'bg-gradient-to-br from-pipocas-secundario to-pipocas-calido' => ! empty($p['es_ctc_pri']),
                                    'bg-gradient-to-br from-pipocas-complementario to-pipocas-principal' => empty($p['es_ctc_pri']),
                                ])>{{ collect(explode(' ', $p['nom_cpl']))->take(2)->map(fn ($x) => mb_substr($x, 0, 1))->join('') }}</span>
                                <div class="min-w-0">
                                    <p class="flex flex-wrap items-center gap-2 font-bold">
                                        {{ $p['nom_cpl'] }}
                                        @if (! empty($p['es_ctc_pri']))
                                            <span class="rsv-chip bg-pipocas-secundario/15 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-pipocas-secundario"><i class="fa-solid fa-star"></i> Principal</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-pipocas-texto-2">
                                        {{ $p['tip_doc'] }} {{ $p['num_doc'] }}{{ $p['ci_cmp'] ? '-' . $p['ci_cmp'] : '' }} · {{ $p['eda'] }} años · {{ $p['pais'] }}
                                    </p>
                                </div>
                            </div>
                            <span class="font-titulo text-lg font-semibold text-pipocas-principal">{{ $bs($sub) }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if (! empty($p['ficha_ok']))
                                <span class="rsv-chip bg-emerald-50 text-emerald-700"><i class="fa-solid fa-file-signature"></i> Ficha firmada</span>
                            @else
                                <a href="{{ $editar['ficha'] }}" class="rsv-chip bg-red-50 text-red-700 hover:bg-red-100"><i class="fa-solid fa-triangle-exclamation"></i> Falta firmar la ficha</a>
                            @endif
                            @forelse ($p['alquileres'] ?? [] as $a)
                                <span class="rsv-chip bg-white text-pipocas-texto shadow-sm">
                                    <i class="fa-solid fa-toolbox text-pipocas-secundario"></i> {{ $a['nom'] }} ({{ $a['tal'] }}) · {{ $bs($a['pre']) }}
                                </span>
                            @empty
                                <span class="rsv-chip bg-white text-pipocas-texto-2">Sin equipo alquilado</span>
                            @endforelse
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    {{-- Totales y confirmación --}}
    <aside class="lg:sticky lg:top-24">
        <div class="overflow-hidden rounded-3xl bg-pipocas-principal text-white shadow-tarjeta">
            <div class="rsv-topo space-y-5 p-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-pipocas-acento">Total a pagar</p>
                    <p class="mt-1 font-titulo text-4xl font-semibold">{{ $bs($total) }}</p>
                </div>
                <dl class="space-y-2 border-t border-white/15 pt-4 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-white/75">Tour · {{ $bs($tour->pre_bas) }} × {{ count($participantes) }}</dt>
                        <dd class="font-semibold">{{ $bs($subtotalTour) }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-white/75">Alquiler de equipo</dt>
                        <dd class="font-semibold">{{ $bs($subtotalEquipo) }}</dd>
                    </div>
                </dl>

                <div class="rounded-2xl bg-white/10 p-4">
                    <label class="flex cursor-pointer items-start gap-3 text-sm">
                        <input type="checkbox" name="acp_ter" value="1" required class="mt-0.5 h-5 w-5 rounded border-white/40 bg-white/10 text-pipocas-acento focus:ring-pipocas-acento" @checked(old('acp_ter'))>
                        <span>Acepto los <a href="{{ Route::has('terms.show') ? route('terms.show') : '#' }}" target="_blank" class="font-semibold text-pipocas-acento underline underline-offset-2">términos y condiciones</a> y la política de cancelación de Pipocas.</span>
                    </label>
                    @error('acp_ter') <p class="mt-1 pl-8 text-xs font-medium text-red-200">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-pipocas-acento px-6 py-4 text-base font-bold text-pipocas-calido shadow-lg shadow-black/10 transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                    <i class="fa-solid fa-lock"></i> Confirmar reserva
                </button>
                <p class="text-center text-xs text-white/70">Tu cupo queda reservado como <b class="text-white">pendiente de pago</b> por 24 horas.</p>
            </div>
        </div>
        <x-reserva.incluye class="mt-4" />
    </aside>
</form>
@endsection
