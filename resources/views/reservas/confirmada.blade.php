@extends('layouts.reserva')

@php
    /*
     | DATOS DE EJEMPLO (solo frontend).
     | El controlador debe pasar:
     |   $reserva -> reservas recién creada (cod, nom_ctc, cor_ctc, cnt_ptc, pre_tot, est, est_pag, cre_en)
     |   $tour    -> Tour (nom)
     |   $salida  -> salidas_tour (fec_sal, hra_sal)
     |   $urlPago -> ruta de la pasarela de pago (placeholder, p. ej. route('pagos.create', $reserva))
     */
    $reserva ??= [
        'cod' => 'PIP-2610-4F7K', 'nom_ctc' => 'Ana Quispe Mamani', 'cor_ctc' => 'ana.quispe@ejemplo.com',
        'cnt_ptc' => 2, 'pre_tot' => 310.00, 'est' => 'pendiente', 'est_pag' => 'pendiente', 'cre_en' => now(),
    ];
    $tour ??= (object) ['nom' => 'Valle de la Luna', 'img' => asset('images/tours/valle-de-la-luna.jpg')];
    $salida ??= ['fec_sal' => now()->addDays(2)->toDateString(), 'hra_sal' => '08:00:00'];
    $urlPago ??= '#'; // controlador: route('pagos.create', $reserva)

    $f = \Carbon\Carbon::parse($salida['fec_sal'])->locale('es');
    $limitePago = \Carbon\Carbon::parse($reserva['cre_en'])->addHours(24)->locale('es');
@endphp

@section('titulo', 'Reserva registrada')

@section('hero')
    <x-reserva.hero :img="$tour->img ?? null" etiqueta="¡Todo listo!" titulo="¡Reserva registrada!"
                    subtitulo="Gracias, {{ \Illuminate\Support\Str::before($reserva['nom_ctc'] . ' ', ' ') }}. Guardamos tus cupos para {{ $tour->nom }}. Solo falta el pago para emitir tus tickets." />
@endsection

@section('stepper')
    <x-reserva.stepper :actual="6" />
@endsection

@section('contenido')
<div class="grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start lg:gap-8">
    {{-- Ticket de la reserva --}}
    <section class="rounded-3xl bg-white shadow-tarjeta" x-data="{ copiado: false }">
        <div class="flex flex-col items-center gap-6 p-6 text-center sm:flex-row sm:p-8 sm:text-left">
            <span class="grid h-20 w-20 shrink-0 place-items-center rounded-full bg-emerald-50 ring-8 ring-emerald-50/60">
                <i class="fa-solid fa-circle-check text-5xl text-emerald-500"></i>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-pipocas-texto-2">Código de reserva</p>
                <p class="mt-1 break-all font-mono text-3xl font-bold tracking-wider text-pipocas-principal sm:text-4xl">{{ $reserva['cod'] }}</p>
                <div class="mt-3 flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                    <span class="rsv-chip bg-pipocas-acento/30 font-bold text-pipocas-calido">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-pipocas-secundario"></span> Pendiente de pago
                    </span>
                    <button type="button" class="rsv-chip border border-gray-200 bg-white text-pipocas-complementario transition hover:border-pipocas-principal"
                            @click="navigator.clipboard?.writeText(@js($reserva['cod'])); copiado = true; setTimeout(() => copiado = false, 2000)">
                        <i class="fa-regular" :class="copiado ? 'fa-circle-check' : 'fa-copy'"></i>
                        <span x-text="copiado ? '¡Copiado!' : 'Copiar código'">Copiar código</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="rsv-ticket-corte"></div>

        <dl class="grid grid-cols-2 gap-4 p-6 text-sm sm:grid-cols-4 sm:p-8">
            @foreach ([
                ['fa-solid fa-route', 'Tour', $tour->nom],
                ['fa-regular fa-calendar', 'Salida', ucfirst($f->translatedFormat('D j M')) . ' · ' . substr($salida['hra_sal'], 0, 5)],
                ['fa-solid fa-users', 'Participantes', $reserva['cnt_ptc']],
                ['fa-solid fa-wallet', 'Total', 'Bs. ' . number_format($reserva['pre_tot'], 2, ',', '.')],
            ] as [$ico, $dt, $dd])
                <div>
                    <dt class="flex items-center gap-1.5 text-xs text-pipocas-texto-2"><i class="{{ $ico }} text-pipocas-secundario"></i> {{ $dt }}</dt>
                    <dd class="mt-0.5 font-bold">{{ $dd }}</dd>
                </div>
            @endforeach
        </dl>

        <div class="flex flex-col gap-3 rounded-b-3xl bg-pipocas-fondo/60 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
            <p class="text-sm text-pipocas-texto-2"><i class="fa-regular fa-hourglass-half mr-1 text-pipocas-secundario"></i>
                Paga antes del <b class="text-pipocas-texto">{{ $limitePago->translatedFormat('j \d\e F, H:i') }}</b> para no perder tus cupos.</p>
            <a href="{{ $urlPago }}" class="rsv-btn shrink-0 text-base"><i class="fa-solid fa-credit-card"></i> Ir al pago</a>
        </div>
    </section>

    {{-- Próximos pasos --}}
    <aside class="space-y-4 lg:sticky lg:top-24">
        <section class="rsv-tarjeta">
            <h2 class="rsv-titulo mb-5">Próximos pasos</h2>
            <ol class="relative space-y-6 before:absolute before:bottom-2 before:left-[17px] before:top-2 before:w-0.5 before:bg-gray-200">
                @foreach ([
                    ['fa-solid fa-credit-card', 'Realiza el pago', 'Con tarjeta o QR bancario. Recibirás el comprobante por correo.', true],
                    ['fa-solid fa-qrcode', 'Recibe tus tickets', 'Un ticket con QR por participante en ' . $reserva['cor_ctc'] . '.', false],
                    ['fa-solid fa-person-hiking', 'Disfruta el tour', 'Llega 15 minutos antes; el guía escaneará tu QR.', false],
                ] as [$ico, $tit, $txt, $activo])
                    <li class="relative flex gap-4">
                        <span @class([
                            'relative z-10 grid h-9 w-9 shrink-0 place-items-center rounded-full text-sm ring-4 ring-white',
                            'bg-pipocas-secundario text-white' => $activo,
                            'bg-pipocas-fondo text-pipocas-principal' => ! $activo,
                        ])><i class="{{ $ico }}"></i></span>
                        <div class="min-w-0"><p class="font-bold">{{ $tit }}</p><p class="break-words text-sm text-pipocas-texto-2">{{ $txt }}</p></div>
                    </li>
                @endforeach
            </ol>
        </section>
        <div class="grid gap-3">
            <a href="{{ url('/reservas/preview/estado?est=pendiente') }}" {{-- controlador: route('reservas.estado', $reserva['cod']) --}} class="rsv-btn-sec"><i class="fa-solid fa-magnifying-glass"></i> Ver estado de la reserva</a>
            <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="rsv-btn-sec"><i class="fa-solid fa-compass"></i> Explorar más tours</a>
        </div>
    </aside>
</div>
@endsection
