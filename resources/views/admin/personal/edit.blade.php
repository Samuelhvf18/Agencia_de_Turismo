@extends('layouts.admin')

@section('header-title', 'Editar ' . ($tipo === 'guia' ? 'guía' : 'chofer'))

@section('content')
@php $campo = 'w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm'; @endphp
<div class="max-w-3xl space-y-6">
    <a href="{{ route('admin.personal') }}" class="text-sm font-semibold text-[#1F5F8B]">← Volver a personal</a>
    @include('admin.partials.mensajes')

    <form method="POST" action="{{ route('admin.personal.update', [$tipo, $perfil->id]) }}" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm grid gap-4 md:grid-cols-2">
        @csrf @method('PUT')
        <label class="text-xs text-[#6B6F72]">Nombre completo<input name="nom" required value="{{ old('nom', $perfil->usuario->nom) }}" class="{{ $campo }}"></label>
        <label class="text-xs text-[#6B6F72]">Correo<input name="cor" type="email" required value="{{ old('cor', $perfil->usuario->cor) }}" class="{{ $campo }}"></label>
        <label class="text-xs text-[#6B6F72]">Teléfono<input name="tel" value="{{ old('tel', $perfil->usuario->tel) }}" class="{{ $campo }}"></label>

        @if ($tipo === 'guia')
            <label class="text-xs text-[#6B6F72]">Años de experiencia<input name="ani_exp" type="number" min="0" max="60" value="{{ old('ani_exp', $perfil->ani_exp) }}" class="{{ $campo }}"></label>
            <label class="text-xs text-[#6B6F72]">Límite de horas por semana<input name="lim_hrs_sem" type="number" min="1" max="60" value="{{ old('lim_hrs_sem', $perfil->lim_hrs_sem) }}" class="{{ $campo }}"></label>
            <fieldset class="md:col-span-2">
                <legend class="text-xs text-[#6B6F72] mb-1">Idiomas</legend>
                @php $sel = old('idiomas', $perfil->idiomas->pluck('id')->all()); @endphp
                <div class="flex flex-wrap gap-3 text-sm">
                    @foreach ($idiomas as $i)
                        <label class="inline-flex items-center gap-1.5"><input type="checkbox" name="idiomas[]" value="{{ $i->id }}" @checked(in_array($i->id, $sel))> {{ $i->nom }}</label>
                    @endforeach
                </div>
            </fieldset>
            <textarea name="bio" rows="3" class="md:col-span-2 {{ $campo }}" placeholder="Biografía">{{ old('bio', $perfil->bio) }}</textarea>
        @else
            <label class="text-xs text-[#6B6F72]">N.º de licencia<input name="num_lic" required value="{{ old('num_lic', $perfil->num_lic) }}" class="{{ $campo }}"></label>
            <label class="text-xs text-[#6B6F72]">Categoría
                <select name="tip_lic" class="{{ $campo }}">
                    @foreach (['A', 'B', 'C', 'P', 'M', 'T'] as $t)<option value="{{ $t }}" @selected(old('tip_lic', $perfil->tip_lic) === $t)>{{ $t }}</option>@endforeach
                </select>
            </label>
            <label class="text-xs text-[#6B6F72]">Vencimiento<input name="fec_ven_lic" type="date" required value="{{ old('fec_ven_lic', $perfil->fec_ven_lic->toDateString()) }}" class="{{ $campo }}"></label>
        @endif

        <div class="md:col-span-2"><button class="rounded-xl bg-[#1F5F8B] px-5 py-2 text-sm font-semibold text-white">Guardar cambios</button></div>
    </form>
</div>
@endsection
