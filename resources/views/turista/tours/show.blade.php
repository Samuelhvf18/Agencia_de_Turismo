@extends('layouts.turista')
@section('title', $tour->nom)
@section('topbar-title', 'Detalle del tour')
@section('content')
<a href="{{ route('turista.tours.index') }}"
   class="mb-5 inline-flex items-center gap-2 text-xs font-bold text-pipocas-blue hover:text-pipocas-terra">
    <i class="fa-solid fa-arrow-left"></i>
    Volver al catálogo
</a>
<div class="grid gap-6 xl:grid-cols-[1.35fr_.85fr]">
    <div class="space-y-6">
        <section class="tourist-card overflow-hidden">
            <div class="grid gap-1 sm:grid-cols-2">
                @forelse($tour->fotos->take(2) as $foto)
                    <img class="h-56 w-full object-cover {{ $loop->first && $tour->fotos->count() === 1 ? 'sm:col-span-2' : '' }}"
                        src="{{ filter_var($foto->rta_arc, FILTER_VALIDATE_URL) ? $foto->rta_arc : asset('storage/' . $foto->rta_arc) }}"
                        alt="{{ $foto->txt_atv ?? $tour->nom }}">
                @empty
                    <div class="grid h-64 place-items-center bg-pipocas-background text-6xl text-pipocas-blue/40 sm:col-span-2">
                        <i class="fa-solid fa-mountain-sun"></i>
                    </div>
                @endforelse
            </div>
            <div class="p-6 sm:p-8">
                <p class="text-xs font-bold uppercase tracking-wider text-pipocas-terra">
                    {{ $tour->categoria->nom ?? 'Experiencia' }}
                    · {{ ucfirst($tour->niv_dif) }}
                </p>
                <h1 class="page-title mt-2 text-3xl font-semibold sm:text-4xl">
                    {{ $tour->nom }}
                </h1>
                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-pipocas-muted">
                    {{ $tour->des }}
                </p>
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-xl bg-pipocas-background p-3">
                        <i class="fa-regular fa-clock text-pipocas-blue"></i>
                        <p class="mt-2 text-xs font-extrabold">{{ $tour->dur_hrs }} h</p>
                        <p class="text-[10px] text-pipocas-muted">Duración</p>
                    </div>
                    <div class="rounded-xl bg-pipocas-background p-3">
                        <i class="fa-solid fa-mountain text-pipocas-blue"></i>
                        <p class="mt-2 text-xs font-extrabold">{{ $tour->alt_max_m ?? '—' }} m</p>
                        <p class="text-[10px] text-pipocas-muted">Altitud máxima</p>
                    </div>
                    <div class="rounded-xl bg-pipocas-background p-3">
                        <i class="fa-solid fa-person-hiking text-pipocas-blue"></i>
                        <p class="mt-2 text-xs font-extrabold">{{ ucfirst($tour->niv_dif) }}</p>
                        <p class="text-[10px] text-pipocas-muted">Dificultad</p>
                    </div>
                    <div class="rounded-xl bg-pipocas-background p-3">
                        <i class="fa-solid fa-tag text-pipocas-blue"></i>
                        <p class="mt-2 text-xs font-extrabold">
                            Bs {{ number_format((float) $tour->pre_bas, 2) }}
                        </p>
                        <p class="text-[10px] text-pipocas-muted">Por persona</p>
                    </div>
                </div>
                @if($tour->req_fis)
                    <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-extrabold text-amber-800">
                            <i class="fa-solid fa-heart-pulse mr-2"></i>
                            Requisitos físicos
                        </p>
                        <p class="mt-1 text-xs leading-5 text-amber-900">
                            {{ $tour->req_fis }}
                        </p>
                    </div>
                @endif
            </div>
        </section>
        <section class="tourist-card p-6 sm:p-7">
            <div class="mb-3 flex items-center gap-3">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-pipocas-blue/10 text-pipocas-blue">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <h2 class="page-title text-xl font-semibold">
                        Georreferenciación de la ruta
                    </h2>
                    <p class="text-[11px] text-pipocas-muted">
                        Espacio reservado para el mapa del recorrido
                    </p>
                </div>
            </div>
            <div class="grid min-h-48 place-items-center rounded-2xl border-2 border-dashed border-pipocas-blue/20 bg-pipocas-background/70 p-6 text-center">
                <div>
                    <i class="fa-solid fa-map text-3xl text-pipocas-blue/45"></i>
                    <p class="mt-3 text-sm font-bold text-pipocas-text">
                        Mapa interactivo próximamente
                    </p>
                    <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-pipocas-muted">
                        Aquí se visualizarán el trazado geográfico y los puntos de interés del tour mediante los datos de ruta registrados en el sistema.
                    </p>
                </div>
            </div>
        </section>
    </div>
    <aside class="space-y-5">
        <section class="tourist-card p-6">
            <div class="mb-4">
                <p class="text-xs font-bold uppercase tracking-wider text-pipocas-terra">
                    Programa tu experiencia
                </p>
                <h2 class="page-title mt-1 text-2xl font-semibold">
                    Solicitar reserva
                </h2>
                <p class="mt-2 text-xs leading-5 text-pipocas-muted">
                    Selecciona una salida y registra a las personas que participarán.
                    Tu solicitud quedará pendiente de confirmación.
                </p>
            </div>
            @if($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4">
                    <p class="mb-2 text-xs font-extrabold text-red-800">
                        No se pudo enviar la solicitud:
                    </p>
                    <ul class="list-inside list-disc space-y-1 text-xs text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($tour->salidas->where('cupos_disponibles', '>', 0)->isNotEmpty())
                <form
                    method="POST"
                    action="{{ route('turista.reservas.solicitar') }}"
                    id="booking-form"
                    class="space-y-4">
                    @csrf
                    <div>
                        <label for="departure-select" class="mb-1.5 block text-xs font-bold">
                            Salida disponible
                        </label>
                        <select
                            name="sal_tur_id"
                            id="departure-select"
                            class="field"
                            required>
                            <option value="">Selecciona una fecha</option>
                            @foreach($tour->salidas as $salida)
                                @if($salida->cupos_disponibles > 0)
                                    <option
                                        value="{{ $salida->id }}"
                                        data-cupos="{{ $salida->cupos_disponibles }}"
                                        data-precio="{{ (float) $tour->pre_bas }}"
                                        @selected(old('sal_tur_id') == $salida->id)>
                                        {{ \Carbon\Carbon::parse($salida->fec_sal)->format('d/m/Y') }}
                                         {{ substr($salida->hra_sal, 0, 5) }}
                                         {{ $salida->cupos_disponibles }} cupos
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('sal_tur_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold">
                                Nombre de contacto
                            </label>
                            <input
                                class="field"
                                name="nom_ctc"
                                value="{{ old('nom_ctc', auth()->user()->nom) }}"
                                maxlength="150"
                                required>
                            @error('nom_ctc')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold">
                                Correo de contacto
                            </label>
                            <input
                                class="field"
                                type="email"
                                name="cor_ctc"
                                value="{{ old('cor_ctc', auth()->user()->cor) }}"
                                maxlength="150"
                                required>
                            @error('cor_ctc')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2 xl:col-span-1">
                            <label class="mb-1.5 block text-xs font-bold">
                                Teléfono de contacto
                            </label>
                            <input
                                class="field"
                                name="tel_ctc"
                                value="{{ old('tel_ctc', auth()->user()->tel) }}"
                                maxlength="20"
                                required>
                            @error('tel_ctc')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label for="participant-count" class="text-xs font-bold">
                                Participantes
                            </label>
                            <select
                                id="participant-count"
                                class="field !w-20 !py-2"
                                aria-label="Cantidad de participantes"
                                disabled></select>
                        </div>
                        <div id="participant-fields" class="space-y-3"></div>
                        @error('participantes')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="rounded-xl bg-pipocas-background p-4">
                        <div class="flex items-center justify-between text-xs text-pipocas-muted">
                            <span>Precio por persona</span>
                            <span id="unit-price">
                                Bs {{ number_format((float) $tour->pre_bas, 2) }}
                            </span>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-xs font-bold">Total estimado</span>
                            <strong id="estimated-total" class="text-lg text-pipocas-blue">
                                Bs 0.00
                            </strong>
                        </div>
                        <p class="mt-2 text-[10px] leading-4 text-pipocas-muted">
                            El importe definitivo se calcula al registrar la solicitud.
                            La solicitud no confirma todavía tu participación.
                        </p>
                    </div>
                    <button class="btn-primary w-full" type="submit">
                        <i class="fa-solid fa-paper-plane mr-2"></i>
                        Enviar solicitud
                    </button>
                </form>
            @else
                <div class="rounded-xl bg-amber-50 p-4 text-xs leading-5 text-amber-800">
                    <i class="fa-solid fa-calendar-xmark mr-2"></i>
                    No hay salidas futuras con cupos disponibles para este tour en este momento.
                </div>
            @endif
        </section>
        <section class="tourist-card p-5">
            <h3 class="text-sm font-extrabold">Salidas programadas</h3>
            <div class="mt-3 space-y-3">
                @forelse($tour->salidas as $salida)
                    <div class="flex items-center justify-between border-t border-slate-100 pt-3 text-xs">
                        <div>
                            <p class="font-bold">
                                {{ \Carbon\Carbon::parse($salida->fec_sal)->format('d/m/Y') }}
                            </p>
                            <p class="mt-1 text-pipocas-muted">
                                {{ substr($salida->hra_sal, 0, 5) }}
                            </p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-[10px] font-bold {{ $salida->cupos_disponibles > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $salida->cupos_disponibles }} cupos
                        </span>
                    </div>
                @empty
                    <p class="border-t border-slate-100 pt-3 text-xs text-pipocas-muted">
                        No hay salidas programadas.
                    </p>
                @endforelse
            </div>
        </section>
    </aside>
</div>
@endsection
@push('scripts')
<script>
(() => {
    const form = document.getElementById('booking-form');
    if (!form) return;
    const departureSelect = document.getElementById('departure-select');
    const participantSelect = document.getElementById('participant-count');
    const participantContainer = document.getElementById('participant-fields');
    const totalElement = document.getElementById('estimated-total');
    const unitPriceElement = document.getElementById('unit-price');
    const previousParticipants = @json(old('participantes', [['nom_cpl' => '']]));
    const previousCount = Array.isArray(previousParticipants)
        ? previousParticipants.length
        : 1;
    const oldValues = Array.isArray(previousParticipants)
        ? previousParticipants
        : [];
    let currentMax = 0;
    let currentPrice = 0;
    function formatPrice(amount) {
        return 'Bs ' + amount.toLocaleString('es-BO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    function updateTotal() {
        const count = parseInt(participantSelect.value || '0', 10);
        totalElement.textContent = formatPrice(currentPrice * count);
    }
    function renderParticipants() {
        const count = parseInt(participantSelect.value || '0', 10);
        participantContainer.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const wrapper = document.createElement('div');
            wrapper.className = 'rounded-xl border border-slate-200 p-3';
            const label = document.createElement('label');
            label.className = 'mb-1.5 block text-[11px] font-bold';
            label.textContent = 'Participante ' + (i + 1);
            const input = document.createElement('input');
            input.className = 'field';
            input.name = `participantes[${i}][nom_cpl]`;
            input.maxLength = 150;
            input.required = true;
            input.placeholder = 'Nombre completo';
            input.value = (oldValues[i] && oldValues[i].nom_cpl) || '';
            wrapper.append(label, input);
            participantContainer.append(wrapper);
        }
        updateTotal();
    }
    function updateParticipantOptions() {
        const selectedOption = departureSelect.selectedOptions[0];
        participantSelect.innerHTML = '';
        participantContainer.innerHTML = '';
        if (!selectedOption || !selectedOption.value) {
            currentMax = 0;
            currentPrice = 0;
            participantSelect.disabled = true;
            totalElement.textContent = formatPrice(0);
            return;
        }
        currentMax = Math.min(
            25,
            parseInt(selectedOption.dataset.cupos || '0', 10)
        );
        currentPrice = parseFloat(selectedOption.dataset.precio || '0');
        unitPriceElement.textContent = formatPrice(currentPrice);
        if (currentMax < 1) {
            participantSelect.disabled = true;
            totalElement.textContent = formatPrice(0);
            return;
        }
        participantSelect.disabled = false;
        const selectedCount = Math.min(
            Math.max(previousCount, 1),
            currentMax
        );
        for (let i = 1; i <= currentMax; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i + (i === 1 ? ' persona' : ' personas');
            option.selected = i === selectedCount;
            participantSelect.append(option);
        }
        renderParticipants();
    }
    departureSelect.addEventListener('change', updateParticipantOptions);
    participantSelect.addEventListener('change', renderParticipants);
    form.addEventListener('submit', (event) => {
        if (
            !departureSelect.value ||
            currentMax < 1 ||
            participantSelect.disabled ||
            parseInt(participantSelect.value || '0', 10) < 1
        ) {
            event.preventDefault();
            alert('Selecciona una salida disponible y al menos un participante.');
        }
    });
    updateParticipantOptions();
})();
</script>
@endpush