{{-- Pantalla de la pasarela simulada (imita al proveedor externo). Variables: $pago, $reserva, $metodos --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('PayLab · Pasarela de pago simulada') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <span class="text-xl font-extrabold tracking-tight text-indigo-700"><i class="fa-solid fa-shield-halved"></i> PayLab</span>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">{{ __('MODO SIMULACIÓN') }}</span>
        </div>

        <div class="overflow-hidden rounded-3xl bg-white shadow-xl">
            <div class="bg-indigo-700 px-6 py-5 text-white">
                <p class="text-sm opacity-80">{{ __('Pagar a Pipocas Agencia de Viajes y Turismo') }}</p>
                <p class="mt-1 text-3xl font-bold">Bs. {{ number_format($pago->mto, 2, ',', '.') }}</p>
                <p class="mt-1 text-xs opacity-80">Referencia {{ $pago->ref_trx }} · {{ __('Reserva') }} {{ $reserva->cod }}</p>
            </div>

            <div class="space-y-4 p-6 text-sm">
                <p class="flex items-center gap-2"><i class="fa-solid fa-wallet text-indigo-600"></i> {{ __('Método:') }} <b>{{ __($metodos[$pago->mtd] ?? $pago->mtd) }}</b></p>

                @if ($pago->mtd === 'qr')
                    <div class="rounded-2xl border border-dashed border-slate-300 p-4 text-center text-slate-500">
                        <i class="fa-solid fa-qrcode text-6xl text-slate-700"></i>
                        <p class="mt-2">{{ __('Escanea con tu app bancaria (simulado)') }}</p>
                    </div>
                @elseif ($pago->mtd === 'transferencia')
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p>{{ __('Banco Simulado S.A. · Cuenta 1000-000000') }}</p>
                        <p>{{ __('Glosa:') }} <b>{{ $pago->ref_trx }}</b></p>
                    </div>
                @else
                    <div class="rounded-2xl bg-slate-50 p-4 text-slate-600">
                        <p><i class="fa-regular fa-credit-card mr-1"></i> {{ __('Tarjeta de prueba •••• 4242 (no se piden datos reales)') }}</p>
                    </div>
                @endif

                <p class="text-xs text-slate-500">{{ __('Esta pantalla simula al proveedor de pagos. Al elegir un resultado, la pasarela notifica al sistema mediante un webhook firmado (HMAC-SHA256).') }}</p>

                <form method="POST" action="{{ route('pasarela.resolver', $pago->ref_trx) }}" class="grid gap-3 sm:grid-cols-2">
                    @csrf
                    <button type="submit" name="resultado" value="aprobar" class="rounded-xl bg-emerald-600 px-4 py-3 font-bold text-white transition hover:bg-emerald-700">
                        <i class="fa-solid fa-check"></i>{{ __('Aprobar pago') }}</button>
                    <button type="submit" name="resultado" value="rechazar" class="rounded-xl bg-slate-200 px-4 py-3 font-bold text-slate-700 transition hover:bg-slate-300">
                        <i class="fa-solid fa-xmark"></i>{{ __('Rechazar') }}</button>
                </form>
            </div>
        </div>

        <a href="{{ route('reservas.estado', $reserva) }}" class="mt-6 text-center text-sm font-semibold text-indigo-700 hover:underline">{{ __('Cancelar y volver a la reserva') }}</a>
    </div>
</body>
</html>
