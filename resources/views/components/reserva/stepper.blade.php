{{--
    Stepper del flujo de reserva.
    Uso: <x-reserva.stepper :actual="2" />
         <x-reserva.stepper :actual="3" :enlaces="[url('/salida'), url('/participantes')]" />
    - :actual = número del paso en curso (si es mayor que el total, todos se muestran completados).
    - :enlaces (mismo orden que los pasos) convierte los pasos completados en enlaces para regresar.
    - :pasos permite cambiar los pasos: [['Etiqueta', 'clase-icono'], ...]
--}}
@props([
    'actual' => 1,
    'pasos' => [
        ['Salida', 'fa-regular fa-calendar'],
        ['Participantes', 'fa-solid fa-users'],
        ['Equipo', 'fa-solid fa-person-hiking'],
        ['Ficha médica', 'fa-solid fa-file-medical'],
        ['Resumen', 'fa-solid fa-receipt'],
    ],
    'enlaces' => [],
])

@php
    $total = count($pasos);
    $actual = max(1, min((int) $actual, $total + 1)); // $total + 1 = todo completado
    $pct = $actual > $total ? 100 : round(($actual - 1) * 100 / ($total - 1));
@endphp

<nav aria-label="Progreso de la reserva" {{ $attributes }}>
    {{-- Móvil: paso actual con icono + barra segmentada --}}
    <div class="sm:hidden">
        <div class="flex items-center gap-3">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-pipocas-secundario text-white">
                <i class="{{ $actual > $total ? 'fa-solid fa-check' : $pasos[$actual - 1][1] }}"></i>
            </span>
            <div class="min-w-0 flex-1 leading-tight">
                <p class="text-[11px] font-bold uppercase tracking-wider text-pipocas-texto-2">
                    {{ $actual > $total ? 'Completado' : "Paso $actual de $total" }}
                </p>
                <p class="truncate font-bold text-pipocas-principal">{{ $actual > $total ? '¡Reserva lista!' : $pasos[$actual - 1][0] }}</p>
            </div>
            @if ($actual < $total)
                <span class="text-right text-[11px] leading-tight text-pipocas-texto-2">Sigue<br><b class="text-pipocas-texto">{{ $pasos[$actual][0] }}</b></span>
            @endif
        </div>
        <div class="mt-3 flex gap-1.5" aria-hidden="true">
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

    {{-- Tablet / escritorio --}}
    <div class="relative hidden sm:block">
        {{-- Línea de fondo y de progreso, detrás de los círculos --}}
        <div class="absolute top-5 h-1 rounded-full bg-gray-200" style="left: {{ 50 / $total }}%; right: {{ 50 / $total }}%" aria-hidden="true">
            <div class="h-full rounded-full bg-gradient-to-r from-pipocas-principal to-pipocas-secundario" style="width: {{ $pct }}%"></div>
        </div>

        <ol class="relative grid" style="grid-template-columns: repeat({{ $total }}, minmax(0, 1fr))">
            @foreach ($pasos as $i => [$etiqueta, $icono])
                @php
                    $n = $i + 1;
                    $hecho = $n < $actual;
                    $esActual = $n === $actual;
                    $href = $hecho ? ($enlaces[$i] ?? null) : null;
                @endphp
                <li class="flex flex-col items-center text-center">
                    @if ($href)
                        <a href="{{ $href }}" class="group flex flex-col items-center" title="Volver a {{ $etiqueta }}">
                    @else
                        <span class="flex flex-col items-center" @if ($esActual) aria-current="step" @endif>
                    @endif
                        <span @class([
                            'grid h-11 w-11 place-items-center rounded-full text-sm ring-4 ring-white transition',
                            'bg-pipocas-principal text-white' => $hecho,
                            'bg-pipocas-secundario text-white shadow-lg shadow-pipocas-secundario/30 scale-110' => $esActual,
                            'border-2 border-gray-200 bg-white text-gray-400' => ! $hecho && ! $esActual,
                            'group-hover:bg-pipocas-hover' => $href,
                        ])>
                            <i class="{{ $hecho ? 'fa-solid fa-check' : $icono }}"></i>
                        </span>
                        <span class="mt-2 text-[10px] font-bold uppercase tracking-wider text-pipocas-texto-2">Paso {{ $n }}</span>
                        <span @class([
                            'text-sm',
                            'font-bold text-pipocas-texto' => $esActual,
                            'font-semibold text-pipocas-principal group-hover:underline' => $hecho,
                            'text-pipocas-texto-2' => ! $hecho && ! $esActual,
                        ])>{{ $etiqueta }}</span>
                    @if ($href) </a> @else </span> @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
