@extends('layouts.admin-io')
@section('titulo', 'Asignar recursos')
@section('subtitulo', 'Organiza guías y vehículos para tus salidas. Cada propuesta se aplica solo cuando la confirmas.')
@section('contenido')
    @php $fmt = fn ($v, $d = 1) => number_format((float) $v, $d, ',', '.'); @endphp
    <div class="grid gap-4 sm:grid-cols-3">
        @foreach ([['Pendientes de revisión', $pendientes->count(), 'Propuestas para próximas salidas', 'fa-clipboard-check'], ['Guías activos', $guias->count(), 'Su disponibilidad se valida al asignar', 'fa-user-group'], ['Vehículos activos', $vehiculos->count(), 'Se comprueban capacidad y horarios', 'fa-van-shuttle']] as [$titulo, $valor, $nota, $icono])
            <div class="admin-metric">
                <div class="flex items-center justify-between"><span
                        class="admin-metric-label">{{ $titulo }}</span><span class="admin-metric-icon"><i aria-hidden="true" class="fa-solid {{ $icono }}"></i></span></div>
                <p class="admin-metric-value">{{ $valor }}</p>
                <p class="admin-metric-note">{{ $nota }}</p>
            </div>
        @endforeach
    </div>
    <section class="rsv-tarjeta">
        <div class="admin-section-head">
            <div>
                <h2>1. Elige qué necesitas organizar</h2>
                <p>Genera sugerencias para un período. Después podrás revisarlas una por una.</p>
            </div><span class="admin-pill">Sin aplicación automática</span>
        </div>
        <form method="POST" action="{{ route('admin.io.propuestas.generar') }}"
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @csrf
            <label class="rsv-etiqueta">Recurso a asignar<select name="tip" class="rsv-campo mt-2">
                    <option value="guia" @selected(old('tip', $tip) !== 'vehiculo')>Guías</option>
                    <option value="vehiculo" @selected(old('tip', $tip) === 'vehiculo')>Vehículos</option>
                </select></label>
            <label class="rsv-etiqueta">Salidas desde<input type="date" name="desde" required
                    value="{{ old('desde', today()->toDateString()) }}" min="{{ today()->toDateString() }}"
                    class="rsv-campo mt-2"></label>
            <label class="rsv-etiqueta">Hasta<input type="date" name="hasta" required
                    value="{{ old('hasta', today()->addDays(14)->toDateString()) }}" class="rsv-campo mt-2"></label>
            <div class="flex items-center"><button class="rsv-btn w-full"><i aria-hidden="true" class="fa-solid fa-wand-magic-sparkles"></i>
                    Generar propuestas</button></div>
            <label class="flex items-center gap-2 text-xs text-slate-600 sm:col-span-2 xl:col-span-4"><input type="hidden"
                    name="solo_sin_asignar" value="0"><input type="checkbox" name="solo_sin_asignar" value="1"
                    @checked(old('solo_sin_asignar', '1') == '1') class="rsv-check"> Solo salidas que aún no tienen este recurso
                asignado</label>
        </form>
    </section>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-extrabold">2. Revisa y confirma las propuestas</h2>
            <p class="mt-1 text-xs text-slate-500">Puedes elegir otro recurso. El sistema vuelve a comprobar las
                restricciones al confirmar.</p>
        </div>
        <nav class="flex gap-1 rounded-xl border border-slate-200 bg-white p-1" aria-label="Filtrar propuestas">
            @foreach (['' => 'Todas', 'guia' => 'Guías', 'vehiculo' => 'Vehículos'] as $valor => $label)
                <a href="{{ route('admin.io.propuestas', $valor ? ['tip' => $valor] : []) }}"
                    class="rounded-lg px-3 py-2 text-xs font-semibold {{ ($tip ?? '') === $valor ? 'bg-pipocas-principal text-white' : 'text-slate-500' }}">{{ $label }}</a>
            @endforeach
        </nav>
    </div>
    <div class="space-y-4">
        @forelse ($pendientes as $p)
            <article class="rsv-tarjeta">
                <div class="admin-section-head">
                    <div><span class="admin-pill mb-2"><i aria-hidden="true" class="fa-solid {{ $p->tip === 'guia' ? 'fa-user-group' : 'fa-van-shuttle' }}"></i>{{ $p->tip === 'guia' ? 'Guía' : 'Vehículo' }}</span>
                        <h2>{{ $p->salida->tour->nom }}</h2>
                        <p><i aria-hidden="true" class="fa-regular fa-calendar mr-1"></i> {{ $p->salida->fec_sal->format('d/m/Y') }} ·
                            {{ substr($p->salida->hra_sal, 0, 5) }}</p>
                    </div><span class="admin-pill !bg-amber-50 !text-amber-800">Por revisar</span>
                </div>
                <div class="grid gap-4 lg:grid-cols-3">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                        <p class="admin-metric-note">Asignación actual</p>
                        <p class="mt-2 text-sm font-bold">{{ $p->recursoActual() ?? 'Sin asignar' }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-blue-50/40 p-4">
                        <p class="admin-metric-note">Recurso propuesto</p>
                        <p class="mt-2 text-sm font-bold text-pipocas-principal">{{ $p->recursoPropuesto() }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 p-4">
                        <p class="admin-metric-note">Qué se tomó en cuenta</p>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600">
                            @if ($p->tip === 'guia')
                                Horarios, carga semanal, experiencia e idiomas. @if (!empty($p->det['idiomas_faltantes']))
                                    <span class="mt-1 block font-semibold text-amber-700">Idiomas faltantes:
                                        {{ implode(', ', $p->det['idiomas_faltantes']) }}. Revisa antes de
                                        confirmar.</span>
                                @endif
                            @else
                                Asientos disponibles, horarios y costo estimado de combustible.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap items-end justify-between gap-4 border-t border-slate-100 pt-5">
                    <details class="text-xs text-slate-500">
                        <summary class="cursor-pointer font-semibold text-pipocas-principal">Ver criterios de la propuesta
                        </summary>
                        <div class="mt-3 max-w-lg space-y-2">
                            <p>Puntaje del modelo: <strong>{{ $fmt($p->cos, 2) }}</strong>. Menor es preferible entre
                                alternativas factibles; no es el precio a pagar.</p>
                            @if ($p->tip === 'guia')
                                <p>Carga semanal: {{ $fmt($p->det['horas_semana'] ?? 0) }} de
                                    {{ $p->det['limite'] ?? '—' }} horas. Penalización por carga:
                                    {{ $fmt($p->det['carga'] ?? 0) }}; experiencia: {{ $p->det['experiencia'] ?? 0 }};
                                idiomas: {{ $p->det['idiomas'] ?? 0 }}.</p>@else<p>Distancia estimada:
                                    {{ $fmt($p->det['km_estimados'] ?? 0) }} km. Combustible estimado: Bs
                                    {{ $fmt($p->det['combustible'] ?? 0, 2) }}. Asientos vacíos:
                                    {{ $p->det['asientos_vacios'] ?? 0 }}.</p>
                            @endif
                        </div>
                    </details>
                    <form id="asignar-{{ $p->id }}" method="POST"
                        action="{{ route('admin.io.propuestas.aplicar', $p) }}" class="flex flex-wrap items-end gap-3">
                        @csrf<label class="rsv-etiqueta !mb-0">Recurso que vas a confirmar<select name="recurso"
                                class="rsv-campo mt-2" required>
                                @if ($p->tip === 'guia')
                                    @foreach ($guias as $g)
                                        <option value="{{ $g->id }}" @selected($g->id === $p->rec_pro_id)>
                                            {{ $g->usuario->nom }}</option>
                                    @endforeach
                                @else
                                    @foreach ($vehiculos as $v)
                                        <option value="{{ $v->id }}" @selected($v->id === $p->rec_pro_id)>
                                            {{ $v->pla }} · {{ $v->cap_asi }} asientos</option>
                                    @endforeach
                                @endif
                            </select>
                        </label><button type="button"
                            onclick="document.getElementById('confirmar-{{ $p->id }}').showModal()"
                            class="rsv-btn"><i aria-hidden="true" class="fa-solid fa-check"></i> Confirmar asignación</button></form>
                </div>
                <form method="POST" action="{{ route('admin.io.propuestas.rechazar', $p) }}" class="mt-3 text-right">
                    @csrf<button
                        class="text-[11px] font-semibold text-slate-500 underline underline-offset-4 hover:text-red-700">Rechazar
                        esta propuesta</button></form>
                <dialog id="confirmar-{{ $p->id }}" class="admin-dialog"
                    aria-labelledby="titulo-confirmar-{{ $p->id }}">
                    <div class="p-6"><span class="admin-metric-icon mb-4"><i aria-hidden="true" class="fa-solid fa-clipboard-check"></i></span>
                        <h2 id="titulo-confirmar-{{ $p->id }}" class="text-lg font-extrabold">¿Confirmar la
                            asignación?</h2>
                        <p class="mt-3 text-xs leading-relaxed text-slate-500">Se asignará el recurso seleccionado a
                            {{ $p->salida->tour->nom }} del {{ $p->salida->fec_sal->format('d/m/Y') }}. Si cambió su
                            disponibilidad, se mostrará el motivo y no se aplicará la propuesta.</p>
                        <div class="mt-6 flex justify-end gap-3"><button type="button"
                                onclick="this.closest('dialog').close()" class="rsv-btn-sec">Volver a
                                revisar</button><button type="submit" form="asignar-{{ $p->id }}"
                                class="rsv-btn">Sí, confirmar</button></div>
                    </div>
                </dialog>
            </article>
        @empty<div class="admin-empty"><i aria-hidden="true" class="fa-solid fa-clipboard-check"></i><strong class="text-slate-600">No
                    hay propuestas pendientes en este grupo</strong>
                <p class="mx-auto mt-2 max-w-md">Elige un recurso y un período para generar sugerencias. Si no hay
                    combinaciones viables, revisa personal, horarios y vehículos disponibles.</p>
            </div>
        @endforelse
    </div>
    <details class="admin-details">
        <summary>Asignaciones revisadas recientemente ({{ $revisadas->count() }})</summary>
        <p class="mb-2 text-[10px] text-slate-500 sm:hidden">Desliza la tabla para ver todas las columnas →</p>
        <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Tabla de resultados">
            <table class="w-full min-w-[600px] text-left">
                <thead>
                    <tr>
                        <th>Salida</th>
                        <th>Recurso</th>
                        <th>Resultado</th>
                        <th>Revisó</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($revisadas as $r)
                        <tr>
                            <td class="font-bold">{{ $r->salida->tour->nom }}<small
                                    class="block font-normal text-slate-500">{{ $r->salida->fec_sal->format('d/m/Y') }}</small>
                            </td>
                            <td>{{ $r->recursoPropuesto() }}</td>
                            <td><span class="admin-pill">{{ ucfirst($r->est) }}</span></td>
                            <td>{{ $r->revisor?->nom ?? '—' }}<small
                                    class="block text-slate-500">{{ $r->rev_en?->format('d/m H:i') }}</small></td>
                    </tr>@empty<tr>
                            <td colspan="4">Todavía no hay propuestas revisadas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>
    <details class="admin-details">
        <summary>Cómo se eligen los recursos</summary>
        <div class="technical-grid">
            <div><strong>Guías</strong>
                <p>El método húngaro compara combinaciones según carga, experiencia e idiomas. Excluye choques de horario y
                    límites superados.</p>
            </div>
            <div><strong>Vehículos</strong>
                <p>Compara combustible estimado y asientos vacíos. La confirmación valida capacidad, estado y compatibilidad
                    con la salida.</p>
            </div>
            <div><strong>Tu revisión es necesaria</strong>
                <p>Un puntaje favorable no sustituye tu criterio ni garantiza conocer todas las condiciones reales del
                    viaje.</p>
            </div>
        </div>
    </details>
@endsection
