<x-guest-layout>
<div class="min-h-screen flex items-center justify-center bg-[#F8F6F1] p-4 relative overflow-hidden">
    {{-- Fondo decorativo --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#6BB6D6]/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-[#C96F4A]/10 blur-3xl"></div>
    </div>
    <div class="relative z-10 w-full max-w-xl">
        {{-- Encabezado --}}
        <div class="text-center mb-8">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl
                        bg-[#1F5F8B] shadow-xl shadow-[#1F5F8B]/20">
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
        <div class="rounded-2xl bg-white border border-[#E5DED5]
                    shadow-xl shadow-[#6B4F3A]/10 p-8">
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
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                {{-- Nombre + apellido paterno --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nom"
                               class="block mb-2 text-sm font-medium text-[#343A40]">
                            Nombre *
                        </label>
                        <input
                            id="nom"
                            type="text"
                            name="nom"
                            value="{{ old('nom') }}"
                            required
                            autofocus
                            autocomplete="given-name"
                            placeholder="Tu nombre"
                            class="w-full rounded-lg bg-[#F8F6F1] border border-[#D9D2C9]
                                   px-4 py-3 text-[#343A40] placeholder:text-[#9A9A9A]
                                   focus:border-[#1F5F8B] focus:ring-2
                                   focus:ring-[#1F5F8B]/20 focus:outline-none">
                    </div>
                    <div>
                        <label for="pat"
                               class="block mb-2 text-sm font-medium text-[#343A40]">
                            Apellido paterno *
                        </label>
                        <input
                            id="pat"
                            type="text"
                            name="pat"
                            value="{{ old('pat') }}"
                            required
                            autocomplete="family-name"
                            placeholder="Apellido paterno"
                            class="w-full rounded-lg bg-[#F8F6F1] border border-[#D9D2C9]
                                   px-4 py-3 text-[#343A40] placeholder:text-[#9A9A9A]
                                   focus:border-[#1F5F8B] focus:ring-2
                                   focus:ring-[#1F5F8B]/20 focus:outline-none">
                    </div>
                </div>
                {{-- Apellido materno + usuario --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="mat"
                               class="block mb-2 text-sm font-medium text-[#343A40]">
                            Apellido materno
                        </label>
                        <input
                            id="mat"
                            type="text"
                            name="mat"
                            value="{{ old('mat') }}"
                            autocomplete="additional-name"
                            placeholder="Apellido materno"
                            class="w-full rounded-lg bg-[#F8F6F1] border border-[#D9D2C9]
                                   px-4 py-3 text-[#343A40] placeholder:text-[#9A9A9A]
                                   focus:border-[#1F5F8B] focus:ring-2
                                   focus:ring-[#1F5F8B]/20 focus:outline-none">
                    </div>
                    <div>
                        <label for="usr"
                               class="block mb-2 text-sm font-medium text-[#343A40]">
                            Nombre de usuario
                        </label>
                        <input
                            id="usr"
                            type="text"
                            name="usr"
                            value="{{ old('usr') }}"
                            autocomplete="username"
                            placeholder=" "
                            class="w-full rounded-lg bg-[#F8F6F1] border border-[#D9D2C9]
                                   px-4 py-3 text-[#343A40] placeholder:text-[#9A9A9A]
                                   focus:border-[#1F5F8B] focus:ring-2
                                   focus:ring-[#1F5F8B]/20 focus:outline-none">

                        <p class="mt-1 text-xs text-[#9A9A9A]">
                            Opcional. Solo letras, números, - y _.
                        </p>
                    </div>
                </div>
                {{-- Correo --}}
                <div>
                    <label for="cor"
                           class="block mb-2 text-sm font-medium text-[#343A40]">
                        Correo electrónico *
                    </label>
                    <input
                        id="cor"
                        type="email"
                        name="cor"
                        value="{{ old('cor') }}"
                        required
                        autocomplete="email"
                        placeholder="correo@gmail.com"
                        class="w-full rounded-lg bg-[#F8F6F1] border border-[#D9D2C9]
                               px-4 py-3 text-[#343A40] placeholder:text-[#9A9A9A]
                               focus:border-[#1F5F8B] focus:ring-2
                               focus:ring-[#1F5F8B]/20 focus:outline-none">
                </div>
                {{-- Contraseña --}}
                <div>
                    <label for="password"
                           class="block mb-2 text-sm font-medium text-[#343A40]">
                        Contraseña *
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
                            class="w-full rounded-lg bg-[#F8F6F1] border border-[#D9D2C9]
                                   px-4 py-3 pr-12 text-[#343A40] placeholder:text-[#9A9A9A]
                                   focus:border-[#1F5F8B] focus:ring-2
                                   focus:ring-[#1F5F8B]/20 focus:outline-none">
                        <button
                            type="button"
                            onclick="togglePassword('password', 'passwordIcon')"
                            class="absolute inset-y-0 right-0 flex items-center pr-4
                                   text-[#6B6F72] hover:text-[#1F5F8B]">
                            <i id="passwordIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    {{-- Indicador de seguridad --}}
                    <div class="mt-2">
                        <div class="flex gap-1 h-1.5">
                            <span id="bar1"
                                  class="flex-1 rounded-full bg-[#E5E0D8]"></span>
                            <span id="bar2"
                                  class="flex-1 rounded-full bg-[#E5E0D8]"></span>
                            <span id="bar3"
                                  class="flex-1 rounded-full bg-[#E5E0D8]"></span>
                            <span id="bar4"
                                  class="flex-1 rounded-full bg-[#E5E0D8]"></span>
                        </div>
                        <p class="mt-1 text-xs text-[#6B6F72]">
                            Seguridad:
                            <span id="strengthText" class="font-medium">
                                Sin contraseña
                            </span>
                        </p>
                    </div>
                </div>
                {{-- Confirmar contraseña --}}
                <div>
                    <label for="password_confirmation"
                           class="block mb-2 text-sm font-medium text-[#343A40]">
                        Confirmar contraseña *
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
                            class="w-full rounded-lg bg-[#F8F6F1] border border-[#D9D2C9]
                                   px-4 py-3 pr-12 text-[#343A40] placeholder:text-[#9A9A9A]
                                   focus:border-[#1F5F8B] focus:ring-2
                                   focus:ring-[#1F5F8B]/20 focus:outline-none">
                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', 'confirmationIcon')"
                            class="absolute inset-y-0 right-0 flex items-center pr-4
                                   text-[#6B6F72] hover:text-[#1F5F8B]">
                            <i id="confirmationIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <p id="matchText" class="mt-1 text-xs"></p>
                </div>
                {{-- Términos --}}
                <div class="text-center">
                    <p class="text-xs text-[#6B6F72]">
                        Al registrarte aceptas las condiciones de uso de Pipocas.
                    </p>
                </div>
                {{-- Crear cuenta --}}
                <button
                    type="submit"
                    class="w-full rounded-lg bg-[#1F5F8B] px-4 py-3
                           text-sm font-semibold text-white shadow-lg
                           shadow-[#1F5F8B]/20 transition duration-200
                           hover:bg-[#174B70] hover:-translate-y-0.5
                           focus:outline-none focus:ring-2
                           focus:ring-[#1F5F8B]/40">
                    <i class="fa-solid fa-user-plus mr-2"></i>
                    Crear cuenta
                </button>
            </form>
            {{-- Separador --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-[#E5DED5]"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-4 text-xs text-[#9A9A9A]">
                        O también puedes
                    </span>
                </div>
            </div>
            {{-- Google --}}
            <button
                type="button"
                onclick="alert('La conexión con Google será configurada próximamente.')"
                class="w-full flex items-center justify-center gap-3 rounded-lg
                       border border-[#D9D2C9] bg-white px-4 py-3
                       text-sm font-semibold text-[#343A40]
                       transition duration-200
                       hover:bg-[#F8F6F1] hover:border-[#BDB5AB]
                       focus:outline-none focus:ring-2
                       focus:ring-[#1F5F8B]/20">
                <i class="fa-brands fa-google text-[#4285F4] text-lg"></i>
                Continuar con Google
            </button>
            {{-- Login --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-[#6B6F72]">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}"
                       class="font-medium text-[#C96F4A] hover:text-[#A95536]">
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
<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
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
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[A-Z]/.test(password) && /[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password)) strength++;
    const levels = [
        'Muy débil',
        'Débil',
        'Media',
        'Fuerte',
        'Muy fuerte'
    ];
    text.textContent = levels[strength];
    for (let i = 0; i < strength; i++) {
        bars[i].className =
            'flex-1 rounded-full bg-[#1F5F8B] transition-all duration-300';
    }
}
function checkPasswordMatch() {
    const password =
        document.getElementById('password').value;
    const confirmation =
        document.getElementById('password_confirmation').value;
    const text =
        document.getElementById('matchText');
    if (confirmation.length === 0) {
        text.textContent = '';
        return;
    }
    if (password === confirmation) {
        text.textContent =
            '✓ Las contraseñas coinciden';
        text.className =
            'mt-1 text-xs text-green-600';
    } else {
        text.textContent =
            '✕ Las contraseñas no coinciden';
        text.className =
            'mt-1 text-xs text-red-500';
    }
}
</script>
</x-guest-layout>