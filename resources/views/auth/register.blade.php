<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#F8F6F1] p-4 relative overflow-hidden">

        {{-- Fondo decorativo --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#6BB6D6]/20 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-[#C96F4A]/10 blur-3xl"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">

            {{-- Encabezado --}}
            <div class="text-center mb-8">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#1F5F8B] shadow-xl shadow-[#1F5F8B]/20">
                    <i class="fa-solid fa-mountain-sun text-3xl text-[#F4C95D]"></i>
                </div>

                <h1 class="text-4xl font-bold tracking-tight text-[#343A40]">
                    <span class="text-[#1F5F8B]">Pipocas</span>
                </h1>

                <p class="mt-2 text-sm font-medium text-[#6B4F3A]">
                    Agencia de Viajes y Turismo
                </p>

                <div class="mx-auto mt-4 flex items-center justify-center gap-3">
                    <span class="h-px w-10 bg-[#C96F4A]"></span>
                    <i class="fa-solid fa-location-dot text-sm text-[#C96F4A]"></i>
                    <span class="h-px w-10 bg-[#C96F4A]"></span>
                </div>
            </div>

            {{-- Tarjeta --}}
            <div class="rounded-2xl bg-white border border-[#E5DED5] shadow-xl shadow-[#6B4F3A]/10 p-8">

                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-[#343A40]">
                        Crear una cuenta
                    </h2>
                    <p class="mt-1 text-sm text-[#6B6F72]">
                        Regístrate para comenzar a descubrir nuevos destinos.
                    </p>
                </div>

                {{-- Errores --}}
                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-4">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
                            <ul class="text-sm text-red-600 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Formulario --}}
                <form
                    id="registerForm"
                    method="POST"
                    action="{{ route('register') }}"
                    class="space-y-5"
                >
                    @csrf

                    {{-- Nombre --}}
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-[#343A40]">
                            Nombre completo
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Tu nombre"
                            class="w-full rounded-xl bg-[#F8F6F1] border border-[#D9D2C9] px-4 py-3 text-[#343A40] placeholder:text-[#9A9A9A] transition focus:border-[#1F5F8B] focus:ring-2 focus:ring-[#1F5F8B]/20 focus:outline-none"
                        >
                    </div>

                    {{-- Correo --}}
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-[#343A40]">
                            Correo electrónico
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="correo@ejemplo.com"
                            class="w-full rounded-xl bg-[#F8F6F1] border border-[#D9D2C9] px-4 py-3 text-[#343A40] placeholder:text-[#9A9A9A] transition focus:border-[#1F5F8B] focus:ring-2 focus:ring-[#1F5F8B]/20 focus:outline-none"
                        >
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-[#343A40]">
                            Contraseña
                        </label>

                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Crea una contraseña"
                                oninput="checkPasswordStrength(); checkPasswordMatch()"
                                class="w-full rounded-xl bg-[#F8F6F1] border border-[#D9D2C9] px-4 py-3 pr-12 text-[#343A40] placeholder:text-[#9A9A9A] transition focus:border-[#1F5F8B] focus:ring-2 focus:ring-[#1F5F8B]/20 focus:outline-none"
                            >

                            <button
                                type="button"
                                onclick="togglePassword('password', 'passwordIcon')"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-[#6B6F72] hover:text-[#1F5F8B]"
                                aria-label="Mostrar contraseña"
                            >
                                <i id="passwordIcon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        {{-- Indicador de seguridad --}}
                        <div class="mt-3">
                            <div class="flex gap-1.5 h-1.5">
                                <span id="bar1" class="flex-1 rounded-full bg-[#E5E0D8]"></span>
                                <span id="bar2" class="flex-1 rounded-full bg-[#E5E0D8]"></span>
                                <span id="bar3" class="flex-1 rounded-full bg-[#E5E0D8]"></span>
                                <span id="bar4" class="flex-1 rounded-full bg-[#E5E0D8]"></span>
                            </div>

                            <p class="mt-1.5 text-xs text-[#6B6F72]">
                                Seguridad:
                                <span id="strengthText" class="font-medium">Sin contraseña</span>
                            </p>
                        </div>
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div>
                        <label for="password_confirmation" class="block mb-2 text-sm font-medium text-[#343A40]">
                            Confirmar contraseña
                        </label>

                        <div class="relative">
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Repite tu contraseña"
                                oninput="checkPasswordMatch()"
                                class="w-full rounded-xl bg-[#F8F6F1] border border-[#D9D2C9] px-4 py-3 pr-12 text-[#343A40] placeholder:text-[#9A9A9A] transition focus:border-[#1F5F8B] focus:ring-2 focus:ring-[#1F5F8B]/20 focus:outline-none"
                            >

                            <button
                                type="button"
                                onclick="togglePassword('password_confirmation', 'confirmationIcon')"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-[#6B6F72] hover:text-[#1F5F8B]"
                                aria-label="Mostrar confirmación de contraseña"
                            >
                                <i id="confirmationIcon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        <p id="matchText" class="mt-1.5 text-xs" aria-live="polite"></p>
                    </div>
                    {{-- Condiciones --}}
                    @if (\Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="flex items-start gap-3">
                        <input
                        id="terms"
                        type="checkbox"
                        name="terms"
                        value="1"
                        required
                        class="mt-1 rounded border-[#D9D2C9] text-[#1F5F8B] focus:ring-[#1F5F8B]">
                        <label for="terms" class="text-xs text-[#6B6F72]">
                            Acepto las condiciones de uso y la política de privacidad de Pipocas.
                        </label>
                    </div>
                    @else
                    <div class="text-center">
                        <p class="text-xs text-[#6B6F72]">
                            Al registrarte aceptas las condiciones de uso de Pipocas.
                        </p>
                    </div>
                    @endif
                    {{-- Botón de registro --}}
                    <button
                        id="registerButton"
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#1F5F8B] px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-[#1F5F8B]/20 transition duration-200 hover:bg-[#174B70] hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-[#1F5F8B]/40"
                    >
                        <i id="registerIcon" class="fa-solid fa-user-plus"></i>
                        <span id="registerText">Crear cuenta</span>
                    </button>
                </form>

                {{-- Ir al login --}}
                <div class="mt-6 text-center">
                    <p class="text-sm text-[#6B6F72]">
                        ¿Ya tienes una cuenta?
                        <a href="{{ route('login') }}" class="font-semibold text-[#C96F4A] hover:text-[#A95536]">
                            Iniciar sesión
                        </a>
                    </p>
                </div>
            </div>

            {{-- Pie --}}
            <div class="mt-6 text-center">
                <p class="text-xs text-[#6B6F72]">
                    <i class="fa-solid fa-compass mr-1 text-[#F4C95D]"></i>
                    Descubre nuevos destinos
                </p>

                <p class="mt-2 text-xs text-[#9A9A9A]">
                    © {{ date('Y') }} Pipocas
                </p>
            </div>
        </div>
    </div>

    {{-- Funciones del registro --}}
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const mostrar = input.type === 'password';

            input.type = mostrar ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !mostrar);
            icon.classList.toggle('fa-eye-slash', mostrar);
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const text = document.getElementById('strengthText');
            const bars = [
                document.getElementById('bar1'),
                document.getElementById('bar2'),
                document.getElementById('bar3'),
                document.getElementById('bar4')
            ];

            bars.forEach(bar => {
                bar.className = 'flex-1 rounded-full bg-[#E5E0D8]';
            });

            if (password.length === 0) {
                text.textContent = 'Sin contraseña';
                return;
            }

            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) strength++;

            const levels = ['Débil', 'Básica', 'Media', 'Fuerte', 'Muy fuerte'];
            text.textContent = levels[strength];

            for (let i = 0; i < strength; i++) {
                bars[i].className =
                    'flex-1 rounded-full bg-[#1F5F8B] transition-all duration-300';
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const text = document.getElementById('matchText');
            const confirmInput = document.getElementById('password_confirmation');

            if (confirmation.length === 0) {
                text.textContent = '';
                confirmInput.setCustomValidity('');
                return;
            }

            if (password === confirmation) {
                text.textContent = '✓ Las contraseñas coinciden';
                text.className = 'mt-1.5 text-xs text-green-600';
                confirmInput.setCustomValidity('');
            } else {
                text.textContent = '✕ Las contraseñas no coinciden';
                text.className = 'mt-1.5 text-xs text-red-500';
                confirmInput.setCustomValidity('Las contraseñas no coinciden.');
            }
        }

        document.getElementById('registerForm').addEventListener('submit', function (event) {
            checkPasswordMatch();

            if (!this.checkValidity()) {
                event.preventDefault();
                this.reportValidity();
                return;
            }

            const button = document.getElementById('registerButton');
            const icon = document.getElementById('registerIcon');
            const text = document.getElementById('registerText');

            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');

            icon.classList.remove('fa-user-plus');
            icon.classList.add('fa-spinner', 'fa-spin');

            text.textContent = 'Creando cuenta...';
        });
    </script>
</x-guest-layout>