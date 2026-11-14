@extends('layouts.reserva')

@php
    /*
     | DATOS DE EJEMPLO (solo frontend).
     | El controlador debe pasar:
     |   $reserva       -> reservas encontrada (cod, nom_ctc, cor_ctc, tel_ctc, cnt_ptc, est, pre_tot, est_pag, cre_en)
     |   $tour          -> Tour presentado (nom, cod, img, img_alt, categoria->nom)
     |   $salida        -> salidas_tour (fec_sal, hra_sal, est) + 'gui_nom' (opcional) + 'pun_enc' (punto de encuentro, opcional)
     |   $participantes -> participantes_reserva (nom_cpl, tip_doc, num_doc, es_ctc_pri) + 'boleto' => boletos (cod, est) o null
     |   $urlPago       -> ruta al pago (solo si est_pag = 'pendiente')
     | En la preview se puede probar otro estado con ?est=pendiente|confirmada|cancelada
     */
    $reserva ??= [
        'cod' => 'PIP-2610-4F7K', 'nom_ctc' => 'Ana Quispe Mamani', 'cor_ctc' => 'ana.quispe@ejemplo.com', 'tel_ctc' => '+591 71234567',
        'cnt_ptc' => 2, 'est' => in_array(request('est'), ['pendiente', 'confirmada', 'cancelada']) ? request('est') : 'confirmada',
        'pre_tot' => 310.00, 'est_pag' => request('est') === 'pendiente' ? 'pendiente' : (request('est') === 'cancelada' ? 'reembolsado' : 'pagado'),
        'cre_en' => now()->subDay(),
    ];
    $tour ??= (object) [
        'nom' => 'Valle de la Luna', 'cod' => 'TUR002', 'img' => asset('images/tours/valle-de-la-luna.jpg'),
        'img_alt' => 'Valle de la Luna', 'categoria' => (object) ['nom' => 'Hiking'],
    ];
    $salida ??= ['fec_sal' => now()->addDays(2)->toDateString(), 'hra_sal' => '08:00:00', 'est' => 'programada', 'gui_nom' => 'Rubén Choque', 'pun_enc' => 'Plaza Abaroa, Sopocachi'];
    $participantes ??= [
        ['nom_cpl' => 'Ana Quispe Mamani', 'tip_doc' => 'CI', 'num_doc' => '7894561', 'es_ctc_pri' => true,
         'boleto' => $reserva['est'] === 'confirmada' ? ['cod' => 'BOL-7F3A9C21', 'est' => 'emitido'] : null],
        ['nom_cpl' => 'Lucas Martin', 'tip_doc' => 'Pasaporte', 'num_doc' => 'XK482910', 'es_ctc_pri' => false,
         'boleto' => $reserva['est'] === 'confirmada' ? ['cod' => 'BOL-2B81D04E', 'est' => 'emitido'] : null],
    ];
    $urlPago ??= '#'; // controlador: route('pagos.create', $reserva)
    $urlConsultar ??= url('/reservas/preview/consultar');
    $urlCancelar ??= null;
    $urlResena ??= null;
    $resena ??= null;
    $urlComprobante ??= null;
    $urlBoletos ??= null;
    $historialPagos ??= collect();
    $metodos ??= [];
    $saldo ??= 0;

    // Colores y textos de los estados (reservas.est y reservas.est_pag)
    $estados = [
        'pendiente'  => ['Pendiente de pago', 'bg-pipocas-acento/30 text-pipocas-calido', 'fa-regular fa-clock'],
        'confirmada' => ['Confirmada', 'bg-emerald-100 text-emerald-800', 'fa-solid fa-circle-check'],
        'cancelada'  => ['Cancelada', 'bg-red-100 text-red-700', 'fa-solid fa-ban'],
        'completada' => ['Completada', 'bg-pipocas-principal/15 text-pipocas-principal', 'fa-solid fa-flag-checkered'],
    ];
    $pagos = [
        'pendiente'   => ['Pago pendiente', 'text-pipocas-calido'],
        'pagado'      => ['Pagado', 'text-emerald-700'],
        'parcial'     => ['Pago parcial', 'text-pipocas-calido'],
        'reembolsado' => ['Reembolsado', 'text-pipocas-texto-2'],
    ];
    [$estTxt, $estCls, $estIco] = $estados[$reserva['est']] ?? [ucfirst($reserva['est']), 'bg-gray-100 text-gray-700', 'fa-solid fa-circle-info'];
    [$pagTxt, $pagCls] = $pagos[$reserva['est_pag']] ?? [ucfirst($reserva['est_pag']), 'text-pipocas-texto'];
    $f = \Carbon\Carbon::parse($salida['fec_sal'])->locale(app()->getLocale());
@endphp

@section('titulo', __('Reserva') . ' ' . $reserva['cod'])

@section('contenido')
<div class="mx-auto max-w-4xl space-y-6">
    @if (session('estado'))
        <p class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"><i class="fa-solid fa-circle-check mr-1"></i> {{ session('estado') }}</p>
    @endif

    <a href="{{ $urlConsultar }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-pipocas-principal hover:text-pipocas-hover">
        <i class="fa-solid fa-arrow-left"></i>{{ __('Consultar otra reserva') }}</a>

    {{-- Cabecera --}}
    <section class="rsv-tarjeta">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-pipocas-texto-2">{{ __('Reserva') }}</p>
                <h1 class="break-all font-mono text-2xl font-bold tracking-wider text-pipocas-principal sm:text-3xl">{{ $reserva['cod'] }}</h1>
                <p class="mt-1 text-sm text-pipocas-texto-2">{{ __('Registrada el :fecha', ['fecha' => \Carbon\Carbon::parse($reserva['cre_en'])->locale(app()->getLocale())->translatedFormat(app()->isLocale('en') ? 'F j, Y' : 'j \d\e F \d\e Y')]) }}</p>
            </div>
            <span class="inline-flex items-center gap-2 self-start rounded-full px-4 py-2 text-sm font-bold sm:self-auto {{ $estCls }}">
                <i class="{{ $estIco }}"></i> {{ __($estTxt) }}
            </span>
        </div>

        @if ($reserva['est'] === 'pendiente' && $urlPago)
            <div class="mt-5 flex flex-col gap-3 rounded-2xl bg-pipocas-acento/15 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-pipocas-calido"><i class="fa-solid fa-circle-info mr-1"></i>
                    @if ($reserva['est_pag'] === 'parcial')
                        {{ __('Tienes un saldo de Bs. :saldo. Complétalo para recibir los tickets con código QR.', ['saldo' => number_format($saldo, 2, ',', '.')]) }}
                    @else
                        {{ __('Completa el pago para recibir los tickets con código QR.') }}
                    @endif
                </p>
                <a href="{{ $urlPago }}" class="rsv-btn shrink-0 px-5 py-2.5"><i class="fa-solid fa-credit-card"></i> {{ __('Pagar ahora') }}</a>
            </div>
        @elseif ($reserva['est'] === 'cancelada')
            <p class="mt-5 rounded-2xl bg-red-50 p-4 text-sm text-red-700"><i class="fa-solid fa-circle-info mr-1"></i> {{ __('Esta reserva fue cancelada y los cupos se liberaron.') }}</p>
        @endif
    </section>

    <div class="grid gap-6 md:grid-cols-[1fr_300px]">
        {{-- Salida --}}
        <section class="rsv-tarjeta overflow-hidden p-0 sm:p-0">
            <div class="relative h-32 bg-gradient-to-br from-pipocas-principal to-pipocas-complementario">
                @if (! empty($tour->img))<img src="{{ $tour->img }}" alt="{{ $tour->img_alt ?? $tour->nom }}" class="h-full w-full object-cover">@endif
                <div class="absolute inset-0 bg-gradient-to-t from-[#123a5a]/85 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-4 text-white">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-pipocas-acento">{{ $tour->categoria->nom ?? 'Tour' }} · {{ $tour->cod }}</span>
                    <h2 class="font-titulo text-xl font-semibold">{{ $tour->nom }}</h2>
                </div>
            </div>
            <dl class="grid gap-4 p-5 text-sm sm:grid-cols-2 sm:p-7">
                <div><dt class="text-pipocas-texto-2">{{ __('Fecha') }}</dt><dd class="font-semibold">{{ ucfirst($f->translatedFormat(app()->isLocale('en') ? 'F j, Y' : 'l j \d\e F')) }}</dd></div>
                <div><dt class="text-pipocas-texto-2">{{ __('Hora de salida') }}</dt><dd class="font-semibold">{{ substr($salida['hra_sal'], 0, 5) }}</dd></div>
                <div><dt class="text-pipocas-texto-2">{{ __('Punto de encuentro') }}</dt><dd class="font-semibold">{{ $salida['pun_enc'] ?? __('Se enviará por correo') }}</dd></div>
                <div><dt class="text-pipocas-texto-2">{{ __('Guía') }}</dt><dd class="font-semibold">{{ $salida['gui_nom'] ?? __('Por asignar') }}</dd></div>
            </dl>
        </section>

        {{-- Pago --}}
        <section class="rsv-tarjeta">
            <h2 class="rsv-titulo mb-4">{{ __('Pago') }}</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-pipocas-texto-2">{{ __('Participantes') }}</dt><dd class="font-semibold">{{ $reserva['cnt_ptc'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-pipocas-texto-2">{{ __('Estado') }}</dt><dd class="font-bold {{ $pagCls }}">{{ __($pagTxt) }}</dd></div>
                <div class="flex items-baseline justify-between border-t border-dashed border-gray-200 pt-3">
                    <dt class="font-bold">{{ __('Total') }}</dt>
                    <dd class="font-titulo text-2xl font-semibold text-pipocas-principal">Bs. {{ number_format($reserva['pre_tot'], 2, ',', '.') }}</dd>
                </div>
            </dl>
            @if ($historialPagos->isNotEmpty())
                <div class="mt-5 border-t border-gray-100 pt-4 text-sm">
                    <p class="mb-2 text-pipocas-texto-2">{{ __('Historial de pagos') }}</p>
                    <ul class="space-y-2">
                        @foreach ($historialPagos as $pago)
                            <li class="flex items-center justify-between gap-2">
                                <span>
                                    <span class="font-semibold">{{ __($metodos[$pago->mtd] ?? $pago->mtd) }}</span>
                                    <span class="block text-xs text-pipocas-texto-2">{{ $pago->pag_en->format('d/m/Y H:i') }} · {{ ucfirst(__($pago->est)) }}</span>
                                </span>
                                <span @class(['font-semibold', 'text-emerald-700' => $pago->est === 'completado', 'text-red-600 line-through' => $pago->est === 'fallido'])>Bs. {{ number_format($pago->mto, 2, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($urlComprobante)
                <a href="{{ $urlComprobante }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover"><i class="fa-solid fa-file-invoice"></i> {{ __('Descargar comprobante') }}</a>
            @endif
            <div class="mt-5 border-t border-gray-100 pt-4 text-sm">
                <p class="text-pipocas-texto-2">{{ __('Contacto') }}</p>
                <p class="font-semibold">{{ $reserva['nom_ctc'] }}</p>
                <p class="break-all text-pipocas-texto-2">{{ $reserva['cor_ctc'] }}</p>
            </div>
        </section>
    </div>

    {{-- Participantes y tickets --}}
    <section class="rsv-tarjeta">
        <div class="mb-1 flex flex-wrap items-center justify-between gap-2">
            <h2 class="rsv-titulo">{{ __('Participantes y tickets') }}</h2>
            @if ($urlBoletos)
                <a href="{{ $urlBoletos }}" class="rsv-btn-sec px-4 py-2 text-sm"><i class="fa-solid fa-download"></i> {{ __('Descargar todos (PDF)') }}</a>
            @endif
        </div>
        <p class="mb-5 text-sm text-pipocas-texto-2">{{ __('Cada participante presenta su código QR al guía antes de abordar.') }}</p>

        <ul class="grid gap-4 sm:grid-cols-2">
            @foreach ($participantes as $p)
                <li class="flex gap-4 rounded-2xl border border-gray-200 p-4">
                    {{-- Espacio para el QR (el backend generará la imagen a partir de boletos.cod) --}}
                    <div @class([
                        'grid h-24 w-24 shrink-0 place-items-center rounded-xl text-center',
                        'border-2 border-pipocas-principal/20 bg-pipocas-fondo text-pipocas-principal' => $p['boleto'],
                        'border-2 border-dashed border-gray-300 text-gray-400' => ! $p['boleto'],
                    ])>
                        @if ($p['boleto'] && ! empty($p['boleto']['qr']))
                            <img src="{{ $p['boleto']['qr'] }}" alt="{{ __('Código QR del boleto :codigo', ['codigo' => $p['boleto']['cod']]) }}" class="h-full w-full rounded-xl bg-white p-1">
                        @elseif ($p['boleto'])
                            <div>
                                <i class="fa-solid fa-qrcode text-5xl"></i>
                                <p class="mt-1 text-[9px] font-bold uppercase tracking-wide">QR</p>
                            </div>
                        @else
                            <div class="px-2 text-[11px] leading-tight">
                                <i class="fa-solid fa-lock mb-1 text-lg"></i>
                                <p>{{ $reserva['est'] === 'cancelada' ? 'Sin ticket' : (($reserva['est_pag'] ?? '') === 'pagado' && empty($p['ficha_ok']) ? 'Falta la ficha médica' : 'Disponible al pagar') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold">{{ $p['nom_cpl'] }}</p>
                        <p class="text-xs text-pipocas-texto-2">{{ $p['tip_doc'] }} {{ $p['num_doc'] }}</p>
                        @if (! empty($p['es_ctc_pri']))
                            <span class="mt-1 inline-block rounded-full bg-pipocas-secundario/15 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-pipocas-secundario">{{ __('Principal') }}</span>
                        @endif
                        @if ($p['boleto'])
                            <p class="mt-2 break-all font-mono text-xs text-pipocas-texto-2">{{ $p['boleto']['cod'] }}</p>
                            <a href="{{ $p['boleto']['url'] ?? '#' }}"
                               class="mt-2 inline-flex items-center gap-1.5 text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover">
                                <i class="fa-solid fa-download"></i>{{ __('Descargar ticket') }}</a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Reseña verificada (RF-32): solo para reservas completadas --}}
    @if ($resena)
        <section class="rsv-tarjeta">
            <h2 class="rsv-titulo mb-2">{{ __('Tu reseña') }}</h2>
            <p class="text-lg text-pipocas-acento">@for ($i = 1; $i <= 5; $i++)<i class="fa-{{ $i <= $resena->cal ? 'solid' : 'regular' }} fa-star"></i>@endfor</p>
            @if ($resena->com)<p class="mt-2 text-sm text-pipocas-texto-2">«{{ $resena->com }}»</p>@endif
        </section>
    @elseif ($urlResena)
        <section class="rsv-tarjeta" x-data="{ cal: {{ (int) old('cal', 0) }} }">
            <h2 class="rsv-titulo mb-1">{{ __('¿Qué tal estuvo el tour?') }}</h2>
            <p class="mb-4 text-sm text-pipocas-texto-2">{{ __('Tu reseña aparecerá como verificada en la página del tour.') }}</p>
            <form method="POST" action="{{ $urlResena }}" class="space-y-3">
                @csrf
                <div class="flex gap-1 text-3xl" role="radiogroup" aria-label="{{ __('Calificación') }}">
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer text-pipocas-acento">
                            <input type="radio" name="cal" value="{{ $i }}" class="sr-only" x-model.number="cal">
                            <i class="fa-star" :class="cal >= {{ $i }} ? 'fa-solid' : 'fa-regular'" aria-label="{{ $i }} estrella(s)"></i>
                        </label>
                    @endfor
                </div>
                @error('cal') <p class="rsv-error">{{ $message }}</p> @enderror
                <textarea name="com" rows="3" maxlength="1000" class="rsv-campo" placeholder="{{ __('Cuéntanos tu experiencia (opcional)') }}">{{ old('com') }}</textarea>
                <button class="rsv-btn px-5 py-2.5" :disabled="!cal"><i class="fa-solid fa-paper-plane"></i> {{ __('Publicar reseña') }}</button>
            </form>
        </section>
    @endif

    @if ($urlCancelar)
        <section class="rsv-tarjeta" x-data="{ abierto: false }">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="rsv-titulo">{{ __('¿No podrás asistir?') }}</h2>
                    <p class="text-sm text-pipocas-texto-2">{{ __('Puedes cancelar hasta el día anterior a la salida. Lo pagado se registra como reembolso.') }}</p>
                </div>
                <button type="button" class="rsv-btn-sec shrink-0 text-red-600" @click="abierto = true"><i class="fa-solid fa-ban"></i> {{ __('Cancelar reserva') }}</button>
            </div>
            <form method="POST" action="{{ $urlCancelar }}" x-show="abierto" x-cloak class="mt-4 rounded-2xl bg-red-50 p-4 text-sm">
                @csrf
                <p class="font-semibold text-red-700">{{ __('¿Seguro? Se liberarán los cupos y se anularán los tickets.') }}</p>
                <div class="mt-3 flex gap-3">
                    <button type="submit" class="rsv-btn bg-red-600 px-4 py-2 hover:bg-red-700">{{ __('Sí, cancelar') }}</button>
                    <button type="button" class="rsv-btn-sec px-4 py-2" @click="abierto = false">{{ __('No') }}</button>
                </div>
            </form>
        </section>
    @endif
</div>
@endsection
