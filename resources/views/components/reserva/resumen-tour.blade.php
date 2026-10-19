{{--
    Tarjeta lateral con el resumen del tour (y de la salida elegida, si existe).
    Uso: <x-reserva.resumen-tour :tour="$tour" :salida="$salida" :cnt="$cnt_ptc" :extra="$subtotalEquipo" />
    - $tour: modelo Tour "presentado" por el controlador (nom, cod, img, img_alt, dur_txt, dif_txt, pre_bas, categoria->nom)
    - $salida: fila de salidas_tour (fec_sal, hra_sal) o null
    - $cnt: cantidad de participantes; $extra: monto adicional (equipo) en Bs.
    - $cntExpr / $extraExpr (opcionales): expresiones Alpine con la cantidad y el monto de equipo,
      para que los totales se actualicen en vivo (p. ej. cnt-expr="cnt", extra-expr="total()").
    El slot permite agregar contenido al final (p. ej. el botón de continuar en escritorio).
--}}
@props(['tour', 'salida' => null, 'cnt' => 1, 'extra' => 0, 'cntExpr' => null, 'extraExpr' => null])

@php
    $fecha = $salida ? \Carbon\Carbon::parse($salida['fec_sal'])->locale('es') : null;
    $subtotalTour = (float) $tour->pre_bas * (int) $cnt;
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-3xl bg-white shadow-tarjeta']) }}>
    <div class="relative h-36 bg-gradient-to-br from-pipocas-principal to-pipocas-complementario sm:h-40">
        @if (! empty($tour->img))
            <img src="{{ $tour->img }}" alt="{{ $tour->img_alt ?? $tour->nom }}" class="h-full w-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-[#123a5a]/85 via-[#123a5a]/20 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 p-4 text-white">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-pipocas-acento">{{ $tour->categoria->nom ?? 'Tour' }} · {{ $tour->cod }}</span>
            <h3 class="font-titulo text-xl font-semibold leading-tight">{{ $tour->nom }}</h3>
        </div>
    </div>

    <div class="space-y-4 p-5">
        <ul class="grid grid-cols-2 gap-3 text-sm">
            <li class="flex items-center gap-2"><i class="fa-regular fa-clock w-4 text-pipocas-secundario"></i> {{ $tour->dur_txt ?? $tour->dur_hrs }} h</li>
            <li class="flex items-center gap-2"><i class="fa-solid fa-signal w-4 text-pipocas-secundario"></i> {{ $tour->dif_txt ?? $tour->niv_dif }}</li>
            @if ($fecha)
                <li class="col-span-2 flex items-center gap-2 font-semibold text-pipocas-principal">
                    <i class="fa-regular fa-calendar w-4"></i>
                    {{ ucfirst($fecha->translatedFormat('l j \d\e F')) }} · {{ substr($salida['hra_sal'], 0, 5) }}
                </li>
            @endif
        </ul>

        <dl class="space-y-2 border-t border-dashed border-gray-200 pt-4 text-sm">
            <div class="flex justify-between">
                <dt class="text-pipocas-texto-2">Bs. {{ number_format($tour->pre_bas, 2, ',', '.') }} ×
                    @if ($cntExpr)
                        <span x-text="{{ $cntExpr }} + ({{ $cntExpr }} == 1 ? ' persona' : ' personas')">{{ $cnt }}</span>
                    @else
                        {{ $cnt }} {{ $cnt == 1 ? 'persona' : 'personas' }}
                    @endif
                </dt>
                <dd class="font-semibold" @if ($cntExpr) x-text="'Bs. ' + ({{ (float) $tour->pre_bas }} * {{ $cntExpr }}).toLocaleString('es-BO', {minimumFractionDigits: 2})" @endif>Bs. {{ number_format($subtotalTour, 2, ',', '.') }}</dd>
            </div>
            @if ($extraExpr)
                <div class="flex justify-between" x-show="{{ $extraExpr }} > 0" x-cloak>
                    <dt class="text-pipocas-texto-2">Alquiler de equipo</dt>
                    <dd class="font-semibold" x-text="'Bs. ' + ({{ $extraExpr }}).toLocaleString('es-BO', {minimumFractionDigits: 2})"></dd>
                </div>
            @elseif ($extra > 0)
                <div class="flex justify-between">
                    <dt class="text-pipocas-texto-2">Alquiler de equipo</dt>
                    <dd class="font-semibold">Bs. {{ number_format($extra, 2, ',', '.') }}</dd>
                </div>
            @endif
            <div class="flex items-baseline justify-between border-t border-gray-100 pt-2">
                <dt class="font-bold">Total</dt>
                <dd class="font-titulo text-2xl font-semibold text-pipocas-principal" @if ($cntExpr || $extraExpr) x-text="'Bs. ' + ({{ (float) $tour->pre_bas }} * ({{ $cntExpr ?: (int) $cnt }}) + ({{ $extraExpr ?: (float) $extra }})).toLocaleString('es-BO', {minimumFractionDigits: 2})" @endif>Bs. {{ number_format($subtotalTour + $extra, 2, ',', '.') }}</dd>
            </div>
        </dl>

        {{ $slot }}
    </div>
</div>
