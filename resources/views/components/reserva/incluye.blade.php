{{--
    Tarjeta "Incluye" + política de cancelación para la columna lateral.
    Uso: <x-reserva.incluye />  o  <x-reserva.incluye :items="[['fa-solid fa-van-shuttle', 'Transporte']]" />
--}}
@props([
    'items' => [
        ['fa-solid fa-user-tie', 'Guía certificado bilingüe'],
        ['fa-solid fa-van-shuttle', 'Transporte ida y vuelta'],
        ['fa-solid fa-kit-medical', 'Botiquín y oxígeno'],
        ['fa-solid fa-qrcode', 'Ticket digital con QR'],
    ],
    'politica' => 'Cancelación gratuita hasta 48 horas antes de la salida.',
])

<div {{ $attributes->merge(['class' => 'rounded-3xl border border-pipocas-principal/10 bg-white/70 p-5 text-sm']) }}>
    <p class="mb-3 font-bold">Incluye</p>
    <ul class="space-y-2.5 text-pipocas-texto-2">
        @foreach ($items as [$ico, $txt])
            <li class="flex items-center gap-2.5"><i class="{{ $ico }} w-4 text-pipocas-principal"></i> {{ $txt }}</li>
        @endforeach
    </ul>
    @if ($politica)
        <p class="mt-4 flex gap-2 rounded-2xl bg-emerald-50 p-3 text-xs text-emerald-800">
            <i class="fa-solid fa-rotate-left mt-0.5"></i> {{ $politica }}
        </p>
    @endif
</div>
