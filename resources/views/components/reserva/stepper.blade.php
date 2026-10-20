{{--
    Stepper del flujo de reserva.
    Uso: <x-reserva.stepper :actual="2" />
         <x-reserva.stepper :actual="3" :pasos="['Salida', 'Datos', ...]" />   (pasos personalizados)
    Un paso ya completado se muestra con check; si se pasa :enlaces (mismo orden que los pasos)
    los pasos completados se vuelven enlaces para regresar.
--}}
@props([
    'actual' => 1,
    'pasos' => ['Salida', 'Participantes', 'Equipo', 'Ficha médica', 'Resumen'],
    'enlaces' => [],
])

@php
    $total = count($pasos);
    $actual = max(1, min((int) $actual, $total + 1)); // $total + 1 = todo completado
@endphp

<nav aria-label="Progreso de la reserva" {{ $attributes }}>
    {{-- Móvil: texto + barra de progreso --}}
    <div class="sm:hidden">
        <div class="flex items-baseline justify-between text-sm">
            <span class="font-bold text-pipocas-principal">
                @if ($actual > $total)
                    Reserva completada
                @else
                    Paso {{ $actual }} de {{ $total }} · {{ $pasos[$actual - 1] }}
                @endif
            </span>
            @if ($actual < $total)
                <span class="text-xs text-pipocas-texto-2">Sigue: {{ $pasos[$actual] }}</span>
            @endif
        </div>
        <div class="mt-2 flex gap-1.5" aria-hidden="true">
            @foreach ($pasos as $i => $paso)
                <span @class([
                    'h-1.5 flex-1 rounded-full',
                    'bg-pipocas-principal' => $i + 1 < $actual,
                    'bg-pipocas-secundario' => $i + 1 === $actual,
                    'bg-gray-200' => $i + 1 > $actual,
                ])></span>
            @endforeach
        </div>
    </div>

    {{-- Tablet / escritorio: círculos con etiquetas --}}
    <ol class="hidden items-center sm:flex">
        @foreach ($pasos as $i => $paso)
            @php
                $n = $i + 1;
                $hecho = $n < $actual;
                $esActual = $n === $actual;
                $href = $hecho ? ($enlaces[$i] ?? null) : null;
            @endphp
            <li @class(['flex items-center', 'flex-1' => $n < $total])>
                @if ($href)
                    <a href="{{ $href }}" class="group flex items-center gap-2.5">
                @else
                    <span class="flex items-center gap-2.5" @if ($esActual) aria-current="step" @endif>
                @endif
                    <span @class([
                        'grid h-9 w-9 shrink-0 place-items-center rounded-full text-sm font-bold transition',
                        'bg-pipocas-principal text-white' => $hecho,
                        'bg-pipocas-secundario text-white ring-4 ring-pipocas-secundario/20' => $esActual,
                        'border-2 border-gray-300 bg-white text-pipocas-texto-2' => ! $hecho && ! $esActual,
                        'group-hover:bg-pipocas-hover' => $href,
                    ])>
                        @if ($hecho)
                            <i class="fa-solid fa-check text-xs"></i>
                        @else
                            {{ $n }}
                        @endif
                    </span>
                    <span @class([
                        'whitespace-nowrap text-sm',
                        'font-bold text-pipocas-texto' => $esActual,
                        'font-semibold text-pipocas-principal' => $hecho,
                        'text-pipocas-texto-2' => ! $hecho && ! $esActual,
                        'hidden lg:inline' => ! $esActual,
                    ])>{{ $paso }}</span>
                @if ($href) </a> @else </span> @endif

                @if ($n < $total)
                    <span @class([
                        'mx-3 h-0.5 flex-1 rounded-full',
                        'bg-pipocas-principal' => $hecho,
                        'bg-gray-200' => ! $hecho,
                    ])></span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
