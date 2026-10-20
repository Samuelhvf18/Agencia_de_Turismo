<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Olvidaste tu contraseña? No hay problema. Escribe tu correo electrónico y te enviaremos un enlace para restablecerla.
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="cor" value="Correo electrónico" />

                <x-input
                    id="cor"
                    class="block mt-1 w-full"
                    type="email"
                    name="cor"
                    :value="old('cor')"
                    required
                    autofocus
                    autocomplete="email"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    Enviar enlace de recuperación
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
