{{-- Ajustes guiados; las tasas técnicas se calculan en el servidor. --}}
@extends('layouts.admin-io')
@section('titulo', 'Ajustes del análisis')
@section('subtitulo', 'Cuéntanos cómo trabaja tu agencia. Nosotros hacemos los cálculos; no necesitas conocer teoría de colas.')
@section('contenido')
    @php
        $fmt = fn ($v, $d = 2) => number_format((float) $v, $d, ',', '.');
        $fila = $tours->first(fn ($fila) => $fila['tour']->id === $seleccionado);
    @endphp
    <div class="admin-insight">
        <span class="insight-icon"><i aria-hidden="true" class="fa-solid fa-wand-magic-sparkles"></i></span>
        <div>
            <h2>Solo necesitas conocer tu operación</h2>
            <p>Elige un tour, revisa su duración e indica con cuántos guías quieres hacer la estimación. Después revisa el resumen y guarda.</p>
            <p class="mt-2"><strong>¿Solo quieres probar qué pasaría con más guías?</strong> Usa <a class="underline font-bold" href="{{ route('admin.io.colas', ['tour' => $seleccionado]) }}">Demanda y capacidad</a>, sin modificar estos ajustes.</p>
        </div>
    </div>

    <section class="rsv-tarjeta" aria-labelledby="elegir-tour">
        <div class="admin-step mb-4"><span class="admin-step-number" aria-hidden="true">1</span><div><h2 id="elegir-tour" class="rsv-titulo">Elige el tour que quieres revisar</h2><p class="text-sm text-slate-500">Trabaja con un tour a la vez. Guarda o deshaz tus cambios antes de elegir otro.</p></div></div>
        <form method="GET" action="{{ route('admin.io.parametros') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-0 flex-1"><label class="rsv-etiqueta" for="tour-ajustes">Tour</label>
                <select id="tour-ajustes" name="tour" class="rsv-campo" required>
                    @foreach ($tours as $opcion)
                        <option value="{{ $opcion['tour']->id }}" @selected($seleccionado === $opcion['tour']->id)>{{ $opcion['tour']->nom }}{{ $opcion['par'] ? '' : ' · Sin configurar' }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rsv-btn-sec">Ver este tour</button>
        </form>
    </section>

    @if ($fila)
        @php
            $t = $fila['tour'];
            $p = $fila['par'];
            $horas = $p ? max(0.0001, round(24 / $p->mu, 2)) : max(0.0001, (float) $t->dur_hrs);
            $original = [
                'horas' => $horas, 'guias' => $p->c_srv ?? 1, 'capacidad' => $p->k_cap ?? max(1, min(200, $t->cap_max)),
                'demanda' => $p->lmb ?? round($fila['lambda_obs'], 4), 'espera' => $p->cos_esp ?? 15,
                'ocioso' => $p->cos_srv ?? 25, 'mu' => $p->mu ?? $fila['mu_sugerido'],
            ];
            $esReintento = (int) old('_tour') === $t->id;
            $valores = $esReintento ? [
                'horas' => old('tiempo_tour_horas', $horas), 'guias' => old('c_srv', $original['guias']),
                'capacidad' => old('k_cap', $original['capacidad']), 'demanda' => old('lmb', $original['demanda']),
                'espera' => old('cos_esp', $original['espera']), 'ocioso' => old('cos_srv', $original['ocioso']),
            ] : $original;
            $config = ['actual' => $original, 'valores' => $valores, 'duracion' => (float) $t->dur_hrs,
                'observado' => round($fila['lambda_obs'], 4), 'configurado' => (bool) $p,
                'motivo' => $esReintento ? old('obs', '') : ''];
        @endphp
        <form id="ajustes-tour" method="POST" action="{{ route('admin.io.parametros.store', $t) }}"
              x-data="ioAjustes(@js($config))" @submit="enviar($event)" @input="revisado = false"
              @invalid.capture="$event.target.closest('details')?.setAttribute('open', '')"
              class="grid min-w-0 gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(280px,1fr)]">
            @csrf
            <input type="hidden" name="_tour" value="{{ $t->id }}">
            <input type="hidden" name="modo" value="guiado">
            <div class="min-w-0 space-y-6">
                <section class="rsv-tarjeta space-y-5" aria-labelledby="datos-operacion">
                    <div class="admin-step"><span class="admin-step-number" aria-hidden="true">2</span><div><h2 id="datos-operacion" class="rsv-titulo">Así se realiza este tour</h2><p class="text-sm font-semibold text-pipocas-principal mt-1">{{ $t->nom }}</p></div></div>
                    <div class="rounded-xl bg-slate-50 p-4 text-sm">
                        @if ($p)<p class="font-semibold">Último ajuste: {{ $p->fec_cal->format('d/m/Y') }}</p>@else<p class="font-semibold">Primera configuración</p>@endif
                        <p class="mt-1">Duración publicada: <strong>{{ $fmt($t->dur_hrs, 1) }} h</strong> · Cupo publicado: <strong>{{ $t->cap_max }} personas</strong>.</p>
                    </div>
                    <div>
                        <label class="rsv-etiqueta" for="tiempo-tour">¿Cuántas horas dedica un guía a realizar un tour?</label>
                        <div class="flex items-center gap-3"><input id="tiempo-tour" name="tiempo_tour_horas" type="number" step="any" min="0.0001" max="240000" required class="rsv-campo" x-model="horas" value="{{ $valores['horas'] }}" aria-describedby="ayuda-tiempo"><span class="text-sm">horas</span></div>
                        <p id="ayuda-tiempo" class="mt-2 text-xs text-slate-500">Ejemplo: escribe 7 si el recorrido ocupa 7 horas, o 7.5 si ocupa 7 horas y media. Incluye preparación y traslado si forman parte del tiempo del guía.</p>
                        <button type="button" @click="usarDuracion()" class="mt-2 text-xs font-semibold text-pipocas-principal underline">Usar la duración publicada: {{ $fmt($t->dur_hrs, 1) }} h</button>
                        @error('tiempo_tour_horas')<p class="rsv-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="rsv-etiqueta" for="guias-tour">¿Con cuántos guías quieres hacer la estimación?</label>
                        <input id="guias-tour" name="c_srv" type="number" min="1" max="60" step="1" required class="rsv-campo" x-model="guias" value="{{ $valores['guias'] }}" aria-describedby="ayuda-guias">
                        <p id="ayuda-guias" class="mt-2 text-xs text-slate-500">Ejemplo: escribe 2 para estimar qué ocurre con dos guías. Esto no asigna personas ni comprueba sus horarios; las asignaciones se hacen en <a class="underline" href="{{ route('admin.io.propuestas') }}">Asignar recursos</a>.</p>
                        @error('c_srv')<p class="rsv-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="admin-notice"><i aria-hidden="true" class="fa-solid fa-chart-line"></i><div><strong>Las solicitudes se cuentan automáticamente</strong><p>{{ (int) round($fila['lambda_obs'] * 30) }} solicitudes registradas en los últimos 30 días: {{ $fmt($fila['lambda_obs'], 3) }} por día en promedio. Se cuentan solicitudes, no pasajeros.</p>@if ($fila['lambda_obs'] == 0)<p>No hay solicitudes registradas en ese periodo. El análisis mostrará demanda cero; no necesitas inventar una cantidad.</p>@endif</div></div>
                </section>

                <details class="admin-details" @if ($esReintento && ($errors->has('k_cap') || $errors->has('lmb') || $errors->has('cos_esp') || $errors->has('cos_srv'))) open @endif>
                    <summary>Opcional: límites y costos de la estimación</summary>
                    <p class="mb-5 text-sm text-slate-500">Puedes conservar los valores actuales. Cambia esta sección solo si tienes una razón y datos para respaldarla.</p>
                    <div class="space-y-5">
                        <div><label class="rsv-etiqueta" for="limite-tour">¿Cuántas solicitudes como máximo debe considerar la estimación?</label><input id="limite-tour" name="k_cap" type="number" step="1" min="1" :min="guias" max="200" required class="rsv-campo" x-model="capacidad" value="{{ $valores['capacidad'] }}" aria-describedby="ayuda-limite"><p id="ayuda-limite" class="mt-2 text-xs text-slate-500">Incluye solicitudes en atención y en espera. Debe ser igual o mayor que la cantidad de guías. Es un supuesto del análisis: no modifica los cupos de una salida. Una solicitud puede incluir varios pasajeros.</p>@error('k_cap')<p class="rsv-error">{{ $message }}</p>@enderror</div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label class="rsv-etiqueta" for="costo-espera">Pérdida estimada por una solicitud en espera (Bs por hora)</label><input id="costo-espera" name="cos_esp" type="number" step="0.01" min="0" max="999999" required class="rsv-campo" x-model="espera" value="{{ $valores['espera'] }}" aria-describedby="ayuda-espera"><p id="ayuda-espera" class="mt-2 text-xs text-slate-500">Ejemplo: 15 representa Bs 15 por cada hora de espera de una solicitud. Es una estimación para comparar opciones, no un cobro al turista.</p></div>
                            <div><label class="rsv-etiqueta" for="costo-guia">Costo de un guía sin actividad (Bs por hora)</label><input id="costo-guia" name="cos_srv" type="number" step="0.01" min="0" max="999999" required class="rsv-campo" x-model="ocioso" value="{{ $valores['ocioso'] }}" aria-describedby="ayuda-ocioso"><p id="ayuda-ocioso" class="mt-2 text-xs text-slate-500">Ejemplo: si pagas Bs 200 por 8 horas, usa 25. No se cambia la remuneración del guía.</p></div>
                        </div>
                        @if (! $p)<p class="text-xs text-amber-800">Los costos iniciales de Bs 15 y Bs 25 son referencias del sistema. Confírmalos con la agencia antes de usar las comparaciones económicas para decidir.</p>@endif
                        <div><label class="rsv-etiqueta" for="demanda-tour">Solicitudes diarias de referencia</label><input id="demanda-tour" name="lmb" type="number" step="0.0001" min="0" max="9999" required class="rsv-campo" x-model="demanda" value="{{ $valores['demanda'] }}" aria-describedby="ayuda-demanda"><p id="ayuda-demanda" class="mt-2 text-xs text-slate-500">Solo se conserva como referencia. El panel Demanda y capacidad usa las solicitudes registradas de los últimos 30 días, incluso si son cero. Para probar una demanda distinta, usa la simulación de ese panel.</p><button type="button" @click="demanda = config.observado; revisado = false" class="mt-2 text-xs font-semibold text-pipocas-principal underline">Copiar el promedio registrado: {{ $fmt($fila['lambda_obs'], 3) }}</button></div>
                    </div>
                </details>
                <div class="rsv-tarjeta"><label class="rsv-etiqueta" for="motivo-tour">¿Por qué haces este cambio? <span class="font-normal">(opcional)</span></label><textarea id="motivo-tour" name="obs" maxlength="500" rows="2" class="rsv-campo" x-model="motivo" placeholder="Ej.: ahora contamos con dos guías para este tour.">{{ $esReintento ? old('obs') : '' }}</textarea><p class="mt-2 text-xs text-slate-500">El motivo quedará junto a los valores guardados en el historial.</p></div>
            </div>

            <section class="rsv-tarjeta min-w-0 self-start space-y-5 xl:sticky xl:top-6" aria-labelledby="resumen-ajustes">
                <div class="admin-step"><span class="admin-step-number" aria-hidden="true">3</span><div><h2 id="resumen-ajustes" class="rsv-titulo">Revisa antes de guardar</h2><p class="text-sm text-slate-500 mt-1">{{ $t->nom }}</p></div></div>
                <dl class="space-y-3 text-sm">
                    <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3"><dt>Tiempo por tour</dt><dd class="font-bold" x-text="numero(horas) + ' h'">{{ $fmt($valores['horas']) }} h</dd></div>
                    <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3"><dt>Guías para estimar</dt><dd class="font-bold" x-text="numero(guias, 0)">{{ $valores['guias'] }}</dd></div>
                    <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3"><dt>Límite de solicitudes</dt><dd class="font-bold" x-text="numero(capacidad, 0)">{{ $valores['capacidad'] }}</dd></div>
                    <div class="flex flex-wrap justify-between gap-2"><dt>Demanda del panel</dt><dd class="font-bold">{{ $fmt($fila['lambda_obs'], 3) }} solicitudes/día</dd></div>
                </dl>
                <div class="rounded-xl bg-slate-50 p-3 text-xs"><strong>Efecto de tus ajustes</strong><p class="mt-1">Más guías permiten comparar una atención con más recursos; más horas por tour representan una atención más lenta. El resultado depende también de la demanda y del límite de solicitudes.</p></div>
                <div x-cloak x-show="cambios.length" class="text-xs"><strong>Lo que cambiará</strong><ul class="mt-2 space-y-2"><template x-for="cambio in cambios" :key="cambio"><li x-text="cambio"></li></template></ul></div>
                <p x-cloak x-show="error" x-text="error" class="rounded-xl bg-red-50 p-3 text-sm text-red-800" role="alert"></p>
                <div class="admin-notice admin-notice-amber"><div><strong>Solo cambia la estimación</strong><p>No se asignan guías, no se modifican reservas ni se aumentan cupos reales. La configuración anterior queda en el historial.</p></div></div>
                <button type="button" @click="restablecer()" class="text-xs font-semibold underline text-pipocas-principal">{{ $p ? 'Deshacer mis cambios' : 'Volver a los valores iniciales' }}</button>
                <label class="flex items-start gap-2 text-xs"><input type="checkbox" class="rsv-check shrink-0" x-model="revisado" required> <span>Revisé estos datos y quiero usarlos en las próximas estimaciones.</span></label>
                <button type="submit" class="rsv-btn w-full" :disabled="!!error || !revisado"><i aria-hidden="true" class="fa-solid fa-floppy-disk"></i> Guardar ajustes de este tour</button>
                <noscript><p class="text-xs">Puedes guardar el formulario. El resumen dinámico y los botones para copiar valores requieren JavaScript.</p></noscript>
                <a class="block text-center text-xs font-semibold text-pipocas-principal underline" href="{{ route('admin.io.colas', ['tour' => $t->id]) }}">Ver el análisis de este tour</a>
            </section>
        </form>

        <details class="admin-details">
            <summary>Cómo se transforma el tiempo en el cálculo (detalle técnico)</summary>
            <p class="text-sm text-slate-500">El modelo expresa la atención en tours por guía y por día: 24 ÷ horas por tour. Usa una escala continua de 24 horas para comparar tasas; no representa una jornada laboral ni autoriza a programar ese número de tours. Un tiempo guardado puede mostrarse redondeado sin alterar su tasa original.</p>
        </details>
    @else
        <p class="admin-empty">No hay tours activos. Registra o activa un tour en Tours y rutas para configurar su análisis.</p>
    @endif

    <details class="admin-details">
        <summary>Historial de cambios · últimas 15 configuraciones</summary>
        <p class="mb-4 text-sm text-slate-500">Consulta qué se guardó y por qué. “Anterior” significa que hay una configuración más reciente para ese tour.</p>
        <div class="overflow-x-auto"><table class="w-full min-w-[760px] text-left text-sm"><caption class="sr-only">Historial de ajustes del análisis</caption>
            <thead><tr><th>Fecha</th><th>Tour</th><th>Tiempo por tour</th><th>Guías</th><th>Límite de solicitudes</th><th>Motivo</th><th>Estado</th></tr></thead>
            <tbody>@forelse ($historial as $h)<tr><td>{{ $h->cre_en?->format('d/m/Y H:i') }}</td><td>{{ $h->tour?->nom }}</td><td>{{ $fmt(24 / $h->mu) }} h</td><td>{{ $h->c_srv }}</td><td>{{ $h->k_cap }}</td><td class="max-w-xs break-words">{{ $h->obs ?: 'Sin motivo registrado' }}</td><td>{{ $h->act ? 'En uso' : 'Anterior' }}</td></tr>@empty<tr><td colspan="7">Todavía no se guardaron configuraciones.</td></tr>@endforelse</tbody>
        </table></div>
    </details>
@endsection
@push('scripts')
    <script src="{{ asset('js/io-ajustes.js') }}"></script>
@endpush
