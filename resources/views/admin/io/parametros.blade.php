{{-- RF-40: parámetros del modelo M/M/c/K por tour. Variables: $tours (tour, par, mu_sugerido, lambda_obs), $historial --}}
@extends('layouts.admin-io')

@section('titulo', 'Ajustes del análisis')
@section('subtitulo', 'Configura cómo se representa cada tour. Estos valores afectan las estimaciones, no asignan
    personal ni cambian los cupos reales.')

@section('contenido')
    @php $fmt = fn ($v, $d = 2) => $v === null ? '—' : number_format((float) $v, $d, ',', '.'); @endphp
    <div class="admin-notice"><i aria-hidden="true" class="fa-solid fa-sliders"></i>
        <div><strong>Primero analiza; cambia estos valores solo cuando corresponda.</strong>
            <p>El panel de capacidad usa la demanda observada de los últimos 30 días. Aquí defines los valores de atención,
                capacidad y costos del modelo. Cada cambio conserva la configuración anterior en el historial.</p>
        </div>
    </div>
    <div class="grid gap-6 xl:grid-cols-2">
        @foreach ($tours as $fila)
            @php
                $t = $fila['tour'];
                $p = $fila['par'];
            @endphp
            <section class="rsv-tarjeta">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-titulo text-xl font-semibold text-pipocas-principal">{{ $t->nom }}</h2>
                        <p class="text-xs text-pipocas-texto-2">Duración: {{ $fmt($t->dur_hrs, 1) }} h · capacidad del tour:
                            {{ $t->cap_max }} personas.<br>Promedio observado: {{ $fmt($fila['lambda_obs'], 3) }}
                            solicitudes/día.</p>
                    </div>
                    @if ($p)
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Activo desde
                            {{ $p->fec_cal->format('d/m/Y') }}</span>
                    @else
                        <span class="rounded-full bg-pipocas-acento/30 px-3 py-1 text-xs font-bold text-pipocas-calido">Sin
                            configurar</span>
                    @endif
                </div>
                <details class="admin-details !p-3" @if ($errors->any() && (int) old('_tour') === $t->id) open @endif>
                    <summary>{{ $p ? 'Revisar o cambiar configuración' : 'Configurar este tour' }}</summary>
                    <form method="POST" action="{{ route('admin.io.parametros.store', $t) }}"
                        class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @csrf
                        <input type="hidden" name="_tour" value="{{ $t->id }}">
                        <label class="rsv-etiqueta col-span-2">Demanda de referencia (solicitudes/día)<input name="lmb"
                                type="number" step="0.0001" min="0" required
                                value="{{ (int) old('_tour') === $t->id ? old('lmb', $p->lmb ?? ($fila['lambda_obs'] ?? 1)) : $p->lmb ?? ($fila['lambda_obs'] ?? 1) }}"
                                class="rsv-campo mt-2"></label>
                        <label class="rsv-etiqueta col-span-2">Atención por guía (tours/día)<input name="mu"
                                type="number" step="0.0001" min="0.0001" required
                                value="{{ (int) old('_tour') === $t->id ? old('mu', $p->mu ?? round($fila['mu_sugerido'], 4)) : $p->mu ?? round($fila['mu_sugerido'], 4) }}"
                                class="rsv-campo mt-2"></label>
                        <label class="rsv-etiqueta col-span-2">Número de guías del modelo<input name="c_srv"
                                type="number" min="1" max="60" required
                                value="{{ (int) old('_tour') === $t->id ? old('c_srv', $p->c_srv ?? 1) : $p->c_srv ?? 1 }}"
                                class="rsv-campo mt-2"></label>
                        <label class="rsv-etiqueta col-span-2">Capacidad del modelo<input name="k_cap" type="number"
                                min="1" max="200" required
                                value="{{ (int) old('_tour') === $t->id ? old('k_cap', $p->k_cap ?? $t->cap_max) : $p->k_cap ?? $t->cap_max }}"
                                class="rsv-campo mt-2"></label>
                        <p class="col-span-full text-[11px] leading-relaxed text-slate-500">Atención sugerida por duración:
                            {{ $fmt($fila['mu_sugerido'], 4) }} tours/guía/día (24 ÷ horas del tour). Es una aproximación,
                            no una jornada laboral. La capacidad del modelo debe ser igual o mayor que el número de guías.
                            La demanda manual es una referencia; no sustituye el promedio observado en el panel.</p>
                        <label class="rsv-etiqueta col-span-1 sm:col-span-2">Costo de espera (Bs/h)<input name="cos_esp"
                                type="number" step="0.01" min="0"
                                value="{{ (int) old('_tour') === $t->id ? old('cos_esp', $p->cos_esp ?? 15) : $p->cos_esp ?? 15 }}"
                                class="rsv-campo mt-2"></label>
                        <label class="rsv-etiqueta col-span-1 sm:col-span-2">Costo de guía ocioso (Bs/h)<input
                                name="cos_srv" type="number" step="0.01" min="0"
                                value="{{ (int) old('_tour') === $t->id ? old('cos_srv', $p->cos_srv ?? 25) : $p->cos_srv ?? 25 }}"
                                class="rsv-campo mt-2"></label>
                        <input name="obs" maxlength="500" aria-label="Motivo del ajuste"
                            placeholder="¿Por qué cambias estos valores? (opcional)" value=""
                            class="rsv-campo col-span-2 sm:col-span-3">
                        <button class="rsv-btn col-span-2 sm:col-span-1 px-4 py-2"><i aria-hidden="true" class="fa-solid fa-floppy-disk"></i>
                            Guardar configuración</button>
                    </form>
                </details>
            </section>
        @endforeach
    </div>

    <section class="rsv-tarjeta">
        <h2 class="rsv-titulo mb-3">Historial de cambios</h2>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="text-xs uppercase text-pipocas-texto-2">
                    <tr>
                        <th class="py-2">Fecha</th>
                        <th>Tour</th>
                        <th>Solicitudes/día</th>
                        <th>Atención por guía/día</th>
                        <th>Guías</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($historial as $h)
                        <tr>
                            <td class="py-2">{{ $h->cre_en?->format('d/m/Y H:i') }}</td>
                            <td>{{ $h->tour?->nom }}</td>
                            <td>{{ $fmt($h->lmb, 4) }}</td>
                            <td>{{ $fmt($h->mu, 4) }}</td>
                            <td>{{ $h->c_srv }}</td>
                            <td>{{ $h->k_cap }}</td>
                            <td>{{ $h->act ? 'Activo' : 'Histórico' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
