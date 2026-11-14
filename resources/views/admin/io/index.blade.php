@extends('layouts.admin-io')
@section('titulo', 'Planificación inteligente')
@section('subtitulo', 'Menos cálculos, mejores decisiones para tu agencia. Elige qué necesitas resolver.')
@section('contenido')
    <section class="admin-insight">
        <span class="insight-icon"><i aria-hidden="true" class="fa-regular fa-lightbulb"></i></span>
        <div><span class="admin-eyebrow">TU ASISTENTE DE PLANIFICACIÓN</span>
            <h2>No necesitas saber de matemáticas para empezar.</h2>
            <p>Estas herramientas usan tus datos para comparar alternativas. Primero verás el resultado y los pasos
                sugeridos; si quieres profundizar, abre los detalles del cálculo. Tú decides qué cambios aplicar.</p>
        </div>
    </section>
    <div class="grid gap-5 md:grid-cols-2">
        @foreach ([
            ['admin.io.colas', 'fa-people-line', '01 / CAPACIDAD', '¿Necesito más guías?', 'Comprueba si la demanda podría superar la atención disponible. Prueba distintos números de guías y cupos antes de cambiar tu operación.', 'Analizar demanda y capacidad'],
            ['admin.io.propuestas', 'fa-route', '02 / ORGANIZACIÓN', '¿A quién asigno cada salida?', 'Recibe propuestas de guías y vehículos según horarios, experiencia, idiomas y capacidad. Revísalas, ajusta el recurso y confirma cuando estés listo.', 'Revisar asignaciones'],
            ['admin.io.eoq', 'fa-boxes-stacked', '03 / EQUIPAMIENTO', '¿Qué equipo debo revisar?', 'Identifica equipos con pocas unidades disponibles y consulta estimaciones de reposición. Verás cuándo faltan datos para hacer un análisis útil.', 'Planificar equipo'],
            ['admin.io.ciclo-vida', 'fa-heart', '04 / CLIENTES', '¿Mis clientes vuelven a viajar?', 'Entiende en qué etapa están tus clientes y cómo podrían evolucionar. Identifica oportunidades de seguimiento después de una reserva o un tour.', 'Explorar retención'],
        ] as [$ruta, $icono, $numero, $pregunta, $detalle, $accion])
            <a href="{{ route($ruta) }}" class="io-tool">
                <div class="flex items-center justify-between"><span class="admin-metric-icon !h-11 !w-11 !text-lg"><i aria-hidden="true" class="fa-solid {{ $icono }}"></i></span><span
                        class="admin-eyebrow !mb-0">{{ $numero }}</span></div>
                <h2>{{ $pregunta }}</h2>
                <p>{{ $detalle }}</p><span class="tool-link">{{ $accion }}<i aria-hidden="true" class="fa-solid fa-arrow-right"></i></span>
            </a>
        @endforeach
    </div>
    <section class="rsv-tarjeta">
        <div class="admin-section-head">
            <div>
                <h2>De los datos a la decisión, en tres pasos</h2>
                <p>Las estimaciones orientan tu planificación; las asignaciones necesitan tu confirmación.</p>
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            @foreach ([['Revisa la situación', 'Elige un tour o un equipo y comprueba si los datos son actuales o de demostración.'], ['Compara alternativas', 'Prueba escenarios y revisa el efecto estimado sobre capacidad, espera o inventario.'], ['Decide y da seguimiento', 'Valida la recomendación con tu operación real. Aplica asignaciones solo después de revisarlas.']] as $paso)
                <div class="admin-step"><span class="admin-step-number">{{ $loop->iteration }}</span>
                    <div>
                        <h3 class="text-xs font-bold">{{ $paso[0] }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500">{{ $paso[1] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <details class="admin-details">
        <summary>¿Qué hay detrás de estas herramientas?</summary>
        <div class="technical-grid">
            <div><strong>Teoría de colas</strong>
                <p>Estima esperas y saturación bajo ciertos supuestos de demanda y atención.</p>
            </div>
            <div><strong>EOQ: cantidad económica de pedido</strong>
                <p>Compara tamaño de pedido, demanda y costos de inventario. En equipo reutilizable necesita validar el
                    desgaste real.</p>
            </div>
            <div><strong>Cadenas de Markov</strong>
                <p>Estiman cambios entre etapas del cliente a partir de probabilidades mensuales; no predicen personas
                    concretas.</p>
            </div>
            <div><strong>Asignación de recursos</strong>
                <p>Compara combinaciones de guías y vehículos respetando las restricciones disponibles.</p>
            </div>
        </div>
    </details>
@endsection
