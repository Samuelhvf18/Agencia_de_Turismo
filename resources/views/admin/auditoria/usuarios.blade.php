@extends('layouts.admin')

@section('header-title', 'Usuarios y roles')

@section('page-description', 'Busca una cuenta para cambiar su rol o controlar si puede iniciar sesión.')

@section('content')
@php
    $campo = 'rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm';
    $etiquetas = ['admin' => 'Administrador', 'guia' => 'Guía', 'chofer' => 'Chofer', 'operaciones' => 'Operaciones', 'turista' => 'Turista'];
@endphp
<div class="space-y-6">
    @include('admin.auditoria._tabs')
    @include('admin.partials.mensajes')

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach ($roles as $r)
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <p class="text-xs text-[#6B6F72]">{{ $etiquetas[$r->nom] ?? ucfirst($r->nom) }}</p>
                <p class="text-2xl font-bold text-[#1F5F8B]">{{ $porRol[$r->nom] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    <form method="GET" class="flex gap-2">
        <input name="q" aria-label="Buscar por nombre o correo" value="{{ $q }}" placeholder="Buscar por nombre o correo" class="w-full sm:w-96 {{ $campo }}">
        <button class="rounded-xl bg-[#1F5F8B] px-4 text-sm font-semibold text-white">Buscar</button>
    </form>
    <p class="text-xs text-[#6B6F72]">Crea las cuentas de campo desde <a href="{{ route('admin.personal') }}" class="font-semibold text-[#1F5F8B]">Guías y choferes</a> para que tengan un perfil al que se puedan asignar salidas.</p>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase"><tr><th class="p-3">Usuario</th><th class="p-3">Registrado</th><th class="p-3">Rol y estado</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($usuarios as $u)
                    <tr>
                        <td class="p-3"><span class="font-semibold">{{ $u->nom }}</span><br><span class="text-xs text-[#6B6F72]">{{ $u->cor }}</span></td>
                        <td class="p-3 text-xs">{{ $u->created_at?->format('d/m/Y') }}</td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('admin.auditoria.usuarios.update', $u) }}" class="flex flex-wrap items-center gap-2">
                                @csrf @method('PUT')
                                <select name="rol_id" aria-label="Rol de {{ $u->nom }}" class="{{ $campo }}">@foreach ($roles as $r)<option value="{{ $r->id }}" @selected($u->rol_id === $r->id)>{{ $etiquetas[$r->nom] ?? ucfirst($r->nom) }}</option>@endforeach</select>
                                <select name="act" aria-label="Estado de la cuenta de {{ $u->nom }}" class="{{ $campo }}"><option value="1" @selected($u->act)>Activo</option><option value="0" @selected(! $u->act)>Inactivo</option></select>
                                <button class="rounded-xl bg-[#1F5F8B] px-3 py-2 text-xs font-semibold text-white">Guardar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-6 text-center text-[#6B6F72]">No se encontraron usuarios con ese nombre o correo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $usuarios->links() }}
</div>
@endsection
