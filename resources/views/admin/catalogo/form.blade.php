@extends('layouts.admin')

@section('header-title', $tour->exists ? 'Editar tour · ' . $tour->nom : 'Nuevo tour')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
@endpush

@section('content')
@php $campo = 'mt-1 w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm'; @endphp
<div class="space-y-6">
    <a href="{{ route('admin.tours') }}" class="text-sm font-semibold text-[#1F5F8B]">← Volver al catálogo</a>
    @include('admin.partials.mensajes')

    {{-- Datos del tour --}}
    <form method="POST" action="{{ $tour->exists ? route('admin.catalogo.update', $tour) : route('admin.catalogo.store') }}" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm grid gap-4 md:grid-cols-4">
        @csrf
        @if ($tour->exists) @method('PUT') @endif
        <h3 class="md:col-span-4 font-bold text-[#343A40]">Datos del tour
            @if ($tour->exists)<span class="ml-2 font-mono text-xs font-normal text-[#6B6F72]">{{ $tour->cod }} · /tours/{{ $tour->rta_amg }}</span>@endif
        </h3>
        <label class="md:col-span-2 text-xs text-[#6B6F72]">Nombre<input name="nom" required maxlength="150" value="{{ old('nom', $tour->nom) }}" class="{{ $campo }}"></label>
        <label class="text-xs text-[#6B6F72]">Categoría
            <select name="cat_id" class="{{ $campo }}">@foreach ($categorias as $c)<option value="{{ $c->id }}" @selected((int) old('cat_id', $tour->cat_id) === $c->id)>{{ $c->nom }}</option>@endforeach</select>
        </label>
        <label class="text-xs text-[#6B6F72]">Dificultad
            <select name="niv_dif" class="{{ $campo }}">@foreach (['facil' => 'Fácil', 'moderado' => 'Moderado', 'dificil' => 'Difícil', 'experto' => 'Experto'] as $v => $t)<option value="{{ $v }}" @selected(old('niv_dif', $tour->niv_dif) === $v)>{{ $t }}</option>@endforeach</select>
        </label>
        <label class="text-xs text-[#6B6F72]">Duración (h, 1–14)<input name="dur_hrs" type="number" step="0.25" min="1" max="14" required value="{{ old('dur_hrs', $tour->dur_hrs) }}" class="{{ $campo }}"></label>
        <label class="text-xs text-[#6B6F72]">Precio base (Bs.)<input name="pre_bas" type="number" step="0.01" min="0" required value="{{ old('pre_bas', $tour->pre_bas) }}" class="{{ $campo }}"></label>
        <label class="text-xs text-[#6B6F72]">Capacidad máxima<input name="cap_max" type="number" min="1" max="60" required value="{{ old('cap_max', $tour->cap_max ?? 12) }}" class="{{ $campo }}"></label>
        <div class="grid grid-cols-2 gap-2">
            <label class="text-xs text-[#6B6F72]">Alt. mín. (m)<input name="alt_min_m" type="number" min="0" max="7000" value="{{ old('alt_min_m', $tour->alt_min_m) }}" class="{{ $campo }}"></label>
            <label class="text-xs text-[#6B6F72]">Alt. máx. (m)<input name="alt_max_m" type="number" min="0" max="7000" value="{{ old('alt_max_m', $tour->alt_max_m) }}" class="{{ $campo }}"></label>
        </div>
        <label class="md:col-span-4 text-xs text-[#6B6F72]">Descripción<textarea name="des" rows="3" class="{{ $campo }}">{{ old('des', $tour->des) }}</textarea></label>
        <label class="md:col-span-4 text-xs text-[#6B6F72]">Requisitos físicos<textarea name="req_fis" rows="2" class="{{ $campo }}">{{ old('req_fis', $tour->req_fis) }}</textarea></label>
        <div class="md:col-span-4 flex flex-wrap items-center gap-4">
            <label class="inline-flex items-center gap-2 text-sm"><input type="hidden" name="act" value="0"><input type="checkbox" name="act" value="1" @checked(old('act', $tour->act))> Publicado en el catálogo</label>
            @if ($tour->exists)
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="regenerar_ruta" value="1"> Regenerar la ruta amigable con el nombre nuevo</label>
            @endif
            <button class="rounded-xl bg-[#1F5F8B] px-5 py-2 text-sm font-semibold text-white">{{ $tour->exists ? 'Guardar cambios' : 'Crear tour' }}</button>
        </div>
    </form>

    @if ($tour->exists)
        {{-- Galería --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-[#343A40]">Galería <span class="text-xs font-normal text-[#6B6F72]">(la primera foto es la portada del catálogo)</span></h3>
            @if ($fotos->isNotEmpty())
                <form method="POST" action="{{ route('admin.catalogo.fotos.orden', $tour) }}" x-data="{ orden: @js($fotos->pluck('foto.id')) }">
                    @csrf
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <template x-for="(id, i) in orden" :key="id">
                            <input type="hidden" name="orden[]" :value="id">
                        </template>
                        @foreach ($fotos as $f)
                            <div class="rounded-xl border border-gray-200 p-2" :style="'order:' + orden.indexOf({{ $f['foto']->id }})">
                                @if ($f['url'])<img src="{{ $f['url'] }}" alt="{{ $f['foto']->txt_atv }}" class="h-32 w-full rounded-lg object-cover">@else<div class="grid h-32 place-items-center rounded-lg bg-gray-100 text-xs text-[#6B6F72]">Archivo no encontrado</div>@endif
                                <input name="txt[{{ $f['foto']->id }}]" value="{{ $f['foto']->txt_atv }}" maxlength="150" placeholder="Texto alternativo" class="mt-2 w-full rounded-lg border border-gray-300 px-2 py-1 text-xs">
                                <div class="mt-2 flex items-center justify-between text-xs font-bold">
                                    <span class="flex gap-1">
                                        <button type="button" class="rounded bg-gray-100 px-2" @click="let i = orden.indexOf({{ $f['foto']->id }}); if (i > 0) { orden.splice(i, 1); orden.splice(i - 1, 0, {{ $f['foto']->id }}) }">←</button>
                                        <button type="button" class="rounded bg-gray-100 px-2" @click="let i = orden.indexOf({{ $f['foto']->id }}); if (i < orden.length - 1) { orden.splice(i, 1); orden.splice(i + 1, 0, {{ $f['foto']->id }}) }">→</button>
                                    </span>
                                    <button type="submit" form="eliminar-foto-{{ $f['foto']->id }}" class="text-red-600">Eliminar</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="mt-3 rounded-xl bg-[#1F5F8B] px-4 py-2 text-xs font-semibold text-white">Guardar orden y textos</button>
                </form>
                @foreach ($fotos as $f)
                    <form id="eliminar-foto-{{ $f['foto']->id }}" method="POST" action="{{ route('admin.catalogo.fotos.eliminar', [$tour, $f['foto']]) }}" onsubmit="return confirm('¿Eliminar la foto?')">@csrf @method('DELETE')</form>
                @endforeach
            @endif
            <form method="POST" action="{{ route('admin.catalogo.fotos', $tour) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                @csrf
                <input type="file" name="fotos[]" multiple accept="image/jpeg,image/png,image/webp" required class="text-sm">
                <input name="txt_atv" maxlength="150" placeholder="Texto alternativo (opcional)" class="rounded-xl border border-gray-300 px-3 py-2 text-sm">
                <button class="rounded-xl bg-[#C96F4A] px-4 py-2 text-xs font-semibold text-white">Subir fotos (máx. 4 MB c/u)</button>
            </form>
        </div>

        {{-- Editor de trazado y puntos de interés --}}
        <form method="POST" action="{{ route('admin.catalogo.ruta', $tour) }}" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4"
              x-data="editorRuta(@js($tour->puntosRuta->map->only(['nom', 'lat', 'lon', 'alt_m', 'tip_pun'])->values()), @js($tour->trz_geo))">
            @csrf
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-bold text-[#343A40]">Trazado y puntos de interés</h3>
                <p class="text-xs text-[#6B6F72]">Haz clic en el mapa para agregar un punto; arrástralo para moverlo.</p>
            </div>
            <p x-show="errorMapa" x-text="errorMapa" class="text-sm text-amber-700" role="status"></p>
            <div id="mapa-editor" class="h-80 rounded-xl border border-gray-200"></div>

            <div class="space-y-2">
                <template x-for="(p, i) in puntos" :key="i">
                    <div class="grid grid-cols-2 gap-2 rounded-xl bg-gray-50 p-2 text-xs md:grid-cols-7 md:items-center">
                        <span class="font-bold text-[#1F5F8B]" x-text="'#' + (i + 1)"></span>
                        <input :name="`puntos[${i}][nom]`" x-model="p.nom" placeholder="Nombre" class="md:col-span-2 rounded-lg border border-gray-300 px-2 py-1">
                        <select :name="`puntos[${i}][tip_pun]`" x-model="p.tip_pun" class="rounded-lg border border-gray-300 px-2 py-1">
                            <option value="inicio">Inicio</option><option value="checkpoint">Checkpoint</option><option value="mirador">Mirador</option><option value="fin">Fin</option>
                        </select>
                        <input :name="`puntos[${i}][alt_m]`" x-model="p.alt_m" type="number" min="0" max="7000" placeholder="Altitud m" class="rounded-lg border border-gray-300 px-2 py-1">
                        <span class="font-mono text-[11px] text-[#6B6F72]" x-text="(+p.lat).toFixed(5) + ', ' + (+p.lon).toFixed(5)"></span>
                        <input type="hidden" :name="`puntos[${i}][lat]`" :value="p.lat"><input type="hidden" :name="`puntos[${i}][lon]`" :value="p.lon">
                        <span class="flex justify-end gap-2 font-bold">
                            <button type="button" @click="mover(i, -1)">↑</button><button type="button" @click="mover(i, 1)">↓</button>
                            <button type="button" class="text-red-600" @click="quitar(i)">Quitar</button>
                        </span>
                    </div>
                </template>
                <p x-show="!puntos.length" class="text-sm text-[#6B6F72]">Todavía no hay puntos.</p>
            </div>

            <details class="text-sm">
                <summary class="cursor-pointer font-semibold text-[#1F5F8B]">Trazado GeoJSON (opcional)</summary>
                <p class="mt-2 text-xs text-[#6B6F72]">Pega un GeoJSON (LineString con [lon, lat, altitud]) exportado de un GPS o de geojson.io. Si lo dejas vacío y marcas la casilla, se genera uniendo los puntos.</p>
                <textarea name="trazado" rows="4" x-model="geojson" class="mt-2 w-full rounded-xl border border-gray-300 px-3 py-2 font-mono text-xs" placeholder='{"type":"LineString","coordinates":[[-68.04,-16.35,4700],...]}'></textarea>
            </details>
            <div class="flex flex-wrap items-center gap-4">
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="trazado_desde_puntos" value="1" @checked(! $tour->trz_geo)> Generar el trazado desde los puntos</label>
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="quitar_trazado" value="1"> Quitar el trazado GeoJSON</label>
                <button class="rounded-xl bg-[#1F5F8B] px-5 py-2 text-sm font-semibold text-white">Guardar ruta</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.catalogo.destroy', $tour) }}" onsubmit="return confirm('¿Dar de baja el tour? Dejará de publicarse.')" class="text-right">
            @csrf @method('DELETE')
            <button class="text-sm font-semibold text-red-600">Dar de baja este tour</button>
        </form>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script>
function editorRuta(iniciales, trazado) {
    return {
        puntos: iniciales.map(p => ({ ...p })),
        geojson: '',
        mapa: null, capa: null, linea: null, errorMapa: '',
        init() {
            if (typeof L === 'undefined') { this.errorMapa = 'No se pudo cargar el mapa. Puedes editar las coordenadas de la lista y guardar.'; return; }
            this.mapa = L.map('mapa-editor');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(this.mapa);
            this.capa = L.layerGroup().addTo(this.mapa);
            if (trazado) L.geoJSON(trazado, { style: { color: '#C96F4A', weight: 3, dashArray: '6 6' } }).addTo(this.mapa);
            this.mapa.on('click', e => {
                const tip = this.puntos.length === 0 ? 'inicio' : 'checkpoint';
                this.puntos.push({ nom: '', lat: +e.latlng.lat.toFixed(7), lon: +e.latlng.lng.toFixed(7), alt_m: null, tip_pun: tip });
                this.dibujar();
            });
            this.dibujar(true);
            this.$watch('puntos', () => this.dibujar(), { deep: true });
        },
        dibujar(encuadrar = false) {
            this.capa.clearLayers();
            this.puntos.forEach((p, i) => {
                const m = L.marker([p.lat, p.lon], { draggable: true, title: p.nom || '#' + (i + 1) }).addTo(this.capa);
                m.on('dragend', e => { const ll = e.target.getLatLng(); p.lat = +ll.lat.toFixed(7); p.lon = +ll.lng.toFixed(7); });
            });
            if (this.puntos.length > 1) L.polyline(this.puntos.map(p => [p.lat, p.lon]), { color: '#1F5F8B', weight: 3 }).addTo(this.capa);
            if (encuadrar) {
                if (this.puntos.length) this.mapa.fitBounds(L.latLngBounds(this.puntos.map(p => [p.lat, p.lon])).pad(0.3));
                else this.mapa.setView([-16.5, -68.15], 10); // La Paz
            }
        },
        mover(i, d) { const j = i + d; if (j < 0 || j >= this.puntos.length) return; const [p] = this.puntos.splice(i, 1); this.puntos.splice(j, 0, p); },
        quitar(i) { this.puntos.splice(i, 1); },
    };
}
</script>
@endpush
