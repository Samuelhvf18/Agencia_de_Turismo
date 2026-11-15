<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif

            <section class="my-8 rounded-xl bg-white p-6 shadow space-y-4">
                <h2 class="text-xl font-bold">Claves de acceso y Google</h2>
                <p>Registra una clave con Windows Hello, huella o el gestor de claves de tu dispositivo. Necesitas HTTPS y confirmar tu contraseña.</p>
                <a class="underline" href="{{ route('password.confirm') }}">Confirmar mi contraseña</a>
                <label for="passkey-name" class="block">Nombre del dispositivo</label><input id="passkey-name" maxlength="255" placeholder="Mi laptop" class="rounded border-gray-300">
                <button type="button" data-passkey-register class="rounded bg-[#1F5F8B] px-4 py-2 text-white">Registrar clave de acceso</button><p data-passkey-status role="status"></p>
                @foreach (auth()->user()->passkeys as $clave)
                    <form method="POST" action="{{ route('passkey.destroy', $clave) }}" class="flex gap-4">@csrf @method('DELETE')<span>{{ $clave->name }}</span><button class="text-red-700 underline">Eliminar clave</button></form>
                @endforeach
                @if (config('services.google.client_id') && config('services.google.client_secret') && config('services.google.redirect'))
                    @if (auth()->user()->google_id)<p>Google está vinculado a tu cuenta.</p>@else<a class="block underline" href="{{ route('google.vincular') }}">Vincular mi cuenta de Google</a>@endif
                @endif
                <p class="text-sm">Si te registraste con Google y necesitas una contraseña, puedes <a class="underline" href="{{ route('codigo.recuperar') }}">restablecerla mediante un código de correo</a> después de cerrar sesión.</p>
            </section>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
