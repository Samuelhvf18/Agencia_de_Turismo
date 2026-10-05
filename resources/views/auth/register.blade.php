<x-guest-layout>
<div class="min-h-screen flex items-center justify-center bg-[#F8F6F1] p-4 relative overflow-hidden">
    {{-- Fondo decorativo --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#6BB6D6]/20 blur-3xl">
        </div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-[#C96F4A]/10 blur-3xl">
        </div>
    </div>
    <div class="relative z-10 w-full max-w-md">
        <div class="text-center mb-8">
            {{-- Icono --}}
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl
                        bg-[#1F5F8B] shadow-xl
                        shadow-[#1F5F8B]/20">
                <i class="fa-solid fa-mountain-sun text-3xl text-[#F4C95D]">
                </i>
            </div>
            {{-- Nombre --}}
            <h1 class="text-4xl font-bold tracking-tight text-[#343A40]">
                <span class="text-[#1F5F8B]">
                    Pipocas
                </span>
            </h1>

            {{-- Subtítulo --}}
            <p class="mt-2 text-sm font-medium text-[#6B4F3A]">
                Agencia de Viajes y Turismo
            </p>

            {{-- Separador --}}
            <div class="mx-auto mt-4 flex items-center justify-center gap-3">
                <span class="h-px w-10 bg-[#C96F4A]"></span>
                <i class="fa-solid fa-location-dot text-sm text-[#C96F4A]">
                </i>
                <span class="h-px w-10 bg-[#C96F4A]"></span>
            </div>
        </div>

        {{-- Tarjeta de registro --}}
        <div class="rounded-2xl bg-white border
                 border-[#E5DED5] shadow-xl
                 shadow-[#6B4F3A]/10 p-8">

            {{-- Título --}}
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
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-4">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
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
                        placeholder="Nombre"

                        class="w-full rounded-lg bg-[#F8F6F1] border
                             border-[#D9D2C9] px-4 py-3
                             text-[#343A40]
                             placeholder:text-[#9A9A9A] transition
                             focus:border-[#1F5F8B] focus:ring-2
                             focus:ring-[#1F5F8B]/20 focus:outline-none" >
                </div>
                {{-- Email --}}
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
                        autocomplete="username"
                        placeholder="correo@gmail.com"

                        class="w-full rounded-lg
                               bg-[#F8F6F1] border 
                               border-[#D9D2C9] px-4 py-3
                               text-[#343A40]
                               placeholder:text-[#9A9A9A] transition
                               focus:border-[#1F5F8B] focus:ring-2
                               focus:ring-[#1F5F8B]/20 focus:outline-none">
                </div>
                {{-- Contraseña --}}
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-[#343A40]">
                        Contraseña
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                     class="w-full rounded-lg
                      bg-[#F8F6F1] border 
                      border-[#D9D2C9] px-4 py-3 text-[#343A40] 
                      placeholder:text-[#9A9A9A] transition
                      focus:border-[#1F5F8B] focus:ring-2
                      focus:ring-[#1F5F8B]/20 focus:outline-none">
                </div>
                {{-- Confirmar contraseña --}}
                <div>
                    <label for="password_confirmation" class="block mb-2 text-sm font-medium text-[#343A40]">
                        Confirmar contraseña
                    </label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" class="w-full rounded-lg
                     bg-[#F8F6F1] border 
                     border-[#D9D2C9] px-4 py-3 text-[#343A40]
                     placeholder:text-[#9A9A9A] transition
                     focus:border-[#1F5F8B] focus:ring-2
                     focus:ring-[#1F5F8B]/20 focus:outline-none">
                </div>
                {{-- Términos y privacidad --}}
                <div class="text-center">
                    <p class="text-xs text-[#6B6F72]">
                         Al registrarte aceptas las condiciones de uso de Pipocas.
                   </p>
                </div>
                {{-- Botón --}}
                <button type="submit" class="w-full rounded-lg
                 bg-[#1F5F8B] px-4 py-3 text-sm font-semibold text-white shadow-lg
                 shadow-[#1F5F8B]/20 transition duration-200
                 hover:bg-[#174B70] hover:-translate-y-0.5 focus:outline-none focus:ring-2
                 focus:ring-[#1F5F8B]/40">
                    <i class="fa-solid fa-user-plus mr-2"></i>
                    Crear cuenta
                </button>
            </form>
            {{-- Ir al login --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-[#6B6F72]">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}" class="font-medium text-[#C96F4A] hover:text-[#A95536]">
                        Iniciar sesión
                    </a>
                </p>
            </div>
        </div>
        {{-- Pie --}}
        <div class="mt-6 text-center">
            <p class="text-xs text-[#6B6F72]">
                <i class="fa-solid fa-compass mr-1 text-[#F4C95D]">
                </i>
                Descubre nuevos destinos
            </p>
            <p class="mt-2 text-xs text-[#9A9A9A]">
                © {{ date('Y') }} Pipocas
            </p>
        </div>
    </div>
</div>
</x-guest-layout>