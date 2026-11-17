@extends('layouts.reserva')
@section('titulo', __('Mi cuenta'))
@section('contenido')
@php
    $tel = $usuario->tel ?? '';
    $prefijo = collect(array_keys(config('turismo.prefijos')))->sortByDesc(fn ($p) => strlen($p))->first(fn ($p) => str_starts_with($tel, $p)) ?? '+591';
@endphp
<div class="mx-auto max-w-3xl space-y-6">
    <a href="{{ route('reservas.historial') }}" class="text-sm font-semibold text-pipocas-principal">← {{ __('Volver a mis reservas') }}</a>
    <div><h1 class="font-titulo text-3xl text-pipocas-principal">{{ __('Mi cuenta') }}</h1><p class="mt-2 text-pipocas-texto-2">{{ __('Consulta tus datos y mantén tu teléfono de contacto actualizado.') }}</p></div>
    @if (session('estado'))<p role="status" class="rounded-xl bg-emerald-50 p-4 text-emerald-800">{{ session('estado') }}</p>@endif
    <section class="rsv-tarjeta"><h2 class="rsv-titulo mb-5">{{ __('Mis datos') }}</h2>
        <dl class="grid gap-5 sm:grid-cols-2">
            @foreach (['Nombre completo' => $usuario->nom, 'Correo electrónico' => $usuario->cor, 'Documento' => trim(($usuario->tip_doc ?? '').' '.($usuario->num_doc ?? '')) ?: 'Sin registrar', 'Teléfono' => $usuario->tel ?: 'Sin registrar'] as $campo => $valor)
                <div><dt class="text-sm text-pipocas-texto-2">{{ __($campo) }}</dt><dd class="mt-1 break-words font-semibold">{{ $valor }}</dd></div>
            @endforeach
        </dl>
    </section>
    <details class="rsv-tarjeta" @if ($errors->any()) open @endif>
        <summary class="cursor-pointer font-bold text-pipocas-principal">{{ __('Editar mi teléfono') }}</summary>
        <form method="POST" action="{{ route('turista.cuenta.update') }}" class="mt-5 space-y-4">
            @csrf @method('PUT')
            <p class="text-sm text-pipocas-texto-2">{{ __('Se usará como contacto para tus próximas reservas.') }}</p>
            <x-validation-errors />
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label for="prefijo" class="rsv-etiqueta">{{ __('País / prefijo') }}</label><select id="prefijo" name="prefijo" class="rsv-campo" autocomplete="tel-country-code">@foreach (config('turismo.prefijos') as $codigo => $nombre)<option value="{{ $codigo }}" @selected(old('prefijo', $prefijo) === $codigo)>{{ $nombre }}</option>@endforeach</select></div>
                <div><label for="telefono" class="rsv-etiqueta">{{ __('Teléfono') }}</label><input id="telefono" name="telefono" type="tel" required inputmode="numeric" pattern="[0-9]{4,14}" maxlength="14" data-numeros autocomplete="tel-national" class="rsv-campo" value="{{ old('telefono', substr($tel, strlen($prefijo))) }}"><p class="mt-1 text-xs">{{ __('Solo números, sin prefijo ni espacios.') }}</p></div>
            </div>
            <button class="rsv-btn" type="submit">{{ __('Guardar teléfono') }}</button>
        </form>
    </details>
</div>
@endsection
