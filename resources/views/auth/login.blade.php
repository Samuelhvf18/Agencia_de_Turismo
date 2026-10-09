<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#F8F6F1] p-4">
        {{-- Decoración de fondo --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -right-32 w-80 h-80 rounded-full bg-[#6BB6D6]/20 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-[#C96F4A]/10 blur-3xl"></div>
        </div>
        {{-- Contenedor --}}
        <div class="relative z-10 w-full max-w-md">
            {{-- ENCABEZADO --}}
            <div class="text-center mb-8">
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl
                            bg-[#1F5F8B] shadow-xl shadow-[#1F5F8B]/20">
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
            {{-- TARJETA LOGIN --}}
            <div class="rounded-2xl border border-[#D9D4CA] bg-white p-8 shadow-xl shadow-[#6B4F3A]/10">
                {{-- TÍTULO --}}
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-[#343A40]">
                        Bienvenido
                    </h2>
                    <p class="mt-1 text-sm text-[#6B6F72]">
                        Ingresa para continuar con tu viaje.
                    </p>
                </div>
                {{-- ERRORES --}}
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>
                            <div class="text-sm text-red-600">
                                <p class="font-semibold">
                                    No se pudo iniciar sesión.
                                </p>
                                <p class="mt-1">
                                    Verifica tu usuario o correo electrónico y tu contraseña.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- MENSAJE DE ESTADO --}}
                @if (session('status'))
                    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-600"></i>
                            <p class="text-sm text-green-700">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                @endif
                {{-- FORMULARIO --}}
                <form
                    method="POST"
                    action="{{ route('login') }}"
                    class="space-y-5"
                    onsubmit="loginLoading()">
                    @csrf
                    {{-- USUARIO / CORREO --}}
                    <div>
                        <label
                            for="login"
                            class="block mb-2 text-sm font-semibold text-[#343A40]">
                            Nombre de usuario o dirección de correo electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-solid fa-user text-[#6BB6D6]"></i>
                            </div>
                            <input
                                id="login"
                                type="text"
                                name="login"
                                value="{{ old('login') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="usuario o correo@gmail.com"
                                class="w-full rounded-xl border border-[#D9D4CA]
                                       bg-[#F8F6F1] py-3.5 pl-11 pr-4
                                       text-[#343A40]
                                       placeholder:text-[#9A9A9A]
                                       focus:border-[#1F5F8B]
                                       focus:ring-2 focus:ring-[#6BB6D6]/30
                                       focus:outline-none transition">
                        </div>
                    </div>
                    {{-- CONTRASEÑA --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">

                            <label
                                for="password"
                                class="block text-sm font-semibold text-[#343A40]">
                                Contraseña
                            </label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-solid fa-lock text-[#6BB6D6]"></i>
                            </div>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full rounded-xl border border-[#D9D4CA]
                                       bg-[#F8F6F1] py-3.5 pl-11 pr-12
                                       text-[#343A40]
                                       placeholder:text-[#9A9A9A]
                                       focus:border-[#1F5F8B]
                                       focus:ring-2 focus:ring-[#6BB6D6]/30
                                       focus:outline-none transition">
                            {{-- MOSTRAR / OCULTAR --}}
                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 flex items-center pr-4
                                       text-[#6B6F72] hover:text-[#1F5F8B] transition"
                                aria-label="Mostrar contraseña">
                                <i id="passwordIcon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    {{-- RECORDAR / RECUPERAR --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="rounded border-[#C9C3B8]
                                       bg-white text-[#1F5F8B]
                                       focus:ring-[#6BB6D6]">
                            <span class="ml-2 text-sm text-[#6B6F72]">
                                Recordarme
                            </span>
                        </label>
                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm font-medium text-[#1F5F8B]
                                       hover:text-[#C96F4A] transition">
                                ¿Has olvidado tu contraseña?
                            </a>
                        @endif
                    </div>
                    {{-- INICIAR SESIÓN --}}
                    <button
                        id="loginButton"
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2
                               rounded-xl bg-[#1F5F8B] px-4 py-3.5
                               text-sm font-semibold text-white shadow-lg
                               shadow-[#1F5F8B]/20
                               hover:bg-[#174B70]
                               hover:-translate-y-0.5
                               focus:outline-none
                               focus:ring-2
                               focus:ring-[#6BB6D6]/50
                               transition duration-200">
                        <i
                            id="loginIcon"
                            class="fa-solid fa-right-to-bracket"
                        ></i>
                        <span id="loginText">
                            Iniciar sesión
                        </span>
                    </button>
                </form>
                {{-- SEPARADOR --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-[#E5E0D8]"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-4 text-xs text-[#9A9A9A]">
                            O continúa con
                        </span>
                    </div>
                </div>
                {{-- GOOGLE --}}
                <button
                    type="button"
                    onclick="alert('La conexión con Google será configurada próximamente.')"
                    class="w-full flex items-center justify-center gap-3
                           rounded-xl border border-[#D9D4CA]
                           bg-white px-4 py-3
                           text-sm font-semibold text-[#343A40]
                           hover:bg-[#F8F6F1]
                           transition">
                    <i class="fa-brands fa-google text-[#DB4437]"></i>
                    Continuar con Google
                </button>
                {{-- CLAVE DE ACCESO --}}
                <button
                    type="button"
                    onclick="alert('El inicio de sesión con clave de acceso será configurado próximamente.')"
                    class="mt-3 w-full flex items-center justify-center gap-3
                           rounded-xl border border-[#D9D4CA]
                           bg-white px-4 py-3
                           text-sm font-semibold text-[#343A40]
                           hover:bg-[#F8F6F1]
                           transition">
                    <i class="fa-solid fa-key text-[#1F5F8B]"></i>
                    Iniciar sesión con clave de acceso
                </button>
                {{-- REGISTRO --}}
                @if (Route::has('register'))
                    <div class="mt-7 border-t border-[#E5E0D8] pt-6 text-center">
                        <p class="text-sm text-[#6B6F72]">
                            ¿Eres nuevo?
                            <a
                                href="{{ route('register') }}"
                                class="ml-1 font-semibold text-[#C96F4A]
                                       hover:text-[#A95538] transition">
                                Crear cuenta
                            </a>
                        </p>
                    </div>
                @endif
            </div>
            {{-- PIE --}}
            <div class="mt-6 text-center">
                <div class="flex items-center justify-center gap-2 text-xs text-[#6B6F72]">
                    <i class="fa-solid fa-location-dot text-[#C96F4A]"></i>
                    <span>
                        Descubre Bolivia, descubre nuevos destinos
                    </span>
                </div>
                <p class="mt-2 text-xs text-[#9A9A9A]">
                    © {{ date('Y') }} Pipocas
                </p>
            </div>
        </div>
    </div>
    {{-- FUNCIONES DEL LOGIN --}}
    <script>
        // Mostrar / ocultar contraseña
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('passwordIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        // Animación mientras se procesa el login
        function loginLoading() {
            const button = document.getElementById('loginButton');
            const icon = document.getElementById('loginIcon');
            const text = document.getElementById('loginText');
            button.disabled = true;
            button.classList.add(
                'opacity-75',
                'cursor-not-allowed'
            );
            icon.classList.remove('fa-right-to-bracket');
            icon.classList.add(
                'fa-spinner',
                'fa-spin'
            );
            text.textContent = 'Iniciando sesión...';
        }
    </script>
</x-guest-layout>