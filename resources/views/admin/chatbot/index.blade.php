@extends('layouts.admin')

@section('header-title', 'Chatbot: atención humana')

@section('content')
<div class="space-y-6">
    @include('admin.partials.mensajes')

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-[#6B6F72]"><b class="text-[#C96F4A]">{{ $pendientes }}</b> conversación(es) esperan a un asesor.</p>
        <div class="inline-flex rounded-xl bg-white border border-gray-200 p-1 text-sm font-semibold">
            <a href="{{ route('admin.chatbot', ['derivadas' => 1]) }}" class="rounded-lg px-4 py-2 {{ $soloDerivadas ? 'bg-[#1F5F8B] text-white' : '' }}">Derivadas</a>
            <a href="{{ route('admin.chatbot', ['derivadas' => 0]) }}" class="rounded-lg px-4 py-2 {{ ! $soloDerivadas ? 'bg-[#1F5F8B] text-white' : '' }}">Todas</a>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto shadow-sm">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-[#6B6F72] uppercase"><tr><th class="p-3">#</th><th class="p-3">Cliente</th><th class="p-3">Inicio</th><th class="p-3">Mensajes</th><th class="p-3">Estado</th><th class="p-3"></th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($conversaciones as $c)
                    <tr>
                        <td class="p-3">{{ $c->id }}</td>
                        <td class="p-3">{{ $c->usuario?->nom ?? 'Invitado' }}<br><span class="text-xs text-[#6B6F72]">{{ $c->usuario?->cor }}</span></td>
                        <td class="p-3 text-xs">{{ $c->ini_en?->format('d/m/Y H:i') }}</td>
                        <td class="p-3">{{ $c->mensajes_count }}</td>
                        <td class="p-3">@if ($c->der_hum)<span class="rounded-lg bg-[#C96F4A]/15 px-2 py-1 text-xs font-bold text-[#C96F4A]">Espera asesor</span>@else<span class="text-xs text-[#6B6F72]">Automático</span>@endif</td>
                        <td class="p-3 text-right"><a href="{{ route('admin.chatbot.show', $c) }}" class="text-xs font-bold text-[#1F5F8B]">Abrir</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-[#6B6F72]">No hay conversaciones.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $conversaciones->links() }}
</div>
@endsection
