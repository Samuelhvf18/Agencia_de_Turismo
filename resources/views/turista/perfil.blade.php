@extends('layouts.turista')
@section('title', 'Mi perfil')
@section('topbar-title', 'Mi perfil')
@section('content')
<div class="mb-7">
    <p class="mb-2 text-xs font-bold uppercase tracking-[.18em] text-pipocas-terra">
        Tu cuenta
    </p>
    <h1 class="page-title text-3xl font-semibold">Mi perfil</h1>
    <p class="mt-2 text-sm text-pipocas-muted">
        Consulta y actualiza tu información personal en Pipocas.
    </p>
</div>
<div class="tourist-card max-w-3xl p-6 sm:p-8">
    <div class="mb-6 flex items-center gap-4 border-b border-slate-100 pb-6">
        <div class="grid h-16 w-16 place-items-center rounded-2xl bg-pipocas-blue/10 text-2xl font-extrabold text-pipocas-blue">
            {{ strtoupper(mb_substr($usuario->nom ?? 'T', 0, 1)) }}
        </div>
        <div>
            <h2 class="text-lg font-extrabold">{{ $usuario->nom }}</h2>
            <p class="text-xs text-pipocas-muted">Cuenta turista</p>
        </div>
    </div>
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            <i class="fa-solid fa-circle-check mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="mb-2 font-bold">No se pudieron actualizar tus datos.</p>
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('turista.perfil.update') }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label for="nom" class="mb-2 block text-xs font-bold uppercase tracking-wider text-pipocas-muted">
                Nombre completo
            </label>
            <input
                type="text"
                id="nom"
                name="nom"
                value="{{ old('nom', $usuario->nom) }}"
                maxlength="150"
                required
                autocomplete="name"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-pipocas-blue focus:ring-2 focus:ring-pipocas-blue/20">
            @error('nom')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="cor" class="mb-2 block text-xs font-bold uppercase tracking-wider text-pipocas-muted">
                Correo electrónico
            </label>
            <input
                type="email"
                id="cor"
                value="{{ $usuario->cor }}"
                readonly
                class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-500">
            <p class="mt-1 text-xs text-pipocas-muted">
                El correo electrónico no se puede modificar desde esta sección.
            </p>
        </div>
        <div>
            <label for="tel" class="mb-2 block text-xs font-bold uppercase tracking-wider text-pipocas-muted">
                Teléfono
            </label>
            <input
                type="text"
                id="tel"
                name="tel"
                value="{{ old('tel', $usuario->tel) }}"
                maxlength="20"
                autocomplete="tel"
                placeholder="Ingresa tu teléfono"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-pipocas-blue focus:ring-2 focus:ring-pipocas-blue/20">
            @error('tel')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="tip_doc" class="mb-2 block text-xs font-bold uppercase tracking-wider text-pipocas-muted">
                Tipo de documento
            </label>
            <input
                type="text"
                id="tip_doc"
                name="tip_doc"
                value="{{ old('tip_doc', $usuario->tip_doc) }}"
                maxlength="30"
                placeholder="Ej. CI, pasaporte"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-pipocas-blue focus:ring-2 focus:ring-pipocas-blue/20">
            @error('tip_doc')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="num_doc" class="mb-2 block text-xs font-bold uppercase tracking-wider text-pipocas-muted">
                Número de documento
            </label>
            <input
                type="text"
                id="num_doc"
                name="num_doc"
                value="{{ old('num_doc', $usuario->num_doc) }}"
                maxlength="30"
                placeholder="Ingresa tu número de documento"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-pipocas-blue focus:ring-2 focus:ring-pipocas-blue/20">
            @error('num_doc')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-6">
            <button
                type="submit"
                class="rounded-xl bg-pipocas-blue px-6 py-3 text-sm font-bold text-white transition hover:bg-pipocas-blue/90">
                <i class="fa-solid fa-floppy-disk mr-2"></i>
                Guardar cambios
            </button>
            <a
                href="{{ route('turista.dashboard') }}"
                class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-bold text-pipocas-muted transition hover:bg-slate-50">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection