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

@section('contenido')
<div class="mx-auto grid max-w-5xl items-center gap-8 lg:grid-cols-2 lg:gap-12">
    <div class="text-center lg:text-left">
        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-pipocas-acento text-2xl text-pipocas-calido lg:mx-0">
            <i class="fa-solid fa-ticket"></i>
        </span>
        <h1 class="mt-5 font-titulo text-3xl font-semibold leading-tight sm:text-5xl">Consulta tu reserva</h1>
        <p class="mt-3 text-pipocas-texto-2 sm:text-lg">Revisa el estado del pago, la fecha de salida y descarga los tickets con código QR de tus participantes.</p>
        <ul class="mt-6 hidden space-y-3 text-sm lg:block">
            <li class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-pipocas-complementario"></i> No necesitas una cuenta</li>
            <li class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-pipocas-complementario"></i> El código está en el correo de confirmación</li>
            <li class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-pipocas-complementario"></i> Usa el mismo correo con el que reservaste</li>
        </ul>
    </div>

    <form method="POST" action="{{ $accionForm }}" class="rsv-tarjeta space-y-5">
        @csrf

        @if ($errors->any())
            <div class="flex gap-2 rounded-2xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <div>
            <label for="cod" class="rsv-etiqueta">Código de reserva</label>
            <div class="relative">
                <i class="fa-solid fa-hashtag pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-pipocas-texto-2"></i>
                <input id="cod" name="cod" type="text" maxlength="20" required autocomplete="off" autocapitalize="characters"
                       value="{{ old('cod', request('cod')) }}" placeholder="PIP-2610-4F7K"
                       class="rsv-campo py-3 pl-10 font-mono uppercase tracking-wider">
            </div>
            @error('cod') <p class="rsv-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="cor_ctc" class="rsv-etiqueta">Correo del contacto</label>
            <div class="relative">
                <i class="fa-regular fa-envelope pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-pipocas-texto-2"></i>
                <input id="cor_ctc" name="cor_ctc" type="email" maxlength="150" required autocomplete="email"
                       value="{{ old('cor_ctc') }}" placeholder="tucorreo@ejemplo.com" class="rsv-campo py-3 pl-10">
            </div>
            @error('cor_ctc') <p class="rsv-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rsv-btn w-full text-base"><i class="fa-solid fa-magnifying-glass"></i> Buscar reserva</button>

        <p class="text-center text-sm text-pipocas-texto-2">
            ¿Perdiste tu código? Escríbenos a nuestro chatbot
            <a href="{{ url('/chatbot') }}" class="font-semibold text-pipocas-principal hover:text-pipocas-hover">aquí</a>.
        </p>
    </form>
</div>
@endsection
