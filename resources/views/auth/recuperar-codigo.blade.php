<x-guest-layout>
<div class="mx-auto my-10 max-w-lg rounded-2xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Recuperar contraseña</h1>
    <p class="my-3">Solicita un código y escríbelo aquí. Puedes leer el correo desde cualquier dispositivo. El código vence en 10 minutos.</p>
    @if (session('status'))<p role="status" class="text-green-700">{{ session('status') }}</p>@endif
    <x-validation-errors class="my-4" />
    <form method="POST" action="{{ route('codigo.solicitar') }}" class="space-y-3">
        @csrf
        <label class="block" for="solicitar-correo">Correo de la cuenta</label><input id="solicitar-correo" type="email" name="cor" required maxlength="150" autocomplete="email" value="{{ old('cor') }}" class="w-full rounded-lg border-gray-300">
        <x-button>Enviar código</x-button>
    </form>
    <hr class="my-6">
    <form method="POST" action="{{ route('codigo.restablecer') }}" class="space-y-3">
        @csrf
        <label class="block" for="reset-correo">Correo de la cuenta</label><input id="reset-correo" type="email" name="cor" required maxlength="150" autocomplete="email" value="{{ old('cor') }}" class="w-full rounded-lg border-gray-300">
        <label class="block" for="codigo">Código de 6 dígitos</label><input id="codigo" name="codigo" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required class="w-full rounded-lg border-gray-300">
        <label class="block" for="password">Nueva contraseña</label><input id="password" name="password" type="password" minlength="8" required autocomplete="new-password" class="w-full rounded-lg border-gray-300">
        <button type="button" data-toggle-password="password" class="text-sm underline">Mostrar contraseña</button>
        <p class="text-sm">Al menos 8 caracteres, una letra y un número.</p>
        <label class="block" for="password_confirmation">Confirmar contraseña</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password" class="w-full rounded-lg border-gray-300">
        <x-button>Guardar nueva contraseña</x-button>
    </form>
    <a class="mt-5 block underline" href="{{ route('login') }}">Volver al inicio de sesión</a>
</div>
</x-guest-layout>
