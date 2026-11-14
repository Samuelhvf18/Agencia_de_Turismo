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

    $f = \Carbon\Carbon::parse($salida['fec_sal'])->locale(app()->getLocale());
    $subtotalTour = (float) $tour->pre_bas * count($participantes);
    $subtotalEquipo = (float) collect($participantes)->flatMap(fn ($p) => $p['alquileres'] ?? [])->sum('pre');
    $total = $subtotalTour + $subtotalEquipo; // => reservas.pre_tot
    $bs = fn ($n) => 'Bs. ' . number_format($n, 2, ',', '.');
    $editar ??= [
        'salida' => url('/reservas/preview/salida'),
        'participantes' => url('/reservas/preview/participantes'),
        'equipo' => url('/reservas/preview/equipo'),
        'ficha' => url('/reservas/preview/ficha'),
    ];
@endphp

@section('titulo', __('Revisa tu reserva'))

@section('stepper')
    <x-reserva.stepper :actual="5" :enlaces="array_values($editar)" />
@endsection

@section('contenido')
<form method="POST" action="{{ $accionForm }}" class="grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start lg:gap-8">
    @csrf

    <div class="space-y-6">
        <div>
            <h1 class="font-titulo text-3xl font-semibold leading-tight sm:text-4xl">{{ __('Revisa tu reserva') }}</h1>
            <p class="mt-2 text-pipocas-texto-2">{{ __('Verifica que todo esté correcto antes de confirmar. Luego podrás pagar en línea.') }}</p>
        </div>

        {{-- Salida --}}
        <section class="rsv-tarjeta">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="rsv-titulo">{{ __('Salida') }}</h2>
                <a href="{{ $editar['salida'] }}" class="text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover"><i class="fa-regular fa-pen-to-square"></i> {{ __('Cambiar') }}</a>
            </div>
            <dl class="grid gap-4 text-sm sm:grid-cols-3">
                <div>
                    <dt class="text-pipocas-texto-2">Tour</dt>
                    <dd class="font-semibold">{{ $tour->nom }} <span class="font-normal text-pipocas-texto-2">({{ $tour->cod }})</span></dd>
                </div>
                <div>
                    <dt class="text-pipocas-texto-2">{{ __('Fecha y hora') }}</dt>
                    <dd class="font-semibold">{{ ucfirst($f->translatedFormat('D j M Y')) }} · {{ substr($salida['hra_sal'], 0, 5) }}</dd>
                </div>
                <div>
                    <dt class="text-pipocas-texto-2">{{ __('Guía') }}</dt>
                    <dd class="font-semibold">{{ $salida['gui_nom'] ?? __('Por asignar') }}</dd>
                </div>
            </dl>
        </section>

        {{-- Contacto --}}
        <section class="rsv-tarjeta">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="rsv-titulo">{{ __('Contacto') }}</h2>
                <a href="{{ $editar['participantes'] }}" class="text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover"><i class="fa-regular fa-pen-to-square"></i> {{ __('Editar') }}</a>
            </div>
            <dl class="grid gap-4 text-sm sm:grid-cols-3">
                <div><dt class="text-pipocas-texto-2">{{ __('Nombre') }}</dt><dd class="font-semibold">{{ $reserva['nom_ctc'] }}</dd></div>
                <div class="min-w-0"><dt class="text-pipocas-texto-2">{{ __('Correo') }}</dt><dd class="break-words font-semibold">{{ $reserva['cor_ctc'] }}</dd></div>
                <div><dt class="text-pipocas-texto-2">{{ __('Teléfono') }}</dt><dd class="font-semibold">{{ $reserva['tel_ctc'] }}</dd></div>
            </dl>
        </section>

        {{-- Participantes y equipo --}}
        <section class="rsv-tarjeta">
            <div class="mb-2 flex items-center justify-between">
                <h2 class="rsv-titulo">{{ __('Participantes') }} <span class="text-base text-pipocas-texto-2">({{ count($participantes) }})</span></h2>
                <a href="{{ $editar['equipo'] }}" class="text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover"><i class="fa-regular fa-pen-to-square"></i> {{ __('Equipo') }}</a>
            </div>
            <ul class="divide-y divide-gray-100">
                @foreach ($participantes as $n => $p)
                    @php $sub = $tour->pre_bas + collect($p['alquileres'] ?? [])->sum('pre'); @endphp
                    <li class="py-4">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="flex items-start gap-3">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-pipocas-principal text-sm font-bold text-white">{{ $n + 1 }}</span>
                                <div>
                                    <p class="font-semibold">
                                        {{ $p['nom_cpl'] }}
                                        @if (! empty($p['es_ctc_pri']))
                                            <span class="ml-1 rounded-full bg-pipocas-secundario/15 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-pipocas-secundario">{{ __('Principal') }}</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-pipocas-texto-2">
                                        {{ $p['tip_doc'] }} {{ $p['num_doc'] }}{{ $p['ci_cmp'] ? '-' . $p['ci_cmp'] : '' }} · {{ $p['eda'] }} años · {{ $p['pais'] }}
                                    </p>
                                </div>
                            </div>
                            <span class="font-semibold">{{ $bs($sub) }}</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2 pl-11">
                            @if (! empty($p['ficha_ok']))
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700"><i class="fa-solid fa-file-signature"></i> {{ __('Ficha firmada') }}</span>
                            @else
                                <a href="{{ $editar['ficha'] }}" class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700"><i class="fa-solid fa-triangle-exclamation"></i> {{ __('Falta firmar la ficha') }}</a>
                            @endif
                            @forelse ($p['alquileres'] ?? [] as $a)
                                <span class="inline-flex items-center gap-1 rounded-full bg-pipocas-fondo px-2.5 py-1 text-xs text-pipocas-texto">
                                    <i class="fa-solid fa-toolbox text-pipocas-secundario"></i> {{ $a['nom'] }} ({{ $a['tal'] }}) · {{ $bs($a['pre']) }}
                                </span>
                            @empty
                                <span class="rounded-full bg-pipocas-fondo px-2.5 py-1 text-xs text-pipocas-texto-2">{{ __('Sin equipo alquilado') }}</span>
                            @endforelse
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    {{-- Totales y confirmación --}}
    <aside class="lg:sticky lg:top-6">
        <div class="rsv-tarjeta space-y-5">
            <h2 class="rsv-titulo">{{ __('Total a pagar') }}</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-pipocas-texto-2">Tour · {{ $bs($tour->pre_bas) }} × {{ count($participantes) }}</dt>
                    <dd class="font-semibold">{{ $bs($subtotalTour) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-pipocas-texto-2">{{ __('Alquiler de equipo') }}</dt>
                    <dd class="font-semibold">{{ $bs($subtotalEquipo) }}</dd>
                </div>
                <div class="flex items-baseline justify-between border-t border-dashed border-gray-200 pt-3">
                    <dt class="font-bold">{{ __('Total') }}</dt>
                    <dd class="font-titulo text-3xl font-semibold text-pipocas-principal">{{ $bs($total) }}</dd>
                </div>
            </dl>

            <div>
                <label class="flex cursor-pointer items-start gap-3 text-sm">
                    <input type="checkbox" name="acp_ter" value="1" required class="rsv-check mt-0.5" @checked(old('acp_ter'))>
                    <span>{{ __('Acepto los') }} <a href="{{ Route::has('terms.show') ? route('terms.show') : '#' }}" target="_blank" class="font-semibold text-pipocas-principal underline underline-offset-2 hover:text-pipocas-hover">{{ __('términos y condiciones') }}</a> {{ __('y la política de cancelación de Pipocas.') }}</span>
                </label>
                @error('acp_ter') <p class="rsv-error pl-8">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="rsv-btn w-full text-base">
                <i class="fa-solid fa-lock"></i>{{ __('Confirmar reserva') }}</button>
            <p class="text-center text-xs text-pipocas-texto-2">{{ __('Tu cupo queda reservado como') }} <b>{{ __('pendiente de pago') }}</b>.</p>
        </div>
    </aside>
</form>
@endsection
