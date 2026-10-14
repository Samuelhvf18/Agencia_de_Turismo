<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#F8F6F1] p-4">

        {{-- Decoración de fondo --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -right-32 w-80 h-80 rounded-full bg-[#6BB6D6]/20 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-[#C96F4A]/10 blur-3xl"></div>
        </div>

        {{-- Contenedor principal --}}
        <div class="relative z-10 w-full max-w-md">

            {{-- Marca --}}
            <div class="text-center mb-8">
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl bg-[#1F5F8B] shadow-xl shadow-[#1F5F8B]/20">
                    <i class="fa-solid fa-mountain-sun text-3xl text-[#F4C95D]"></i>
                </div>

                <h1 class="text-4xl font-bold tracking-tight text-[#343A40]">
                    <span class="text-[#1F5F8B]">Pipocas</span>
                </h1>

                <p class="mt-2 text-sm font-medium text-[#6B4F3A]">
                    Agencia de Viajes y Turismo
                </p>

                <div class="mt-5 flex items-center justify-center gap-3">
                    <span class="h-px w-10 bg-[#C96F4A]"></span>
                    <i class="fa-solid fa-compass text-sm text-[#F4C95D]"></i>
                    <span class="h-px w-10 bg-[#C96F4A]"></span>
                </div>
            </div>

            {{-- Tarjeta de inicio de sesión --}}
            <div class="rounded-2xl border border-[#D9D4CA] bg-white p-8 shadow-xl shadow-[#6B4F3A]/10">

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-[#343A40]">Bienvenido</h2>
                    <p class="mt-1 text-sm text-[#6B6F72]">
                        Ingresa para continuar con tu viaje.
                    </p>
                </div>

                {{-- Errores --}}
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>
                            <ul class="space-y-1 text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Mensaje de sesión --}}
                @if (session('status'))
                    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-600"></i>
                            <p class="text-sm text-green-700">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                {{-- Formulario --}}
                <form
                    id="loginForm"
                    method="POST"
                    action="{{ route('login') }}"
                    class="space-y-5"
                    onsubmit="return loginLoading()"
                >
                    @csrf

                    {{-- Correo --}}
                    <div>
                        <label for="cor" class="mb-2 block text-sm font-semibold text-[#343A40]">
                            Correo electrónico
                        </label>

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <i class="fa-solid fa-envelope text-[#6BB6D6]"></i>
                            </div>

                            <input
                                id="cor"
                                type="email"
                                name="cor"
                                value="{{ old('cor') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="correo@ejemplo.com"
                                class="w-full rounded-xl border border-[#D9D4CA] bg-[#F8F6F1] py-3.5 pl-11 pr-4 text-[#343A40] placeholder:text-[#9A9A9A] transition focus:border-[#1F5F8B] focus:outline-none focus:ring-2 focus:ring-[#6BB6D6]/30"
                            >
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-[#343A40]">
                            Contraseña
                        </label>

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <i class="fa-solid fa-lock text-[#6BB6D6]"></i>
                            </div>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full rounded-xl border border-[#D9D4CA] bg-[#F8F6F1] py-3.5 pl-11 pr-12 text-[#343A40] placeholder:text-[#9A9A9A] transition focus:border-[#1F5F8B] focus:outline-none focus:ring-2 focus:ring-[#6BB6D6]/30"
                            >

                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-[#6B6F72] transition hover:text-[#1F5F8B]"
                                aria-label="Mostrar contraseña"
                                aria-pressed="false"
                            >
                                <i id="passwordIcon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Recordarme y recuperar contraseña --}}
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <label class="flex items-center">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="rounded border-[#C9C3B8] bg-white text-[#1F5F8B] focus:ring-[#6BB6D6]"
                            >
                            <span class="ml-2 text-sm text-[#6B6F72]">Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm font-medium text-[#1F5F8B] transition hover:text-[#C96F4A]"
                            >
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    {{-- Botón de ingreso --}}
                    <button
                        type="submit"
                        id="loginButton"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#1F5F8B] px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-[#1F5F8B]/20 transition duration-200 hover:-translate-y-0.5 hover:bg-[#174B70] focus:outline-none focus:ring-2 focus:ring-[#6BB6D6]/50"
                    >
                        <i id="loginIcon" class="fa-solid fa-right-to-bracket"></i>
                        <span id="loginText">Iniciar sesión</span>
                    </button>
                </form>

                {{-- Registro --}}
                @if (Route::has('register'))
                    <div class="mt-7 border-t border-[#E5E0D8] pt-6 text-center">
                        <p class="text-sm text-[#6B6F72]">
                            ¿Aún no tienes una cuenta?
                            <a
                                href="{{ route('register') }}"
                                class="ml-1 font-semibold text-[#C96F4A] transition hover:text-[#A95538]"
                            >
                                Regístrate
                            </a>
                        </p>
                    </div>
                @endif
            </div>

            {{-- Pie de página --}}
            <div class="mt-6 text-center">
                <div class="flex items-center justify-center gap-2 text-xs text-[#6B6F72]">
                    <i class="fa-solid fa-location-dot text-[#C96F4A]"></i>
                    <span>Descubre Bolivia, descubre nuevos destinos</span>
                </div>

                <p class="mt-2 text-xs text-[#9A9A9A]">
                    © {{ date('Y') }} Pipocas
                </p>
            </div>
        </div>
    </div>

    {{-- Funciones del formulario --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const password = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const passwordIcon = document.getElementById('passwordIcon');

            togglePassword.addEventListener('click', function () {
                const mostrar = password.type === 'password';

                password.type = mostrar ? 'text' : 'password';
                passwordIcon.classList.toggle('fa-eye', !mostrar);
                passwordIcon.classList.toggle('fa-eye-slash', mostrar);

                togglePassword.setAttribute('aria-pressed', String(mostrar));
                togglePassword.setAttribute(
                    'aria-label',
                    mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña'
                );
            });
        });

        function loginLoading() {
            const button = document.getElementById('loginButton');
            const icon = document.getElementById('loginIcon');
            const text = document.getElementById('loginText');

            if (button.disabled) {
                return false;
            }

            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');

            icon.classList.remove('fa-right-to-bracket');
            icon.classList.add('fa-spinner', 'fa-spin');

            text.textContent = 'Iniciando sesión...';

            return true;
        }
    </script>
</x-guest-layout>