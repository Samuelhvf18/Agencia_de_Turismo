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
    $tour ??= (object) ['nom' => 'Valle de la Luna'];
    $salida ??= ['fec_sal' => now()->addDays(2)->toDateString(), 'hra_sal' => '08:00:00'];
    $urlPago ??= '#'; // controlador: route('pagos.create', $reserva)

    $f = \Carbon\Carbon::parse($salida['fec_sal'])->locale('es');
    $limitePago = \Carbon\Carbon::parse($reserva['cre_en'])->addHours(24)->locale('es');
@endphp

@section('titulo', 'Reserva registrada')

@section('stepper')
    <x-reserva.stepper :actual="6" />
@endsection

@section('contenido')
<div class="mx-auto max-w-2xl space-y-6">
    <section class="rsv-tarjeta overflow-hidden p-0 text-center sm:p-0">
        <div class="bg-gradient-to-br from-pipocas-principal to-pipocas-complementario px-5 pb-8 pt-10 text-white sm:px-10">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-white/15 ring-8 ring-white/10">
                <i class="fa-solid fa-check text-3xl text-pipocas-acento"></i>
            </span>
            <h1 class="mt-5 font-titulo text-3xl font-semibold sm:text-4xl">¡Reserva registrada!</h1>
            <p class="mt-2 text-white/80">Gracias, {{ \Illuminate\Support\Str::before($reserva['nom_ctc'], ' ') }}. Guardamos tus cupos para <b class="text-white">{{ $tour->nom }}</b>.</p>
        </div>

        <div class="px-5 py-8 sm:px-10" x-data="{ copiado: false }">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-pipocas-texto-2">Código de reserva</p>
            <p class="mt-2 break-all font-mono text-3xl font-bold tracking-wider text-pipocas-principal sm:text-5xl">{{ $reserva['cod'] }}</p>
            <button type="button" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-pipocas-complementario hover:text-pipocas-hover"
                    @click="navigator.clipboard?.writeText(@js($reserva['cod'])); copiado = true; setTimeout(() => copiado = false, 2000)">
                <i class="fa-regular" :class="copiado ? 'fa-circle-check' : 'fa-copy'"></i>
                <span x-text="copiado ? '¡Copiado!' : 'Copiar código'">Copiar código</span>
            </button>

            <div class="mt-6 inline-flex items-center gap-2 rounded-full bg-pipocas-acento/25 px-4 py-2 text-sm font-bold text-pipocas-calido">
                <span class="h-2 w-2 animate-pulse rounded-full bg-pipocas-secundario"></span> Pendiente de pago
            </div>

            <dl class="mt-8 grid gap-4 rounded-2xl bg-pipocas-fondo p-5 text-left text-sm sm:grid-cols-3">
                <div><dt class="text-pipocas-texto-2">Salida</dt><dd class="font-semibold">{{ ucfirst($f->translatedFormat('D j M')) }} · {{ substr($salida['hra_sal'], 0, 5) }}</dd></div>
                <div><dt class="text-pipocas-texto-2">Participantes</dt><dd class="font-semibold">{{ $reserva['cnt_ptc'] }}</dd></div>
                <div><dt class="text-pipocas-texto-2">Total</dt><dd class="font-titulo text-xl font-semibold text-pipocas-principal">Bs. {{ number_format($reserva['pre_tot'], 2, ',', '.') }}</dd></div>
            </dl>

            <a href="{{ $urlPago }}" class="rsv-btn mt-8 w-full text-base sm:w-auto">
                <i class="fa-solid fa-credit-card"></i> Ir al pago
            </a>
            <p class="mt-3 text-xs text-pipocas-texto-2">Paga antes del {{ $limitePago->translatedFormat('j \d\e F, H:i') }} para no perder tus cupos.</p>
        </div>
    </section>

    <section class="rsv-tarjeta">
        <h2 class="rsv-titulo mb-5">Próximos pasos</h2>
        <ol class="space-y-5">
            <li class="flex gap-4">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-pipocas-secundario text-sm font-bold text-white">1</span>
                <div><p class="font-semibold">Realiza el pago en línea</p><p class="text-sm text-pipocas-texto-2">Con tarjeta o QR bancario. Recibirás el comprobante en tu correo.</p></div>
            </li>
            <li class="flex gap-4">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-pipocas-principal/15 text-sm font-bold text-pipocas-principal">2</span>
                <div><p class="font-semibold">Recibe los tickets con código QR</p><p class="text-sm text-pipocas-texto-2">Enviaremos un ticket por participante a <b class="break-all text-pipocas-texto">{{ $reserva['cor_ctc'] }}</b>.</p></div>
            </li>
            <li class="flex gap-4">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-pipocas-principal/15 text-sm font-bold text-pipocas-principal">3</span>
                <div><p class="font-semibold">Preséntate el día del tour</p><p class="text-sm text-pipocas-texto-2">Llega 15 minutos antes; el guía escaneará tu QR al abordar.</p></div>
            </li>
        </ol>
        <div class="mt-6 flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row">
            <a href="{{ url('/reservas/preview/estado') }}" {{-- controlador: route('reservas.estado', $reserva['cod']) --}} class="rsv-btn-sec flex-1"><i class="fa-solid fa-magnifying-glass"></i> Ver estado de la reserva</a>
            <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="rsv-btn-sec flex-1"><i class="fa-solid fa-compass"></i> Explorar más tours</a>
        </div>
    </section>
</div>
@endsection
