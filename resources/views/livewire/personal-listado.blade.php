<section class="space-y-4" aria-label="Listado de personal">
    <div class="grid gap-3 rounded-2xl bg-white p-4 sm:grid-cols-3">
        <div><label class="rsv-etiqueta" for="personal-buscar">Buscar personal</label><input id="personal-buscar" type="search" wire:model.live.debounce.350ms="buscar" maxlength="120" placeholder="Nombre o correo" class="rsv-campo"></div>
        <div><label class="rsv-etiqueta" for="personal-tipo">Función</label><select id="personal-tipo" wire:model.live="tipo" class="rsv-campo"><option value="guia">Guías</option><option value="chofer">Choferes</option></select></div>
        <div><label class="rsv-etiqueta" for="personal-estado">Estado</label><select id="personal-estado" wire:model.live="estado" class="rsv-campo"><option value="todos">Todos</option><option value="activo">Activos</option><option value="inactivo">Inactivos</option></select></div>
    </div>
    <p wire:loading role="status" class="text-sm text-slate-500">Buscando personal…</p>
    <div class="grid gap-4 lg:grid-cols-2" wire:loading.class="opacity-60">
        @forelse ($perfiles as $p)
        <article wire:key="personal-{{ $tipo }}-{{ $p->id }}" class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="font-bold text-pipocas-principal">{{ $p->usuario->nom }}</h2><p class="break-all text-sm text-slate-500">{{ $p->usuario->cor }}</p></div><span class="rounded-lg bg-slate-100 px-3 py-1 text-xs">{{ $p->act ? 'Activo' : 'Inactivo' }}</span></div>
            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-xs text-slate-500">Teléfono</dt><dd>{{ $p->usuario->tel ?: 'Sin registrar' }}</dd></div>
                <div><dt class="text-xs text-slate-500">Salidas futuras</dt><dd>{{ $p->futuras }}</dd></div>
                @if ($tipo === 'guia')
                    <div><dt class="text-xs text-slate-500">Idiomas</dt><dd>{{ $p->idiomas->pluck('nom')->implode(', ') }}</dd></div><div><dt class="text-xs text-slate-500">Experiencia y disponibilidad</dt><dd>{{ $p->ani_exp }} años · {{ $p->lim_hrs_sem }} h/semana</dd></div>
                @else
                    <div><dt class="text-xs text-slate-500">Licencia</dt><dd>{{ $p->num_lic }} · Categoría {{ $p->tip_lic }}</dd></div><div><dt class="text-xs text-slate-500">Vencimiento</dt><dd>{{ $p->fec_ven_lic->format('d/m/Y') }}</dd></div>
                @endif
            </dl>
            <div class="mt-5 flex flex-wrap gap-4 text-sm font-semibold"><a href="{{ route('admin.personal.edit', [$tipo, $p->id]) }}" class="text-pipocas-principal">Editar datos</a><form method="POST" action="{{ route('admin.personal.estado', [$tipo, $p->id]) }}">@csrf<button type="submit" class="{{ $p->act ? 'text-red-700' : 'text-emerald-700' }}">{{ $p->act ? 'Desactivar' : 'Activar' }}</button></form></div>
        </article>
        @empty
        <p class="rounded-2xl bg-white p-6 text-slate-500">No encontramos personal con estos filtros.</p>
        @endforelse
    </div>
    {{ $perfiles->links() }}
</section>
