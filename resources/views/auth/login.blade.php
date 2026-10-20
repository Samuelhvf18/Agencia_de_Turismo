<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        .pipocas-login {
            font-family: 'Plus Jakarta Sans', sans-serif;
            --azul: #1F5F8B;
            --azul-profundo: #174870;
            --azul-claro: #457699;
            --terracota: #C96F4A;
            --dorado: #F4C95D;
            --fondo: #F0F0F1;
            --texto: #343A40;
            --secundario: #6B6F72;
            --radio: 24px;
        }

        .pipocas-login .font-editorial {
            font-family: 'Fraunces', Georgia, serif;
        }

        .pipocas-login .login-image {
            transition: transform 1.2s cubic-bezier(.2,.7,.2,1);
        }

        .pipocas-login .login-image:hover {
            transform: scale(1.035);
        }

        .pipocas-login .login-card {
            animation: pipocas-appear .7s cubic-bezier(.2,.7,.2,1) both;
        }

        .pipocas-login .login-decoration {
            animation: pipocas-float 7s ease-in-out infinite;
        }

        .pipocas-login .login-decoration-delay {
            animation-delay: -3s;
        }

        .pipocas-login .login-input {
            transition: border-color .25s ease, background .25s ease,
                        box-shadow .25s ease, transform .25s ease;
        }

        .pipocas-login .login-input:focus-within {
            transform: translateY(-1px);
            box-shadow: 0 0 0 4px rgb(31 95 139 / 9%);
        }

        .pipocas-login .login-submit {
            background-size: 200% 100%;
            transition: background-position .35s ease, transform .25s ease,
                        box-shadow .25s ease;
        }

        .pipocas-login .login-submit:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
        }

        .pipocas-login .login-submit:active {
            transform: translateY(0);
        }

        @keyframes pipocas-appear {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pipocas-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-9px); }
        }

        @media (prefers-reduced-motion: reduce) {
            .pipocas-login *,
            .pipocas-login *::before,
            .pipocas-login *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    <div class="pipocas-login relative min-h-screen overflow-hidden bg-[#F0F0F1] text-[#343A40]">
        <div class="pointer-events-none absolute -left-40 top-20 h-96 w-96 rounded-full bg-[#457699]/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 bottom-0 h-96 w-96 rounded-full bg-[#F4C95D]/20 blur-3xl"></div>

        <div class="grid min-h-screen lg:grid-cols-2">

            {{-- PANEL TURÍSTICO --}}
            <section class="relative hidden min-h-screen overflow-hidden bg-[#1F5F8B] lg:flex lg:flex-col lg:justify-between lg:p-10 xl:p-14">

                {{-- Fotografía de paisaje --}}
                <div class="absolute inset-0 overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1800&q=85"
                        alt=""
                        class="login-image h-full w-full object-cover opacity-45"
                        aria-hidden="true"
                    >
                    <div class="absolute inset-0 bg-gradient-to-br from-[#123C5B]/95 via-[#1F5F8B]/75 to-[#174870]/95"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#102F49]/80 via-transparent to-[#1F5F8B]/30"></div>
                </div>

                {{-- Decoración --}}
                <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full border-[50px] border-white/10"></div>
                <div class="pointer-events-none absolute -bottom-40 -left-32 h-[500px] w-[500px] rounded-full bg-[#457699]/50 blur-3xl"></div>
                <div class="pointer-events-none absolute right-12 top-1/3 h-40 w-40 rounded-full bg-[#C96F4A]/25 blur-3xl"></div>
                <div class="pointer-events-none absolute left-1/3 top-1/4 h-2 w-2 rounded-full bg-[#F4C95D] shadow-[0_0_24px_8px_rgba(244,201,93,.25)]"></div>

                {{-- Marca --}}
                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3" aria-label="Pipocas, página principal">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#F4C95D] shadow-lg shadow-black/10">
                            <i class="fa-solid fa-mountain-sun text-2xl text-[#1F5F8B]" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-extrabold tracking-tight text-white">Pipocas</h1>
                            <p class="text-[10px] font-bold tracking-[0.2em] text-white/70">
                                AGENCIA DE VIAJES Y TURISMO
                            </p>
                        </div>
                    </a>
                </div>

                {{-- Presentación visual --}}
                <div class="relative z-10 my-10 max-w-2xl">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold tracking-wide text-[#F4C95D] backdrop-blur-md">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#F4C95D] opacity-60"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-[#F4C95D]"></span>
                        </span>
                        TU PRÓXIMA AVENTURA COMIENZA AQUÍ
                    </div>

                    <h2 class="font-editorial max-w-xl text-5xl font-semibold leading-[1.12] tracking-tight text-white xl:text-6xl">
                        Hay lugares que se quedan
                        <span class="relative inline-block text-[#F4C95D]">
                            contigo.
                        </span>
                    </h2>

                    <p class="mt-6 max-w-lg text-base leading-8 text-white/80 xl:text-lg">
                        Vuelve a tu cuenta y sigue descubriendo los paisajes extraordinarios, la cultura y las experiencias únicas que Bolivia tiene para ti.
                    </p>

                    {{-- Composición fotográfica --}}
                    <div class="relative mt-9 h-[260px] max-w-xl xl:h-[290px]">

                        <div class="absolute bottom-0 left-0 right-16 top-0 overflow-hidden rounded-[28px] border border-white/20 bg-white/10 shadow-2xl shadow-black/20">
                            <img
                                src="https://images.unsplash.com/photo-1464278533981-50106e6176b1?auto=format&fit=crop&w=1200&q=85"
                                alt="Paisaje de montañas y naturaleza"
                                class="login-image h-full w-full object-cover"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-[#102F49]/80 via-transparent to-transparent"></div>

                            <div class="absolute bottom-5 left-5 right-5">
                                <div class="flex items-end justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#F4C95D]">
                                            Explora sin límites
                                        </p>
                                        <p class="font-editorial mt-1 text-2xl font-semibold text-white xl:text-3xl">
                                            Bolivia te espera
                                        </p>
                                    </div>
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-white/30 bg-white/15 text-white backdrop-blur-md">
                                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Imagen secundaria flotante --}}
                        <div class="login-decoration absolute -right-1 -top-5 h-32 w-32 overflow-hidden rounded-2xl border-4 border-[#1F5F8B] shadow-xl xl:-right-3 xl:h-36 xl:w-36">
                            <img
                                src="https://images.unsplash.com/photo-1472396961693-142e6e269027?auto=format&fit=crop&w=500&q=85"
                                alt="Naturaleza y aventura"
                                class="login-image h-full w-full object-cover"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-[#174870]/40 to-transparent"></div>
                            <div class="absolute bottom-2 left-2 flex h-7 w-7 items-center justify-center rounded-full bg-[#F4C95D] text-[#1F5F8B]">
                                <i class="fa-solid fa-compass text-xs" aria-hidden="true"></i>
                            </div>
                        </div>

                        {{-- Insignia flotante --}}
                        <div class="login-decoration login-decoration-delay absolute -bottom-4 right-0 flex items-center gap-3 rounded-2xl border border-white/20 bg-white/95 p-3 shadow-xl xl:-right-5">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4C95D]/25 text-[#1F5F8B]">
                                <i class="fa-solid fa-mountain-sun text-lg" aria-hidden="true"></i>
                            </div>
                            <div class="pr-2">
                                <p class="text-xs font-extrabold text-[#343A40]">Experiencias únicas</p>
                                <p class="mt-0.5 text-[11px] text-[#6B6F72]">Momentos para recordar</p>
                            </div>
                        </div>
                    </div>

                    {{-- Etiquetas --}}
                    <div class="mt-8 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-2 text-xs font-medium text-white/90 backdrop-blur-sm">
                            <i class="fa-solid fa-mountain text-[#F4C95D]" aria-hidden="true"></i>
                            Cordillera de los Andes
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-2 text-xs font-medium text-white/90 backdrop-blur-sm">
                            <i class="fa-solid fa-earth-americas text-[#F4C95D]" aria-hidden="true"></i>
                            Cultura boliviana
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-2 text-xs font-medium text-white/90 backdrop-blur-sm">
                            <i class="fa-solid fa-route text-[#F4C95D]" aria-hidden="true"></i>
                            Nuevos destinos
                        </span>
                    </div>
                </div>

                {{-- Pie de panel --}}
                <footer class="relative z-10 border-t border-white/15 pt-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-white/90">Vive La Paz. Descubre Bolivia.</p>
                            <p class="mt-1 text-xs text-white/55">
                                &copy; {{ date('Y') }} Pipocas · Agencia de Viajes y Turismo
                            </p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/5 text-[#F4C95D]">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        </div>
                    </div>
                </footer>
            </section>

            {{-- PANEL DE INICIO DE SESIÓN --}}
            <section class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-8 sm:px-8 lg:px-10 xl:px-14">

                {{-- Formas decorativas --}}
                <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#F4C95D]/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-[#C96F4A]/10 blur-3xl"></div>
                <div class="pointer-events-none absolute right-10 top-16 hidden h-16 w-16 rounded-full border border-[#1F5F8B]/10 sm:block"></div>
                <div class="pointer-events-none absolute bottom-20 right-12 hidden h-3 w-3 rounded-full bg-[#C96F4A]/40 sm:block"></div>

                <main class="relative z-10 w-full max-w-[440px]">

                    {{-- Marca móvil --}}
                    <header class="mb-7 text-center lg:hidden">
                        <a href="{{ url('/') }}" class="inline-block" aria-label="Pipocas, página principal">
                            <div class="relative mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#1F5F8B] shadow-lg shadow-[#1F5F8B]/20">
                                <div class="absolute inset-1 rounded-xl border border-white/15"></div>
                                <i class="fa-solid fa-mountain-sun text-2xl text-[#F4C95D]" aria-hidden="true"></i>
                            </div>
                            <h1 class="text-3xl font-extrabold tracking-tight text-[#1F5F8B]">Pipocas</h1>
                            <p class="mt-1 text-[10px] font-bold uppercase tracking-[0.2em] text-[#6B4F3A]">
                                Agencia de Viajes y Turismo
                            </p>
                        </a>
                    </header>

                    <section class="login-card overflow-hidden rounded-[28px] border border-white/80 bg-white/95 shadow-2xl shadow-[#343A40]/10 backdrop-blur-xl">

                        {{-- Línea decorativa superior --}}
                        <div class="h-1.5 bg-gradient-to-r from-[#1F5F8B] via-[#F4C95D] to-[#C96F4A]"></div>

                        <div class="p-6 sm:p-9">

                            {{-- Encabezado del formulario --}}
                            <div class="mb-7">
                                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1F5F8B]/[0.08] text-[#1F5F8B]">
                                    <i class="fa-solid fa-right-to-bracket text-xl" aria-hidden="true"></i>
                                </div>

                                <p class="mb-2 text-[11px] font-extrabold uppercase tracking-[0.2em] text-[#C96F4A]">
                                    Tu próxima aventura
                                </p>

                                <h2 class="font-editorial text-3xl font-semibold leading-tight tracking-tight text-[#343A40] sm:text-[2.15rem]">
                                    ¡Qué bueno tenerte de vuelta!
                                </h2>

                                <p class="mt-3 text-sm leading-6 text-[#6B6F72]">
                                    Inicia sesión para gestionar tus viajes y seguir descubriendo nuevos destinos.
                                </p>
                            </div>

                            {{-- Errores de Laravel --}}
                            @if ($errors->any())
                                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4"
                                     role="alert" aria-live="polite">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100">
                                            <i class="fa-solid fa-circle-exclamation text-red-500" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 text-sm font-bold text-red-700">
                                                No se pudo iniciar sesión
                                            </p>
                                            <ul class="space-y-1 text-sm text-red-600">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Mensajes de estado --}}
                            @if (session('status'))
                                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 p-4"
                                     role="status" aria-live="polite">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-green-100">
                                        <i class="fa-solid fa-circle-check text-green-600" aria-hidden="true"></i>
                                    </div>
                                    <p class="pt-1.5 text-sm leading-5 text-green-700">{{ session('status') }}</p>
                                </div>
                            @endif

                            {{-- Inicio con Google --}}
                            @if (Route::has('auth.google.redirect'))
                                <a href="{{ route('auth.google.redirect') }}"
                                   class="group flex w-full items-center justify-center gap-3 rounded-2xl border border-[#457699]/20 bg-white px-4 py-3.5 text-sm font-bold text-[#343A40] shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-[#1F5F8B]/40 hover:bg-[#F0F0F1]/70 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#1F5F8B]/15">

                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" transform="translate(0 4)"/>
                                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.76 7.18l7.73 6C44.42 38.05 46.98 31.95 46.98 24.55z"/>
                                        <path fill="#FBBC05" d="M10.53 28.59a14.4 14.4 0 0 1 0-9.18l-7.98-6.19a24 24 0 0 0 0 21.56l7.98-6.19z" transform="translate(0 4)"/>
                                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.91-5.8l-7.73-6c-2.15 1.45-4.91 2.3-8.18 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" transform="translate(0 -4)"/>
                                    </svg>

                                    <span>Continuar con Google</span>

                                    <i class="fa-solid fa-arrow-right ml-auto text-xs text-[#6B6F72] transition group-hover:translate-x-1 group-hover:text-[#1F5F8B]" aria-hidden="true"></i>
                                </a>

                                <div class="my-6 flex items-center gap-4">
                                    <span class="h-px flex-1 bg-[#457699]/20"></span>
                                    <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-[0.15em] text-[#6B6F72]">
                                        O ingresa con tu correo
                                    </span>
                                    <span class="h-px flex-1 bg-[#457699]/20"></span>
                                </div>
                            @endif

                            {{-- FORMULARIO ORIGINAL: LÓGICA CONSERVADA --}}
                            <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-5">
                                @csrf

                                <div>
                                    <label for="cor" class="mb-2 block text-sm font-bold text-[#343A40]">
                                        Correo electrónico
                                    </label>

                                    <div class="login-input relative rounded-2xl border {{ $errors->has('cor') ? 'border-red-400' : 'border-[#457699]/25' }} bg-[#F0F0F1]/60 focus-within:border-[#1F5F8B] focus-within:bg-white">
                                        <i class="fa-solid fa-envelope pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#457699]" aria-hidden="true"></i>

                                        <input
                                            id="cor"
                                            type="email"
                                            name="cor"
                                            value="{{ old('cor') }}"
                                            required
                                            autofocus
                                            autocomplete="username"
                                            inputmode="email"
                                            maxlength="255"
                                            placeholder="correo@ejemplo.com"
                                            aria-invalid="{{ $errors->has('cor') ? 'true' : 'false' }}"
                                            class="w-full rounded-2xl border-0 bg-transparent py-3.5 pl-11 pr-4 text-sm text-[#343A40] placeholder:text-[#6B6F72]/70 focus:outline-none focus:ring-0">
                                    </div>

                                    @error('cor')
                                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="mb-2 block text-sm font-bold text-[#343A40]">
                                        Contraseña
                                    </label>

                                    <div class="login-input relative rounded-2xl border {{ $errors->has('password') ? 'border-red-400' : 'border-[#457699]/25' }} bg-[#F0F0F1]/60 focus-within:border-[#1F5F8B] focus-within:bg-white">
                                        <i class="fa-solid fa-lock pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#457699]" aria-hidden="true"></i>

                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            required
                                            autocomplete="current-password"
                                            placeholder="Ingresa tu contraseña"
                                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                            class="w-full rounded-2xl border-0 bg-transparent py-3.5 pl-11 pr-12 text-sm text-[#343A40] placeholder:text-[#6B6F72]/70 focus:outline-none focus:ring-0">

                                        <button
                                            type="button"
                                            id="togglePassword"
                                            class="absolute inset-y-0 right-0 flex items-center rounded-r-2xl px-4 text-[#6B6F72] transition hover:text-[#1F5F8B] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#1F5F8B]/30"
                                            aria-label="Mostrar contraseña"
                                            aria-pressed="false">
                                            <i id="passwordIcon" class="fa-solid fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    @error('password')
                                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                    <label for="remember_me" class="flex cursor-pointer items-center gap-2.5">
                                        <input
                                            id="remember_me"
                                            type="checkbox"
                                            name="remember"
                                            value="1"
                                            @checked(old('remember'))
                                            class="h-4 w-4 rounded border-[#457699]/30 bg-white text-[#1F5F8B] focus:ring-2 focus:ring-[#1F5F8B]">
                                        <span class="text-sm text-[#6B6F72]">Recordarme</span>
                                    </label>

                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}"
                                           class="text-xs font-bold text-[#1F5F8B] transition hover:text-[#C96F4A] hover:underline sm:text-sm">
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    @endif
                                </div>

                                <button
                                    type="submit"
                                    id="loginButton"
                                    class="login-submit group flex w-full items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-r from-[#1F5F8B] via-[#1F5F8B] to-[#457699] px-4 py-4 text-sm font-extrabold text-white shadow-lg shadow-[#1F5F8B]/20 focus:outline-none focus:ring-4 focus:ring-[#1F5F8B]/25 disabled:cursor-not-allowed disabled:opacity-70">

                                    <i id="loginIcon" class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                                    <span id="loginText">Iniciar sesión</span>
                                    <i class="fa-solid fa-arrow-right ml-auto text-xs text-[#F4C95D] transition group-hover:translate-x-1" aria-hidden="true"></i>
                                </button>
                            </form>

                            {{-- Registro --}}
                            @if (Route::has('register'))
                                <div class="mt-7 border-t border-[#457699]/15 pt-6 text-center">
                                    <p class="text-sm text-[#6B6F72]">
                                        ¿Todavía no tienes una cuenta?
                                    </p>

                                    <a href="{{ route('register') }}"
                                       class="mt-2 inline-flex items-center gap-2 font-extrabold text-[#C96F4A] transition hover:gap-3 hover:text-[#6B4F3A] hover:underline">
                                        Crear una cuenta
                                        <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Franja de confianza --}}
                        <div class="flex items-center justify-center gap-3 border-t border-[#457699]/15 bg-[#F0F0F1]/70 px-5 py-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#1F5F8B]/10">
                                <i class="fa-solid fa-shield-halved text-sm text-[#1F5F8B]" aria-hidden="true"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#343A40]">Tu información está protegida</p>
                                <p class="mt-0.5 text-[10px] text-[#6B6F72]">Accede a tu cuenta de forma segura.</p>
                            </div>
                        </div>
                    </section>

                    {{-- Pie móvil --}}
                    <footer class="mt-6 text-center lg:hidden">
                        <p class="text-xs font-semibold text-[#6B4F3A]">
                            <i class="fa-solid fa-location-dot mr-1 text-[#C96F4A]" aria-hidden="true"></i>
                            Hecho para descubrir Bolivia
                        </p>
                        <p class="mt-1 text-[11px] text-[#6B6F72]">
                            &copy; {{ date('Y') }} Pipocas · La Paz, Bolivia
                        </p>
                    </footer>
                </main>
            </section>
        </div>
    </div>

    {{-- SCRIPT ORIGINAL: FUNCIONALIDAD CONSERVADA --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const password = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const passwordIcon = document.getElementById('passwordIcon');
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const loginIcon = document.getElementById('loginIcon');
            const loginText = document.getElementById('loginText');

            if (password && togglePassword && passwordIcon) {
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
            }

            if (loginForm && loginButton) {
                loginForm.addEventListener('submit', function (event) {
                    if (!loginForm.checkValidity()) {
                        event.preventDefault();
                        loginForm.reportValidity();
                        return;
                    }
                    if (loginButton.disabled) {
                        event.preventDefault();
                        return;
                    }
                    loginButton.disabled = true;
                    if (loginIcon) {
                        loginIcon.classList.remove('fa-right-to-bracket');
                        loginIcon.classList.add('fa-spinner', 'fa-spin');
                    }
                    if (loginText) {
                        loginText.textContent = 'Iniciando sesión...';
                    }
                });
            }
        });
    </script>
</x-guest-layout>