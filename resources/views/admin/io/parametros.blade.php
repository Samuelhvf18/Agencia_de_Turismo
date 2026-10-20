{{-- RF-40: parámetros del modelo M/M/c/K por tour. Variables: $tours (tour, par, mu_sugerido, lambda_obs), $historial --}}
@extends('layouts.admin-io')

@section('titulo', 'Parámetros de colas por tour')
@section('subtitulo', 'λ (llegadas/día), μ (tours por guía y día), c (guías) y K (cupos). Un solo juego activo por tour.')

@section('contenido')
@php $fmt = fn ($v, $d = 2) => $v === null ? '—' : number_format((float) $v, $d, ',', '.'); @endphp
<div class="grid gap-6 xl:grid-cols-2">
    @foreach ($tours as $fila)
        @php $t = $fila['tour']; $p = $fila['par']; @endphp
        <section class="rsv-tarjeta">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-titulo text-xl font-semibold text-pipocas-principal">{{ $t->nom }}</h2>
                    <p class="text-xs text-pipocas-texto-2">Duración {{ $fmt($t->dur_hrs, 1) }} h · capacidad {{ $t->cap_max }} · μ sugerido = 24/{{ $fmt($t->dur_hrs, 1) }} = {{ $fmt($fila['mu_sugerido'], 4) }}
                        · λ observado (30 días) = {{ $fmt($fila['lambda_obs'], 3) }}</p>
                </div>
                @if ($p)
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Activo desde {{ $p->fec_cal->format('d/m/Y') }}</span>
                @else
                    <span class="rounded-full bg-pipocas-acento/30 px-3 py-1 text-xs font-bold text-pipocas-calido">Sin parámetros</span>
                @endif
            </div>
            <form method="POST" action="{{ route('admin.io.parametros.store', $t) }}" class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @csrf
                <label class="rsv-etiqueta">λ<input name="lmb" type="number" step="0.0001" min="0" required value="{{ $p->lmb ?? ($fila['lambda_obs'] ?? 1) }}" class="rsv-campo"></label>
                <label class="rsv-etiqueta">μ<input name="mu" type="number" step="0.0001" min="0.0001" required value="{{ $p->mu ?? round($fila['mu_sugerido'], 4) }}" class="rsv-campo"></label>
                <label class="rsv-etiqueta">c<input name="c_srv" type="number" min="1" max="60" required value="{{ $p->c_srv ?? 1 }}" class="rsv-campo"></label>
                <label class="rsv-etiqueta">K<input name="k_cap" type="number" min="1" max="200" required value="{{ $p->k_cap ?? $t->cap_max }}" class="rsv-campo"></label>
                <label class="rsv-etiqueta col-span-1 sm:col-span-2">Costo de espera (Bs/h)<input name="cos_esp" type="number" step="0.01" min="0" value="{{ $p->cos_esp ?? 15 }}" class="rsv-campo"></label>
                <label class="rsv-etiqueta col-span-1 sm:col-span-2">Costo de guía ocioso (Bs/h)<input name="cos_srv" type="number" step="0.01" min="0" value="{{ $p->cos_srv ?? 25 }}" class="rsv-campo"></label>
                <input name="obs" maxlength="500" placeholder="Observación (opcional)" value="" class="rsv-campo col-span-2 sm:col-span-3">
                <button class="rsv-btn col-span-2 sm:col-span-1 px-4 py-2"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
            </form>
        </section>
    @endforeach
</div>

<section class="rsv-tarjeta">
    <h2 class="rsv-titulo mb-3">Historial de cambios</h2>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="text-xs uppercase text-pipocas-texto-2"><tr><th class="py-2">Fecha</th><th>Tour</th><th>λ</th><th>μ</th><th>c</th><th>K</th><th>Estado</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($historial as $h)
                    <tr><td class="py-2">{{ $h->cre_en?->format('d/m/Y H:i') }}</td><td>{{ $h->tour?->nom }}</td><td>{{ $fmt($h->lmb, 4) }}</td><td>{{ $fmt($h->mu, 4) }}</td><td>{{ $h->c_srv }}</td><td>{{ $h->k_cap }}</td>
                        <td>{{ $h->act ? 'Activo' : 'Histórico' }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
