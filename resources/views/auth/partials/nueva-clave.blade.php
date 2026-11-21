<p class="mb-5 text-sm text-andinist-texto-2">{{ __('Revisa tu bandeja de entrada o spam. El código vence en 10 minutos y puede usarse una sola vez.') }}</p>
<form method="POST" action="{{ route('codigo.restablecer') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="_paso" value="codigo">
    <div><label class="rsv-etiqueta" for="reset-correo">{{ __('Correo de la cuenta') }}</label><input id="reset-correo" type="email" name="cor" required maxlength="150" autocomplete="email" value="{{ $correo }}" class="rsv-campo"></div>
    <div><label class="rsv-etiqueta" for="codigo">{{ __('Código de 6 dígitos') }}</label><input id="codigo" name="codigo" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" data-numeros autocomplete="one-time-code" required class="rsv-campo text-center text-2xl tracking-[.4em]" placeholder="000000"></div>
    <div><label class="rsv-etiqueta" for="password">{{ __('Nueva contraseña') }}</label><input id="password" data-clave-nueva minlength="8" maxlength="128" name="password" type="password" minlength="8" required autocomplete="new-password" class="rsv-campo" aria-describedby="ayuda-clave"><button type="button" data-toggle-password="password" class="mt-2 text-sm font-semibold text-andinist-principal">{{ __('Mostrar contraseña') }}</button><p id="ayuda-clave" class="mt-1 text-xs text-andinist-texto-2">{{ __('Al menos 8 caracteres, una letra y un número.') }}</p></div>
    <div><label class="rsv-etiqueta" for="password_confirmation">{{ __('Confirmar contraseña') }}</label><input id="password_confirmation" data-clave-confirmacion maxlength="128" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password" class="rsv-campo"><button type="button" data-toggle-password="password_confirmation" class="mt-2 text-sm font-semibold text-andinist-principal">{{ __('Mostrar contraseña') }}</button></div>
    <button type="submit" class="rsv-btn w-full">{{ __('Guardar nueva contraseña') }}</button>
</form>
<form method="POST" action="{{ route('codigo.solicitar') }}" class="mt-5 border-t border-stone-100 pt-4">
    @csrf
    <input type="hidden" name="cor" value="{{ $correo }}">
    <p class="mb-2 text-xs text-andinist-texto-2">{{ __('¿No llegó? Revisa el correo escrito y solicita otro código.') }}</p>
    <button type="submit" class="text-sm font-semibold text-andinist-principal underline">{{ __('Enviar un nuevo código') }}</button>
</form>
