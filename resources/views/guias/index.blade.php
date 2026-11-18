@extends('layouts.campo')

@section('titulo', auth()->user()->tieneRol([\App\Models\User::ROL_GUIA, \App\Models\User::ROL_CHOFER]) ? 'Mis salidas' : 'Salidas en campo')
@section('volver', url('/'))

@section('contenido')
    <nav aria-label="Vista de salidas" class="flex flex-wrap gap-2">
        @foreach (['' => 'Próximas y en curso', 'pendientes' => 'Fechas pasadas'] as $vista => $texto)
            @php $actual = $pendientes === ($vista === 'pendientes'); @endphp
            <a href="{{ route('guias.checkin', array_filter(['vista' => $vista, 'tour' => $busqueda], fn ($valor) => filled($valor))) }}"
                @if ($actual) aria-current="page" @endif
                class="rounded-xl border px-4 py-2 text-sm font-semibold {{ $actual ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-700' }}">
                {{ $texto }}
            </a>
        @endforeach
    </nav>

    <form method="GET" action="{{ route('guias.checkin') }}" class="flex flex-wrap items-end gap-2">
        @if ($pendientes)<input type="hidden" name="vista" value="pendientes">@endif
        <label class="min-w-0 flex-1 text-sm font-semibold text-slate-700">Nombre del tour
            <input type="search" name="tour" value="{{ $busqueda }}" maxlength="120" class="mt-1 block w-full rounded-xl border-slate-300 bg-white text-sm">
        </label>
        <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Buscar</button>
        @if ($busqueda !== '')
            <a href="{{ route('guias.checkin', $pendientes ? ['vista' => 'pendientes'] : []) }}" class="px-2 py-2 text-sm font-semibold text-slate-700">Limpiar</a>
        @endif
    </form>
    @if ($pendientes)
        <p class="text-sm text-slate-600">Salidas cuya fecha ya pasó y que siguen programadas.</p>
    @endif

    <div class="space-y-3">
        @forelse ($salidas as $s)
            @php $enCurso = $s->est === \App\Models\SalidaTour::EST_EN_CURSO; @endphp
            <a href="{{ route('guias.salida', $s) }}" class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-amber-400">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-amber-800">
                            {{ $s->fec_sal->isToday() ? 'Hoy' : $s->fec_sal->locale('es')->translatedFormat('D j M Y') }} · {{ substr($s->hra_sal, 0, 5) }}
                        </p>
                        <h2 class="text-lg font-extrabold text-slate-900">{{ $s->tour->nom }}</h2>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $enCurso ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ $enCurso ? 'En curso' : 'Programada' }}</span>
                </div>
                <dl class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                    <div><dt class="text-xs text-slate-500">Guía</dt><dd>{{ $s->guia?->usuario?->nom ?? 'Sin asignar' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Chofer</dt><dd>{{ $s->chofer?->usuario?->nom ?? 'Sin asignar' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Vehículo</dt><dd>{{ $s->vehiculo?->pla ?? 'Sin asignar' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Pasajeros confirmados</dt><dd>{{ (int) $s->pasajeros }}</dd></div>
                </dl>
                <span class="mt-4 block text-sm font-semibold text-slate-700">Abrir salida <span aria-hidden="true">→</span></span>
            </a>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <p class="font-semibold">{{ match (true) {
                    $busqueda !== '' => 'No hay salidas para ese nombre de tour en esta vista.',
                    $pendientes => 'No hay salidas pendientes de fechas pasadas.',
                    default => 'No hay salidas próximas ni en curso.',
                } }}</p>
                @if ($busqueda === '' && ! $pendientes && auth()->user()->tieneRol([\App\Models\User::ROL_GUIA, \App\Models\User::ROL_CHOFER]))
                    <p class="mt-2 text-sm text-slate-600">Aquí aparecerán los tours cuando el equipo de operaciones te asigne a una salida.</p>
                @endif
            </div>
        @endforelse
    </div>
    {{ $salidas->links() }}
@endsection
