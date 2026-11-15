<x-guest-layout>
<div class="mx-auto my-12 max-w-lg rounded-2xl bg-white p-6 shadow">
    <a href="{{ route('home') }}" class="text-xl font-bold text-[#1F5F8B]">Pipocas · Agencia de Viajes y Turismo</a>
    <h1 class="mt-5 text-2xl font-bold">Verifica tu correo</h1>
    <p class="my-4">Enviamos un código de 6 dígitos a <strong>{{ auth()->user()->cor }}</strong>. Léelo desde tu celular o computadora y escríbelo aquí. Vence en 10 minutos.</p>
    @if (session('status'))<p class="my-3 text-green-700" role="status">Enviamos un nuevo código. Revisa también spam.</p>@endif
    <x-validation-errors class="my-3" />
    <form method="POST" action="{{ route('codigo.verificar') }}" class="space-y-3">
        @csrf
        <label for="codigo" class="block">Código de verificación</label><input id="codigo" name="codigo" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" autofocus required class="w-full rounded-lg border-gray-300">
        <x-button>Verificar correo</x-button>
    </form>
    <form method="POST" action="{{ route('verification.send') }}" class="mt-5">@csrf<button class="underline">Reenviar código</button><p class="mt-1 text-xs">Espera un minuto entre solicitudes.</p></form>
    <div class="mt-5 flex gap-4"><a class="underline" href="{{ route('profile.show') }}">Corregir mi correo</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="underline">Cerrar sesión</button></form></div>
</div>
</x-guest-layout>
