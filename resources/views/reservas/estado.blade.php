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
        'reembolsado' => ['Reembolsado', 'text-pipocas-texto-2'],
    ];
    [$estTxt, $estCls, $estIco] = $estados[$reserva['est']] ?? [ucfirst($reserva['est']), 'bg-gray-100 text-gray-700', 'fa-solid fa-circle-info'];
    [$pagTxt, $pagCls] = $pagos[$reserva['est_pag']] ?? [ucfirst($reserva['est_pag']), 'text-pipocas-texto'];
    $f = \Carbon\Carbon::parse($salida['fec_sal'])->locale('es');
@endphp

@section('titulo', 'Reserva ' . $reserva['cod'])

@section('hero')
    <x-reserva.hero :tour="$tour" etiqueta="Reserva" :titulo="$reserva['cod']"
                    subtitulo="Registrada el {{ \Carbon\Carbon::parse($reserva['cre_en'])->locale('es')->translatedFormat('j \d\e F \d\e Y') }} a nombre de {{ $reserva['nom_ctc'] }}."
                    :volver="['url' => url('/reservas/preview/consultar'), 'texto' => 'Consultar otra reserva']">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-bold shadow-lg {{ $estCls }}">
            <i class="{{ $estIco }}"></i> {{ $estTxt }}
        </span>
    </x-reserva.hero>
@endsection

@section('contenido')
<div class="relative z-10 -mt-14 space-y-6">
    {{-- Línea de tiempo del estado --}}
    <section class="rsv-tarjeta">
        @php
            $hitos = [
                ['fa-solid fa-calendar-check', 'Reservada', true],
                ['fa-solid fa-credit-card', 'Pagada', $reserva['est_pag'] === 'pagado'],
                ['fa-solid fa-qrcode', 'Tickets emitidos', collect($participantes)->contains(fn ($p) => ! empty($p['boleto']))],
                ['fa-solid fa-flag-checkered', 'Tour realizado', $reserva['est'] === 'completada'],
            ];
            $cancelada = $reserva['est'] === 'cancelada';
        @endphp
        <ol class="grid grid-cols-2 gap-y-6 sm:grid-cols-4">
            @foreach ($hitos as $i => [$ico, $txt, $ok])
                <li class="relative flex flex-col items-center text-center">
                    @if ($i > 0)
                        <span @class(['absolute right-1/2 top-5 hidden h-1 w-full -translate-y-1/2 sm:block', 'bg-pipocas-principal' => $ok && ! $cancelada, 'bg-gray-200' => ! $ok || $cancelada])></span>
                    @endif
                    <span @class([
                        'relative grid h-10 w-10 place-items-center rounded-full ring-4 ring-white',
                        'bg-red-100 text-red-600' => $cancelada && $i > 0,
                        'bg-pipocas-principal text-white' => $ok && ! ($cancelada && $i > 0),
                        'border-2 border-gray-200 bg-white text-gray-400' => ! $ok && ! $cancelada,
                    ])><i class="{{ $cancelada && $i > 0 ? 'fa-solid fa-xmark' : $ico }}"></i></span>
                    <span @class(['mt-2 text-sm', 'font-bold text-pipocas-texto' => $ok, 'text-pipocas-texto-2' => ! $ok])>{{ $txt }}</span>
                </li>
            @endforeach
        </ol>

        @if ($reserva['est'] === 'pendiente')
            <div class="mt-6 flex flex-col gap-3 rounded-2xl bg-pipocas-acento/20 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-pipocas-calido"><i class="fa-solid fa-circle-info mr-1"></i> Completa el pago para recibir los tickets con código QR.</p>
                <a href="{{ $urlPago }}" class="rsv-btn shrink-0 px-5 py-2.5"><i class="fa-solid fa-credit-card"></i> Pagar ahora</a>
            </div>
        @elseif ($cancelada)
            <p class="mt-6 rounded-2xl bg-red-50 p-4 text-sm text-red-700"><i class="fa-solid fa-circle-info mr-1"></i> Esta reserva fue cancelada y los cupos se liberaron.</p>
        @elseif ($reserva['est'] === 'confirmada')
            <p class="mt-6 rounded-2xl bg-emerald-50 p-4 text-sm text-emerald-800"><i class="fa-solid fa-circle-check mr-1"></i> ¡Todo listo! Presenta el QR de cada participante al guía antes de abordar.</p>
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
                <div><dt class="text-pipocas-texto-2">Fecha</dt><dd class="font-semibold">{{ ucfirst($f->translatedFormat('l j \d\e F')) }}</dd></div>
                <div><dt class="text-pipocas-texto-2">Hora de salida</dt><dd class="font-semibold">{{ substr($salida['hra_sal'], 0, 5) }}</dd></div>
                <div><dt class="text-pipocas-texto-2">Punto de encuentro</dt><dd class="font-semibold">{{ $salida['pun_enc'] ?? 'Se enviará por correo' }}</dd></div>
                <div><dt class="text-pipocas-texto-2">Guía</dt><dd class="font-semibold">{{ $salida['gui_nom'] ?? 'Por asignar' }}</dd></div>
            </dl>
        </section>

        {{-- Pago --}}
        <section class="rsv-tarjeta">
            <h2 class="rsv-titulo mb-4">Pago</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-pipocas-texto-2">Participantes</dt><dd class="font-semibold">{{ $reserva['cnt_ptc'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-pipocas-texto-2">Estado</dt><dd class="font-bold {{ $pagCls }}">{{ $pagTxt }}</dd></div>
                <div class="flex items-baseline justify-between border-t border-dashed border-gray-200 pt-3">
                    <dt class="font-bold">Total</dt>
                    <dd class="font-titulo text-2xl font-semibold text-pipocas-principal">Bs. {{ number_format($reserva['pre_tot'], 2, ',', '.') }}</dd>
                </div>
            </dl>
            <div class="mt-5 border-t border-gray-100 pt-4 text-sm">
                <p class="text-pipocas-texto-2">Contacto</p>
                <p class="font-semibold">{{ $reserva['nom_ctc'] }}</p>
                <p class="break-all text-pipocas-texto-2">{{ $reserva['cor_ctc'] }}</p>
            </div>
        </section>
    </div>

    {{-- Participantes y tickets --}}
    <section class="rsv-tarjeta">
        <h2 class="rsv-titulo mb-1">Participantes y tickets</h2>
        <p class="mb-5 text-sm text-pipocas-texto-2">Cada participante presenta su código QR al guía antes de abordar.</p>

        <ul class="grid gap-4 sm:grid-cols-2">
            @foreach ($participantes as $p)
                <li class="flex gap-4 rounded-2xl border border-gray-200 p-4">
                    {{-- Espacio para el QR (el backend generará la imagen a partir de boletos.cod) --}}
                    <div @class([
                        'grid h-24 w-24 shrink-0 place-items-center rounded-xl text-center',
                        'border-2 border-pipocas-principal/20 bg-pipocas-fondo text-pipocas-principal' => $p['boleto'],
                        'border-2 border-dashed border-gray-300 text-gray-400' => ! $p['boleto'],
                    ])>
                        @if ($p['boleto'])
                            <div>
                                <i class="fa-solid fa-qrcode text-5xl"></i>
                                <p class="mt-1 text-[9px] font-bold uppercase tracking-wide">QR</p>
                            </div>
                        @else
                            <div class="px-2 text-[11px] leading-tight">
                                <i class="fa-solid fa-lock mb-1 text-lg"></i>
                                <p>{{ $reserva['est'] === 'cancelada' ? 'Sin ticket' : 'Disponible al pagar' }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold">{{ $p['nom_cpl'] }}</p>
                        <p class="text-xs text-pipocas-texto-2">{{ $p['tip_doc'] }} {{ $p['num_doc'] }}</p>
                        @if (! empty($p['es_ctc_pri']))
                            <span class="mt-1 inline-block rounded-full bg-pipocas-secundario/15 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-pipocas-secundario">Principal</span>
                        @endif
                        @if ($p['boleto'])
                            <p class="mt-2 break-all font-mono text-xs text-pipocas-texto-2">{{ $p['boleto']['cod'] }}</p>
                            <a href="#" {{-- controlador: route('boletos.descargar', $p['boleto']['cod']) --}}
                               class="mt-2 inline-flex items-center gap-1.5 text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover">
                                <i class="fa-solid fa-download"></i> Descargar ticket
                            </a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
</div>
@endsection
