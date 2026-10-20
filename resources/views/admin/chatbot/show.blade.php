@extends('layouts.admin')

@section('header-title', 'Conversación #' . $c->id)

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('admin.chatbot') }}" class="text-sm font-semibold text-[#1F5F8B]">← Volver</a>
    @include('admin.partials.mensajes')

    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-3">
        <p class="text-sm text-[#6B6F72]">{{ $c->usuario?->nom ?? 'Invitado' }} · inició {{ $c->ini_en?->format('d/m/Y H:i') }} · {{ $c->der_hum ? 'derivada a un asesor' : 'atendida por el asistente' }}</p>
        @foreach ($c->mensajes as $m)
            <div class="flex {{ $m->rem === 'usuario' ? 'justify-start' : 'justify-end' }}">
                <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm whitespace-pre-line {{ $m->rem === 'usuario' ? 'bg-gray-100' : ($m->int_det === 'humano' ? 'bg-[#F4C95D]/30' : 'bg-[#1F5F8B]/10') }}">
                    <p class="text-[10px] font-bold uppercase text-[#6B6F72]">{{ $m->rem === 'usuario' ? 'Cliente' : ($m->int_det === 'humano' ? 'Asesor' : 'Asistente · '.$m->int_det) }} · {{ $m->cre_en?->format('H:i') }}</p>
                    {{ $m->txt_msj }}
                </div>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.chatbot.responder', $c) }}" class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-3">
        @csrf
        <textarea name="mensaje" rows="3" required maxlength="2000" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" placeholder="Escribe la respuesta para el cliente"></textarea>
        <div class="flex flex-wrap gap-3">
            <button class="rounded-xl bg-[#1F5F8B] px-4 py-2 text-sm font-semibold text-white">Responder</button>
            @if ($c->der_hum)
                <button form="cerrar" class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-semibold text-[#343A40]">Devolver al asistente automático</button>
            @endif
        </div>
    </form>
    <form id="cerrar" method="POST" action="{{ route('admin.chatbot.cerrar', $c) }}">@csrf</form>
</div>
@endsection
