{{--
    Cabecera de las páginas de reserva, con la foto del tour de fondo.
    Uso: @section('hero')
             <x-reserva.hero :tour="$tour" etiqueta="Paso 1" titulo="¿Cuándo quieres ir?"
                             subtitulo="Elige una salida" :volver="['url' => ..., 'texto' => 'Volver al tour']" />
         @endsection
    - Si la página tiene stepper, deja espacio abajo para que la tarjeta del stepper se monte encima.
    - El slot se muestra a la derecha en escritorio (p. ej. datos rápidos del tour).
--}}
@props(['tour' => null, 'titulo', 'subtitulo' => null, 'etiqueta' => null, 'volver' => null, 'img' => null])

@php
    $imagen = $img ?? ($tour->img ?? null);
@endphp

<section {{ $attributes->merge(['class' => 'relative isolate overflow-hidden bg-pipocas-principal text-white']) }}>
    @if ($imagen)
        <img src="{{ $imagen }}" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" aria-hidden="true">
    @endif
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0f2f4a]/95 via-[#123a5a]/80 to-pipocas-principal/40"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-[#0f2f4a]/70 to-transparent"></div>
    <div class="rsv-topo absolute inset-0 -z-10"></div>

    <div @class([
        'mx-auto flex max-w-6xl flex-col gap-6 px-4 pt-8 sm:px-6 sm:pt-12 lg:flex-row lg:items-end lg:justify-between lg:px-8',
        'pb-16 sm:pb-20' => $__env->hasSection('stepper'),
        'pb-10 sm:pb-14' => ! $__env->hasSection('stepper'),
    ])>
        <div class="max-w-2xl">
            @if ($volver)
                <a href="{{ $volver['url'] }}" class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold backdrop-blur transition hover:bg-white/20">
                    <i class="fa-solid fa-arrow-left"></i> {{ $volver['texto'] }}
                </a>
            @endif
            @if ($etiqueta || $tour)
                <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-pipocas-acento">
                    {{ $etiqueta }}@if ($etiqueta && $tour) · @endif{{ $tour ? $tour->nom : '' }}
                </p>
            @endif
            <h1 class="mt-2 font-titulo text-3xl font-semibold leading-[1.1] sm:text-5xl">{{ $titulo }}</h1>
            @if ($subtitulo)
                <p class="mt-3 max-w-xl text-sm text-white/80 sm:text-base">{{ $subtitulo }}</p>
            @endif
        </div>

        @if (trim($slot) !== '')
            <div class="shrink-0">{{ $slot }}</div>
        @endif
    </div>
</section>
