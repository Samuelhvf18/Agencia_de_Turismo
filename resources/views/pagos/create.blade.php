@extends('layouts.reserva')

{{-- Pago de una reserva. Variables: $reserva, $tour, $salida, $metodos, $saldo, $pagado, $minimo, $historial --}}

@section('titulo', 'Pagar reserva ' . $reserva->cod)

@section('contenido')
@php $bs = fn ($n) => 'Bs. ' . number_format($n, 2, ',', '.'); @endphp
<form method="POST" action="{{ route('pagos.store', $reserva) }}"
      x-data="{ mtd: @js(old('mtd', 'tarjeta')), mto: @js((string) old('mto', number_format($saldo, 2, '.', ''))) }"
      class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[1fr_340px] lg:items-start">
    @csrf

    <div class="space-y-6">
        <div>
            <a href="{{ route('reservas.estado', $reserva) }}" class="mb-3 inline-flex items-center gap-2 text-sm font-semibold text-pipocas-principal hover:text-pipocas-hover">
                <i class="fa-solid fa-arrow-left"></i> Volver a la reserva
            </a>
            <h1 class="font-titulo text-3xl font-semibold leading-tight sm:text-4xl">Pago en línea</h1>
            <p class="mt-2 text-pipocas-texto-2">{{ __('Reserva') }} <b class="font-mono text-pipocas-texto">{{ $reserva->cod }}</b> · {{ $tour->nom }} · {{ $salida->fec_sal->format('d/m/Y') }}</p>
        </div>

        <section class="rsv-tarjeta">
            <h2 class="rsv-titulo mb-4">Método de pago</h2>
            <div class="grid gap-3 sm:grid-cols-3">
                @foreach (['tarjeta' => 'fa-regular fa-credit-card', 'qr' => 'fa-solid fa-qrcode', 'transferencia' => 'fa-solid fa-building-columns'] as $valor => $icono)
                    <label class="cursor-pointer rounded-2xl border-2 border-gray-200 p-4 text-center transition hover:border-pipocas-complementario"
                           :class="mtd === '{{ $valor }}' && '!border-pipocas-principal bg-pipocas-principal/5 ring-4 ring-pipocas-principal/10'">
                        <input type="radio" name="mtd" value="{{ $valor }}" x-model="mtd" class="sr-only">
                        <i class="{{ $icono }} text-2xl text-pipocas-secundario"></i>
                        <p class="mt-2 font-semibold">{{ $metodos[$valor] }}</p>
                    </label>
                @endforeach
            </div>
            @error('mtd') <p class="rsv-error">{{ $message }}</p> @enderror
        </section>

        <section class="rsv-tarjeta">
            <h2 class="rsv-titulo mb-1">Monto a pagar</h2>
            <p class="mb-4 text-sm text-pipocas-texto-2">Puedes pagar el total o un anticipo de al menos {{ $bs($minimo) }} y completar el saldo después. Los tickets se emiten cuando el pago está completo.</p>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="rsv-btn-sec px-4 py-2 text-sm" @click="mto = @js(number_format($saldo, 2, '.', ''))">Saldo completo · {{ $bs($saldo) }}</button>
                @if ($minimo < $saldo)
                    <button type="button" class="rsv-btn-sec px-4 py-2 text-sm" @click="mto = @js(number_format($minimo, 2, '.', ''))">Anticipo mínimo · {{ $bs($minimo) }}</button>
                @endif
            </div>
            <label for="mto" class="rsv-etiqueta mt-4">Monto (Bs.)</label>
            <input id="mto" name="mto" type="number" step="0.01" min="{{ $minimo }}" max="{{ $saldo }}" x-model="mto" class="rsv-campo max-w-xs text-lg font-bold">
            @error('mto') <p class="rsv-error">{{ $message }}</p> @enderror
        </section>

        @if ($historial->isNotEmpty())
            <section class="rsv-tarjeta">
                <h2 class="rsv-titulo mb-3">Pagos anteriores</h2>
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($historial as $p)
                        <li class="flex justify-between py-2">
                            <span>{{ $metodos[$p->mtd] ?? $p->mtd }} · {{ $p->pag_en->format('d/m/Y H:i') }} · <b>{{ ucfirst($p->est) }}</b></span>
                            <span class="font-semibold">{{ $bs($p->mto) }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>

    <aside class="lg:sticky lg:top-6">
        <div class="rsv-tarjeta space-y-4">
            <h2 class="rsv-titulo">Resumen</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-pipocas-texto-2">Total de la reserva</dt><dd class="font-semibold">{{ $bs($reserva->pre_tot) }}</dd></div>
                <div class="flex justify-between"><dt class="text-pipocas-texto-2">Pagado</dt><dd class="font-semibold text-emerald-700">{{ $bs($pagado) }}</dd></div>
                <div class="flex items-baseline justify-between border-t border-dashed border-gray-200 pt-3">
                    <dt class="font-bold">Saldo</dt><dd class="font-titulo text-2xl font-semibold text-pipocas-principal">{{ $bs($saldo) }}</dd>
                </div>
            </dl>
            <button type="submit" class="rsv-btn w-full text-base"><i class="fa-solid fa-lock"></i> Continuar a la pasarela</button>
            <p class="text-center text-xs text-pipocas-texto-2"><i class="fa-solid fa-flask mr-1"></i> Pasarela simulada: no se realizan cobros reales.</p>
        </div>
    </aside>
</form>
@endsection
