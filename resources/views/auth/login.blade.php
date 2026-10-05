<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#F8F6F1] p-4">
        {{-- Decoración de fondo --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -right-32 w-80 h-80 rounded-full bg-[#6BB6D6]/20 blur-3xl">
            </div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-[#C96F4A]/10 blur-3xl">
            </div>
        </div>
        {{-- Contenedor --}}
        <div class="relative z-10 w-full max-w-md">
            <div class="text-center mb-8">
                {{-- Icono --}}
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl
                            bg-[#1F5F8B] shadow-xl
                            shadow-[#1F5F8B]/20">
                    <i class="fa-solid fa-mountain-sun
                              text-3xl
                              text-[#F4C95D]">
                    </i>
                </div>
                {{-- Nombre --}}
                <h1 class="text-4xl font-bold tracking-tight text-[#343A40]">
                    <span class="text-[#1F5F8B]">
                        Pipocas
                    </span>
                </h1>
                <p class="mt-2 text-sm font-medium text-[#6B4F3A]">
                    Agencia de Viajes y Turismo
                </p>
                {{-- Separador --}}
                <div class="mt-5 flex items-center justify-center gap-3">
                    <span class="h-px w-10 bg-[#C96F4A]">
                    </span>
                    <i class="fa-solid fa-compass text-sm text-[#F4C95D]">
                    </i>
                    <span class="h-px w-10 bg-[#C96F4A]">
                    </span>
                </div>
            </div>
            {{-- TARJETA LOGIN --}}
            <div class="rounded-2xl border border-[#D9D4CA] bg-white p-8 shadow-xl shadow-[#6B4F3A]/10">
                {{-- Título --}}
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
                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500">
                            </i>
                            <ul class="text-sm text-red-600 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                {{-- MENSAJE DE SESIÓN --}}
                @if (session('status'))
                    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-green-600">
                            </i>
                            <p class="text-sm text-green-700">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                @endif
                {{-- FORMULARIO --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    {{-- CORREO --}}
                    <div>
                        <label for="email" class="block mb-2 text-sm font-semibold text-[#343A40]">
                            Correo electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-solid fa-envelope text-[#6BB6D6]">
                                </i>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="correo@gmail.com.com"

                                class="w-full rounded-xl border border-[#D9D4CA]
                                       bg-[#F8F6F1] py-3.5 pl-11 pr-4 text-[#343A40]
                                       placeholder:text-[#9A9A9A]
                                       focus:border-[#1F5F8B] focus:ring-2
                                       focus:ring-[#6BB6D6]/30 focus:outline-none transition">
                        </div>
                    </div>
                    {{-- CONTRASEÑA --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-semibold text-[#343A40]">
                                Contraseña
                            </label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-solid fa-lock text-[#6BB6D6]">
                                </i>
                            </div>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"

                                class="w-full rounded-xl border border-[#D9D4CA]
                                       bg-[#F8F6F1] py-3.5 pl-11 pr-4
                                       text-[#343A40]
                                       placeholder:text-[#9A9A9A]
                                       focus:border-[#1F5F8B] focus:ring-2
                                       focus:ring-[#6BB6D6]/30 focus:outline-none transition">
                        </div>
                    </div>
                    {{-- RECUPERAR --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"

                                class="rounded
                                       border-[#C9C3B8]
                                       bg-white
                                       text-[#1F5F8B]
                                       focus:ring-[#6BB6D6]">

                            <span class="ml-2 text-sm text-[#6B6F72]">
                                Recordarme
                            </span>
                        </label>
                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm font-medium
                                       text-[#1F5F8B]
                                       hover:text-[#C96F4A] transition">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>
                    {{-- BOTÓN INGRESAR --}}
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl
                               bg-[#1F5F8B] px-4 py-3.5 text-sm font-semibold text-white shadow-lg
                               shadow-[#1F5F8B]/20
                               hover:bg-[#174B70] hover:-translate-y-0.5 focus:outline-none focus:ring-2
                               focus:ring-[#6BB6D6]/50 transition duration-200">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Iniciar sesión
                    </button>
                </form>
                {{-- REGISTRO --}}
                @if (Route::has('register'))
                    <div class="mt-7 border-t border-[#E5E0D8] pt-6 text-center">
                        <p class="text-sm text-[#6B6F72]">
                            ¿Aún no tienes una cuenta?
                            <a
                                href="{{ route('register') }}"
                                class="ml-1 font-semibold
                                       text-[#C96F4A]
                                       hover:text-[#A95538] transition">
                                Regístrate
                            </a>
                        </p>
                    </div>
                @endif
            </div>
            {{-- PIE --}}
            <div class="mt-6 text-center">
                <div class="flex items-center justify-center gap-2 text-xs text-[#6B6F72]">
                    <i class="fa-solid fa-location-dot text-[#C96F4A]">
                    </i>
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
</x-guest-layout>