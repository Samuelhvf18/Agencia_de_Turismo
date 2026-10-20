{{-- RF-22 a RF-24: propuestas de asignación. Variables: $pendientes, $revisadas, $guias, $vehiculos, $tip --}}
@extends('layouts.admin-io')

@section('titulo', 'Asignación óptima de recursos')
@section('subtitulo', 'Método húngaro para guías (carga, experiencia e idiomas) y vehículos (costo de combustible). Nada se aplica sin tu confirmación.')

@section('contenido')
@php $fmt = fn ($v, $d = 2) => number_format((float) $v, $d, ',', '.'); @endphp

<section class="rsv-tarjeta">
    <h2 class="rsv-titulo mb-3">Ejecutar el modelo</h2>
    <form method="POST" action="{{ route('admin.io.propuestas.generar') }}" class="grid gap-3 sm:grid-cols-5 sm:items-end">
        @csrf
        <label class="rsv-etiqueta">Recurso
            <select name="tip" class="rsv-campo">
                <option value="guia" @selected($tip !== 'vehiculo')>Guías (RF-22)</option>
                <option value="vehiculo" @selected($tip === 'vehiculo')>Vehículos (RF-23)</option>
            </select>
        </label>
        <label class="rsv-etiqueta">Desde<input type="date" name="desde" value="{{ today()->toDateString() }}" min="{{ today()->toDateString() }}" class="rsv-campo"></label>
        <label class="rsv-etiqueta">Hasta<input type="date" name="hasta" value="{{ today()->addDays(14)->toDateString() }}" class="rsv-campo"></label>
        <label class="flex items-center gap-2 pb-3 text-sm font-semibold"><input type="hidden" name="solo_sin_asignar" value="0"><input type="checkbox" name="solo_sin_asignar" value="1" checked class="rsv-check"> Solo salidas sin asignar</label>
        <button class="rsv-btn px-4 py-2.5"><i class="fa-solid fa-wand-magic-sparkles"></i> Generar propuestas</button>
    </form>
    <p class="mt-3 text-xs text-pipocas-texto-2">Costo guía c<sub>ij</sub> = 40·(horas semana / límite) + peso·(10 − años de experiencia) + 60·idiomas faltantes; celdas con choque de horario o límite superado valen M. Costo vehículo = km estimados × Bs/km + 2·asientos vacíos.</p>
</section>

<section class="rsv-tarjeta">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <h2 class="rsv-titulo">Propuestas pendientes ({{ $pendientes->count() }})</h2>
        <div class="flex gap-2 text-sm font-semibold">
            <a href="{{ route('admin.io.propuestas') }}" class="rounded-lg px-3 py-1.5 {{ ! $tip ? 'bg-pipocas-principal text-white' : 'bg-pipocas-fondo' }}">Todas</a>
            <a href="{{ route('admin.io.propuestas', ['tip' => 'guia']) }}" class="rounded-lg px-3 py-1.5 {{ $tip === 'guia' ? 'bg-pipocas-principal text-white' : 'bg-pipocas-fondo' }}">Guías</a>
            <a href="{{ route('admin.io.propuestas', ['tip' => 'vehiculo']) }}" class="rounded-lg px-3 py-1.5 {{ $tip === 'vehiculo' ? 'bg-pipocas-principal text-white' : 'bg-pipocas-fondo' }}">Vehículos</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead class="text-xs uppercase text-pipocas-texto-2">
                <tr><th class="py-2">Salida</th><th>Actual</th><th>Propuesto</th><th>Costo</th><th>Desglose</th><th class="text-right">Revisión</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($pendientes as $p)
                    <tr class="align-top">
                        <td class="py-3">
                            <span class="font-semibold">{{ $p->salida->tour->nom }}</span><br>
                            <span class="text-xs text-pipocas-texto-2">{{ $p->salida->fec_sal->format('d/m/Y') }} {{ substr($p->salida->hra_sal, 0, 5) }} · {{ $p->tip === 'guia' ? 'Guía' : 'Vehículo' }}</span>
                        </td>
                        <td class="py-3">{{ $p->recursoActual() ?? 'Sin asignar' }}</td>
                        <td class="py-3 font-semibold text-pipocas-principal">{{ $p->recursoPropuesto() }}</td>
                        <td class="py-3">{{ $fmt($p->cos) }}</td>
                        <td class="py-3 text-xs text-pipocas-texto-2">
                            @if ($p->tip === 'guia')
                                Carga {{ $fmt($p->det['carga'] ?? 0) }} · exp. {{ $p->det['experiencia'] ?? 0 }} · idiomas {{ $p->det['idiomas'] ?? 0 }}<br>
                                {{ $fmt($p->det['horas_semana'] ?? 0, 1) }} / {{ $p->det['limite'] ?? '—' }} h semana
                                @if (! empty($p->det['idiomas_faltantes'])) · falta: {{ implode(', ', $p->det['idiomas_faltantes']) }} @endif
                            @else
                                {{ $fmt($p->det['km_estimados'] ?? 0, 1) }} km × Bs {{ $fmt($p->det['costo_km'] ?? 0, 3) }} = {{ $fmt($p->det['combustible'] ?? 0) }}<br>
                                {{ $p->det['asientos_vacios'] ?? 0 }} asientos vacíos · {{ $p->det['fuente_costo'] ?? '' }}
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <div class="flex flex-col items-end gap-2">
                                <form method="POST" action="{{ route('admin.io.propuestas.aplicar', $p) }}" class="flex items-center gap-2">
                                    @csrf
                                    <select name="recurso" class="rounded-lg border-gray-300 py-1 text-xs" aria-label="Ajustar recurso">
                                        @if ($p->tip === 'guia')
                                            @foreach ($guias as $g)<option value="{{ $g->id }}" @selected($g->id === $p->rec_pro_id)>{{ $g->usuario->nom }}</option>@endforeach
                                        @else
                                            @foreach ($vehiculos as $v)<option value="{{ $v->id }}" @selected($v->id === $p->rec_pro_id)>{{ $v->pla }} ({{ $v->cap_asi }})</option>@endforeach
                                        @endif
                                    </select>
                                    <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white">Confirmar</button>
                                </form>
                                <form method="POST" action="{{ route('admin.io.propuestas.rechazar', $p) }}">
                                    @csrf
                                    <button class="text-xs font-bold text-red-600">Rechazar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-pipocas-texto-2">No hay propuestas pendientes. Ejecuta el modelo para generar nuevas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="rsv-tarjeta">
    <h2 class="rsv-titulo mb-3">Revisadas recientemente</h2>
    <ul class="divide-y divide-gray-100 text-sm">
        @forelse ($revisadas as $r)
            <li class="flex flex-wrap justify-between gap-2 py-2">
                <span>{{ $r->salida->tour->nom }} · {{ $r->salida->fec_sal->format('d/m') }} · {{ $r->tip }} → <b>{{ $r->recursoPropuesto() }}</b></span>
                <span class="text-pipocas-texto-2">{{ $r->est }} por {{ $r->revisor?->nom ?? '—' }} · {{ $r->rev_en?->format('d/m H:i') }}</span>
            </li>
        @empty
            <li class="py-2 text-pipocas-texto-2">Todavía no se revisaron propuestas.</li>
        @endforelse
    </ul>
</section>
@endsection
