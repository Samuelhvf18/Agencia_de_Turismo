@extends('layouts.reserva')

@php
    /*
     | Página pública para consultar una reserva.
     | El controlador debe pasar:
     |   $accionForm -> route('reservas.consultar.buscar')  (POST con cod y cor_ctc; buscar en reservas por cod + cor_ctc)
     | Si no hay coincidencia, volver con ->withErrors(['cod' => 'No encontramos una reserva con esos datos.'])->withInput()
     */
    $accionForm ??= url('/reservas/preview/estado'); // controlador: route('reservas.consultar.buscar')
@endphp

@section('titulo', 'Consultar reserva')

@section('hero')
    <x-reserva.hero :img="asset('images/tours/huayna-potosi.jpg')" etiqueta="Mi reserva" titulo="Consulta tu reserva"
                    subtitulo="Revisa el estado del pago, la fecha de salida y descarga los tickets con código QR de tus participantes. No necesitas una cuenta." />
@endsection

@section('contenido')
<div class="relative z-10 -mt-14 grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start lg:gap-8">
    <form method="POST" action="{{ $accionForm }}" class="rsv-tarjeta space-y-5 sm:p-8">
        @csrf

        <header class="flex items-center gap-4">
            <span class="rsv-icono"><i class="fa-solid fa-ticket"></i></span>
            <div>
                <h2 class="rsv-titulo">Busca tu reserva</h2>
                <p class="text-sm text-pipocas-texto-2">Ingresa el código y el correo con el que reservaste.</p>
            </div>
        </header>

        @if ($errors->any())
            <div class="flex gap-2 rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="cod" class="rsv-etiqueta">Código de reserva</label>
                <div class="relative">
                    <i class="fa-solid fa-hashtag pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-pipocas-texto-2"></i>
                    <input id="cod" name="cod" type="text" maxlength="20" required autocomplete="off" autocapitalize="characters"
                           value="{{ old('cod', request('cod')) }}" placeholder="PIP-2610-4F7K"
                           class="rsv-campo py-3.5 pl-10 font-mono uppercase tracking-wider">
                </div>
                @error('cod') <p class="rsv-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="cor_ctc" class="rsv-etiqueta">Correo del contacto</label>
                <div class="relative">
                    <i class="fa-regular fa-envelope pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-pipocas-texto-2"></i>
                    <input id="cor_ctc" name="cor_ctc" type="email" maxlength="150" required autocomplete="email"
                           value="{{ old('cor_ctc') }}" placeholder="tucorreo@ejemplo.com" class="rsv-campo py-3.5 pl-10">
                </div>
                @error('cor_ctc') <p class="rsv-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="rsv-btn w-full text-base sm:w-auto"><i class="fa-solid fa-magnifying-glass"></i> Buscar reserva</button>

        <div class="grid gap-3 border-t border-gray-100 pt-5 text-sm sm:grid-cols-3">
            @foreach ([
                ['fa-solid fa-envelope-open-text', 'El código está en el correo de confirmación'],
                ['fa-solid fa-at', 'Usa el mismo correo con el que reservaste'],
                ['fa-solid fa-mobile-screen', 'Muestra el QR desde tu celular el día del tour'],
            ] as [$ico, $txt])
                <p class="flex items-start gap-2.5 text-pipocas-texto-2"><i class="{{ $ico }} mt-0.5 text-pipocas-complementario"></i> {{ $txt }}</p>
            @endforeach
        </div>
    </form>

    <aside class="space-y-4">
        <div class="rsv-topo overflow-hidden rounded-3xl bg-pipocas-secundario p-6 text-white shadow-tarjeta">
            <i class="fa-regular fa-comments text-3xl text-pipocas-acento"></i>
            <h3 class="mt-3 font-titulo text-xl font-semibold">¿Perdiste tu código?</h3>
            <p class="mt-1 text-sm text-white/85">Nuestro asistente virtual te ayuda a recuperarlo en segundos, a cualquier hora.</p>
            <a href="{{ url('/chatbot') }}" class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-bold text-pipocas-secundario transition hover:bg-pipocas-acento hover:text-pipocas-calido">
                Hablar con el asistente <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="rounded-3xl border border-pipocas-principal/10 bg-white/70 p-5 text-sm">
            <p class="mb-3 font-bold">Estados de una reserva</p>
            <ul class="space-y-2.5">
                <li class="flex items-center gap-2.5"><span class="rsv-chip bg-pipocas-acento/30 text-pipocas-calido"><i class="fa-regular fa-clock"></i> Pendiente</span> <span class="text-pipocas-texto-2">falta el pago</span></li>
                <li class="flex items-center gap-2.5"><span class="rsv-chip bg-emerald-100 text-emerald-800"><i class="fa-solid fa-circle-check"></i> Confirmada</span> <span class="text-pipocas-texto-2">tickets emitidos</span></li>
                <li class="flex items-center gap-2.5"><span class="rsv-chip bg-red-100 text-red-700"><i class="fa-solid fa-ban"></i> Cancelada</span> <span class="text-pipocas-texto-2">cupos liberados</span></li>
            </ul>
        </div>
    </aside>
</div>
@endsection
