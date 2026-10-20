<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        .pipocas-register {
            font-family: 'Plus Jakarta Sans', sans-serif;
            --azul: #1F5F8B;
            --azul-profundo: #174870;
            --azul-claro: #457699;
            --terracota: #C96F4A;
            --dorado: #F4C95D;
            --fondo: #F0F0F1;
            --texto: #343A40;
            --secundario: #6B6F72;
        }

        .pipocas-register .font-editorial {
            font-family: 'Fraunces', Georgia, serif;
        }

        .pipocas-register .register-image {
            transition: transform 1.2s cubic-bezier(.2,.7,.2,1);
        }

        .pipocas-register .register-image:hover {
            transform: scale(1.035);
        }

        .pipocas-register .register-card {
            animation: pipocas-register-appear .7s cubic-bezier(.2,.7,.2,1) both;
        }

        .pipocas-register .register-float {
            animation: pipocas-register-float 7s ease-in-out infinite;
        }

        .pipocas-register .register-float-delay {
            animation-delay: -3s;
        }

        .pipocas-register .register-input {
            transition: border-color .25s ease, background .25s ease,
                        box-shadow .25s ease, transform .25s ease;
        }

        .pipocas-register .register-input:focus-within {
            transform: translateY(-1px);
            box-shadow: 0 0 0 4px rgb(31 95 139 / 9%);
        }

        .pipocas-register .register-submit {
            background-size: 200% 100%;
            transition: background-position .35s ease, transform .25s ease,
                        box-shadow .25s ease;
        }

        .pipocas-register .register-submit:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
        }

        @keyframes pipocas-register-appear {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pipocas-register-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-9px); }
        }

        @media (prefers-reduced-motion: reduce) {
            .pipocas-register *,
            .pipocas-register *::before,
            .pipocas-register *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>

    <div class="pipocas-register relative min-h-screen overflow-hidden bg-[#F0F0F1] text-[#343A40]">
        <div class="pointer-events-none absolute -left-40 top-20 h-96 w-96 rounded-full bg-[#457699]/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-40 bottom-0 h-96 w-96 rounded-full bg-[#F4C95D]/20 blur-3xl"></div>

        <div class="grid min-h-screen lg:grid-cols-2">

            {{-- PANEL TURÍSTICO --}}
            <section class="relative hidden min-h-screen overflow-hidden bg-[#1F5F8B] lg:flex lg:flex-col lg:justify-between lg:p-10 xl:p-14">

                {{-- Fondo fotográfico --}}
                <div class="absolute inset-0 overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1800&q=85"
                        alt=""
                        class="register-image h-full w-full object-cover opacity-45"
                        aria-hidden="true"
                    >
                    <div class="absolute inset-0 bg-gradient-to-br from-[#123C5B]/95 via-[#1F5F8B]/75 to-[#174870]/95"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#102F49]/80 via-transparent to-[#1F5F8B]/30"></div>
                </div>

                {{-- Elementos decorativos --}}
                <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full border-[50px] border-white/10"></div>
                <div class="pointer-events-none absolute -bottom-40 -left-32 h-[500px] w-[500px] rounded-full bg-[#457699]/50 blur-3xl"></div>
                <div class="pointer-events-none absolute right-12 top-1/3 h-40 w-40 rounded-full bg-[#C96F4A]/25 blur-3xl"></div>
                <div class="pointer-events-none absolute left-1/3 top-1/4 h-2 w-2 rounded-full bg-[#F4C95D] shadow-[0_0_24px_8px_rgba(244,201,93,.25)]"></div>

                {{-- Marca --}}
                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
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

                {{-- Presentación --}}
                <div class="relative z-10 my-10 max-w-2xl">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold tracking-wide text-[#F4C95D] backdrop-blur-md">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#F4C95D] opacity-60"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-[#F4C95D]"></span>
                        </span>
                        TU AVENTURA COMIENZA AQUÍ
                    </div>

                    <h2 class="font-editorial max-w-xl text-5xl font-semibold leading-[1.12] tracking-tight text-white xl:text-6xl">
                        Tu próxima historia comienza en
                        <span class="text-[#F4C95D]">Bolivia.</span>
                    </h2>

                    <p class="mt-6 max-w-lg text-base leading-8 text-white/80 xl:text-lg">
                        Crea tu cuenta y prepárate para descubrir paisajes extraordinarios, conocer nuevas culturas y vivir experiencias que recordarás siempre.
                    </p>

                    {{-- Composición fotográfica --}}
                    <div class="relative mt-9 h-[250px] max-w-xl xl:h-[275px]">
                        <div class="absolute bottom-0 left-0 right-16 top-0 overflow-hidden rounded-[28px] border border-white/20 bg-white/10 shadow-2xl shadow-black/20">
                            <img
                                src="https://images.unsplash.com/photo-1464278533981-50106e6176b1?auto=format&fit=crop&w=1200&q=85"
                                alt="Paisaje de montañas y naturaleza"
                                class="register-image h-full w-full object-cover"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-[#102F49]/85 via-transparent to-transparent"></div>

                            <div class="absolute bottom-5 left-5 right-5">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#F4C95D]">
                                    Una nueva aventura
                                </p>
                                <p class="font-editorial mt-1 text-2xl font-semibold text-white xl:text-3xl">
                                    Empieza tu viaje
                                </p>
                                <p class="mt-1 text-sm text-white/80">
                                    Descubre Bolivia a tu manera.
                                </p>
                            </div>
                        </div>

                        {{-- Imagen secundaria --}}
                        <div class="register-float absolute -right-1 -top-5 h-32 w-32 overflow-hidden rounded-2xl border-4 border-[#1F5F8B] shadow-xl xl:-right-3 xl:h-36 xl:w-36">
                            <img
                                src="https://images.unsplash.com/photo-1472396961693-142e6e269027?auto=format&fit=crop&w=500&q=85"
                                alt="Naturaleza y aventura"
                                class="register-image h-full w-full object-cover"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-[#174870]/40 to-transparent"></div>
                            <div class="absolute bottom-2 left-2 flex h-7 w-7 items-center justify-center rounded-full bg-[#F4C95D] text-[#1F5F8B]">
                                <i class="fa-solid fa-compass text-xs" aria-hidden="true"></i>
                            </div>
                        </div>

                        {{-- Insignia flotante --}}
                        <div class="register-float register-float-delay absolute -bottom-4 right-0 flex items-center gap-3 rounded-2xl border border-white/20 bg-white/95 p-3 shadow-xl xl:-right-5">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F4C95D]/25 text-[#1F5F8B]">
                                <i class="fa-solid fa-earth-americas text-lg" aria-hidden="true"></i>
                            </div>
                            <div class="pr-2">
                                <p class="text-xs font-extrabold text-[#343A40]">Tu viaje comienza aquí</p>
                                <p class="mt-0.5 text-[11px] text-[#6B6F72]">Crea nuevos recuerdos</p>
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

            {{-- PANEL DE REGISTRO --}}
            <section class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-8 sm:px-8 lg:px-10 xl:px-14">

                <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#F4C95D]/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-[#C96F4A]/10 blur-3xl"></div>
                <div class="pointer-events-none absolute right-10 top-16 hidden h-16 w-16 rounded-full border border-[#1F5F8B]/10 sm:block"></div>
                <div class="pointer-events-none absolute bottom-20 right-12 hidden h-3 w-3 rounded-full bg-[#C96F4A]/40 sm:block"></div>

                <main class="relative z-10 w-full max-w-[440px]">

                    {{-- Marca móvil --}}
                    <header class="mb-7 text-center lg:hidden">
                        <a href="{{ url('/') }}" class="inline-block">
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

                    <section class="register-card overflow-hidden rounded-[28px] border border-white/80 bg-white/95 shadow-2xl shadow-[#343A40]/10 backdrop-blur-xl">
                        <div class="h-1.5 bg-gradient-to-r from-[#1F5F8B] via-[#F4C95D] to-[#C96F4A]"></div>

                        <div class="p-6 sm:p-9">

                            {{-- Encabezado --}}
                            <div class="mb-7">
                                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1F5F8B]/[0.08] text-[#1F5F8B]">
                                    <i class="fa-solid fa-user-plus text-xl" aria-hidden="true"></i>
                                </div>

                                <p class="mb-2 text-[11px] font-extrabold uppercase tracking-[0.2em] text-[#C96F4A]">
                                    Únete a nuestra comunidad
                                </p>

                                <h2 class="font-editorial text-3xl font-semibold leading-tight tracking-tight text-[#343A40] sm:text-[2.15rem]">
                                    Crea tu cuenta
                                </h2>

                                <p class="mt-3 text-sm leading-6 text-[#6B6F72]">
                                    Regístrate y comienza a planificar tu próxima aventura por Bolivia.
                                </p>
                            </div>

                            {{-- Errores de Laravel --}}
                            @if ($errors->any())
                                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4" role="alert">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100">
                                            <i class="fa-solid fa-circle-exclamation text-red-500" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 text-sm font-bold text-red-700">
                                                No se pudo completar el registro
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

                            {{-- Registro con Google --}}
                            <a href="{{ route('auth.google.redirect') }}"
                               class="group flex w-full items-center justify-center gap-3 rounded-2xl border border-[#457699]/20 bg-white px-4 py-3.5 text-sm font-bold text-[#343A40] shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-[#1F5F8B]/40 hover:bg-[#F0F0F1]/70 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#1F5F8B]/15">

                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" transform="translate(0 4)"/>
                                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.74 7.18l7.68 5.96c4.48-4.14 7.1-10.25 7.1-17.61z"/>
                                    <path fill="#FBBC05" d="M10.53 28.59A14.4 14.4 0 0 1 9.75 24c0-1.59.27-3.13.76-4.59l-7.98-6.19A23.9 23.9 0 0 0 0 24c0 3.87.93 7.53 2.56 10.78l7.97-6.19z" transform="translate(1)"/>
                                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.9-5.84l-7.68-5.96c-2.13 1.43-4.86 2.28-8.22 2.28-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" transform="translate(0 -2)"/>
                                </svg>

                                <span>Registrarse con Google</span>

                                <i class="fa-solid fa-arrow-right ml-auto text-xs text-[#6B6F72] transition group-hover:translate-x-1 group-hover:text-[#1F5F8B]" aria-hidden="true"></i>
                            </a>

                            <div class="my-6 flex items-center gap-4">
                                <span class="h-px flex-1 bg-[#457699]/20"></span>
                                <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-[0.15em] text-[#6B6F72]">
                                    O con tu correo
                                </span>
                                <span class="h-px flex-1 bg-[#457699]/20"></span>
                            </div>

                            {{-- FORMULARIO ORIGINAL --}}
                            <form id="registerForm" method="POST" action="{{ route('register') }}" class="space-y-5">
                                @csrf

                                <div>
                                    <label for="nom" class="mb-2 block text-sm font-bold text-[#343A40]">
                                        Nombre completo
                                    </label>

                                    <div class="register-input relative rounded-2xl border {{ $errors->has('nom') ? 'border-red-400' : 'border-[#457699]/25' }} bg-[#F0F0F1]/60 focus-within:border-[#1F5F8B] focus-within:bg-white">
                                        <i class="fa-regular fa-user pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#457699]" aria-hidden="true"></i>

                                        <input
                                            id="nom"
                                            type="text"
                                            name="nom"
                                            value="{{ old('nom') }}"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            maxlength="255"
                                            placeholder="Nombre completo"
                                            class="w-full rounded-2xl border-0 bg-transparent py-3.5 pl-11 pr-4 text-sm text-[#343A40] placeholder:text-[#6B6F72]/70 focus:outline-none focus:ring-0">
                                    </div>

                                    @error('nom')
                                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="cor" class="mb-2 block text-sm font-bold text-[#343A40]">
                                        Correo electrónico
                                    </label>

                                    <div class="register-input relative rounded-2xl border {{ $errors->has('cor') ? 'border-red-400' : 'border-[#457699]/25' }} bg-[#F0F0F1]/60 focus-within:border-[#1F5F8B] focus-within:bg-white">
                                        <i class="fa-regular fa-envelope pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#457699]" aria-hidden="true"></i>

                                        <input
                                            id="cor"
                                            type="email"
                                            name="cor"
                                            value="{{ old('cor') }}"
                                            required
                                            autocomplete="email"
                                            maxlength="255"
                                            placeholder="correo@gmail.com"
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

                                    <div class="register-input relative rounded-2xl border {{ $errors->has('password') ? 'border-red-400' : 'border-[#457699]/25' }} bg-[#F0F0F1]/60 focus-within:border-[#1F5F8B] focus-within:bg-white">
                                        <i class="fa-solid fa-lock pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#457699]" aria-hidden="true"></i>

                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            required
                                            minlength="5"
                                            autocomplete="new-password"
                                            placeholder="Crea una contraseña segura"
                                            aria-describedby="strengthText"
                                            class="w-full rounded-2xl border-0 bg-transparent py-3.5 pl-11 pr-12 text-sm text-[#343A40] placeholder:text-[#6B6F72]/70 focus:outline-none focus:ring-0">

                                        <button type="button"
                                                data-toggle-password="password"
                                                class="absolute inset-y-0 right-0 flex items-center rounded-r-2xl px-4 text-[#6B6F72] transition hover:text-[#1F5F8B] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#1F5F8B]/30"
                                                aria-label="Mostrar contraseña"
                                                aria-pressed="false">
                                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <div class="mt-3 rounded-xl border border-[#457699]/10 bg-[#F0F0F1]/60 p-3" aria-live="polite">
                                        <div class="flex h-1.5 gap-1.5">
                                            <span data-strength-bar class="flex-1 rounded-full bg-[#457699]/20 transition-colors"></span>
                                            <span data-strength-bar class="flex-1 rounded-full bg-[#457699]/20 transition-colors"></span>
                                            <span data-strength-bar class="flex-1 rounded-full bg-[#457699]/20 transition-colors"></span>
                                            <span data-strength-bar class="flex-1 rounded-full bg-[#457699]/20 transition-colors"></span>
                                        </div>

                                        <p class="mt-2 text-xs text-[#6B6F72]">
                                            <i class="fa-solid fa-shield-halved mr-1 text-[#457699]" aria-hidden="true"></i>
                                            Seguridad:
                                            <span id="strengthText" class="font-bold">Sin contraseña</span>
                                        </p>
                                    </div>

                                    @error('password')
                                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-[#343A40]">
                                        Confirmar contraseña
                                    </label>

                                    <div class="register-input relative rounded-2xl border border-[#457699]/25 bg-[#F0F0F1]/60 focus-within:border-[#1F5F8B] focus-within:bg-white">
                                        <i class="fa-solid fa-shield-halved pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#457699]" aria-hidden="true"></i>

                                        <input
                                            id="password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            required
                                            minlength="5"
                                            autocomplete="new-password"
                                            placeholder="Repite tu contraseña"
                                            class="w-full rounded-2xl border-0 bg-transparent py-3.5 pl-11 pr-12 text-sm text-[#343A40] placeholder:text-[#6B6F72]/70 focus:outline-none focus:ring-0">

                                        <button type="button"
                                                data-toggle-password="password_confirmation"
                                                class="absolute inset-y-0 right-0 flex items-center rounded-r-2xl px-4 text-[#6B6F72] transition hover:text-[#1F5F8B] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#1F5F8B]/30"
                                                aria-label="Mostrar confirmación de contraseña"
                                                aria-pressed="false">
                                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <p id="matchText" class="mt-2 text-xs" aria-live="polite"></p>
                                </div>

                                <div class="flex items-start gap-3 rounded-2xl border border-[#F4C95D]/50 bg-[#F4C95D]/10 p-4">
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#F4C95D]/25 text-[#6B4F3A]">
                                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                                    </div>
                                    <p class="text-xs leading-5 text-[#6B4F3A]">
                                        Al crear tu cuenta, aceptas las condiciones de uso de Pipocas y el tratamiento de tus datos para gestionar tu cuenta.
                                    </p>
                                </div>

                                <button
                                    id="registerButton"
                                    type="submit"
                                    class="register-submit group flex w-full items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-r from-[#1F5F8B] via-[#1F5F8B] to-[#457699] px-4 py-4 text-sm font-extrabold text-white shadow-lg shadow-[#1F5F8B]/20 focus:outline-none focus:ring-4 focus:ring-[#1F5F8B]/25 disabled:cursor-not-allowed disabled:opacity-70">

                                    <i id="registerIcon" class="fa-solid fa-user-plus transition" aria-hidden="true"></i>
                                    <span id="registerText">Crear mi cuenta</span>
                                    <i class="fa-solid fa-arrow-right ml-auto text-xs text-[#F4C95D] transition group-hover:translate-x-1" aria-hidden="true"></i>
                                </button>
                            </form>

                            {{-- Acceso al login --}}
                            <div class="mt-7 border-t border-[#457699]/15 pt-6 text-center">
                                <p class="text-sm text-[#6B6F72]">
                                    ¿Ya tienes una cuenta?
                                </p>

                                <a href="{{ route('login') }}"
                                   class="mt-2 inline-flex items-center gap-2 font-extrabold text-[#C96F4A] transition hover:gap-3 hover:text-[#6B4F3A] hover:underline">
                                    Inicia sesión
                                    <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Franja inferior --}}
                        <div class="flex items-center justify-center gap-3 border-t border-[#457699]/15 bg-[#F0F0F1]/70 px-5 py-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#1F5F8B]/10">
                                <i class="fa-solid fa-shield-halved text-sm text-[#1F5F8B]" aria-hidden="true"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#343A40]">Tu información está protegida</p>
                                <p class="mt-0.5 text-[10px] text-[#6B6F72]">Crea tu cuenta y empieza a explorar.</p>
                            </div>
                        </div>
                    </section>

                    {{-- Pie móvil --}}
                    <footer class="mt-6 text-center lg:hidden">
                        <p class="text-xs font-semibold text-[#6B4F3A]">
                            <i class="fa-solid fa-location-dot mr-1 text-[#C96F4A]" aria-hidden="true"></i>
                            Vive La Paz. Descubre Bolivia.
                        </p>
                        <p class="mt-1 text-[11px] text-[#6B6F72]">
                            &copy; {{ date('Y') }} Pipocas · Agencia de Viajes y Turismo
                        </p>
                    </footer>
                </main>
            </section>
        </div>
    </div>

    {{-- SCRIPT ORIGINAL: FUNCIONALIDAD CONSERVADA --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('registerForm');
            const password = document.getElementById('password');
            const confirmation = document.getElementById('password_confirmation');
            const matchText = document.getElementById('matchText');
            const strengthText = document.getElementById('strengthText');
            const strengthBars = document.querySelectorAll('[data-strength-bar]');
            const registerButton = document.getElementById('registerButton');
            const registerIcon = document.getElementById('registerIcon');
            const registerText = document.getElementById('registerText');

            document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.dataset.togglePassword);
                    const icon = button.querySelector('i');
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    button.setAttribute('aria-pressed', String(show));
                    button.setAttribute(
                        'aria-label',
                        show ? 'Ocultar contraseña' : 'Mostrar contraseña'
                    );
                    icon.classList.toggle('fa-eye', !show);
                    icon.classList.toggle('fa-eye-slash', show);
                    icon.classList.toggle('fa-regular', !show);
                    icon.classList.toggle('fa-solid', show);
                });
            });

            function checkPasswordStrength() {
                const value = password.value;
                let score = 0;
                if (value.length === 0) {
                    strengthText.textContent = 'Sin contraseña';
                    strengthBars.forEach(function (bar) {
                        bar.style.backgroundColor = '#D9D2C9';
                    });
                    return;
                }
                score = 1;
                if (value.length >= 8) score = 2;
                if (
                    value.length >= 10 &&
                    /[a-z]/.test(value) &&
                    /[A-Z]/.test(value)
                ) {
                    score = 3;
                }
                if (
                    value.length >= 12 &&
                    /\d/.test(value) &&
                    /[^A-Za-z0-9]/.test(value)
                ) {
                    score = 4;
                }
                const levels = ['', 'Débil', 'Básica', 'Buena', 'Muy buena'];
                const colors = [
                    '#D9D2C9',
                    '#C96F4A',
                    '#D9A441',
                    '#6B9B78',
                    '#1F5F8B'
                ];
                strengthBars.forEach(function (bar, index) {
                    bar.style.backgroundColor = index < score
                        ? colors[score]
                        : '#D9D2C9';
                });
                strengthText.textContent = levels[score];
            }

            function checkPasswordMatch() {
                const value = confirmation.value;
                confirmation.setCustomValidity('');
                matchText.textContent = '';
                matchText.className = 'mt-2 text-xs';
                if (!value) return;
                if (password.value === value) {
                    matchText.textContent = '✓ Las contraseñas coinciden';
                    matchText.classList.add('font-medium', 'text-green-700');
                } else {
                    matchText.textContent = 'Las contraseñas no coinciden';
                    matchText.classList.add('font-medium', 'text-red-600');
                    confirmation.setCustomValidity(
                        'Las contraseñas no coinciden.'
                    );
                }
            }

            password.addEventListener('input', function () {
                checkPasswordStrength();
                checkPasswordMatch();
            });
            confirmation.addEventListener('input', checkPasswordMatch);

            form.addEventListener('submit', function (event) {
                checkPasswordMatch();
                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.reportValidity();
                    return;
                }
                if (registerButton.disabled) {
                    event.preventDefault();
                    return;
                }
                registerButton.disabled = true;
                registerIcon.className = 'fa-solid fa-spinner fa-spin';
                registerText.textContent = 'Creando tu cuenta...';
            });
        });
    </script>
</x-guest-layout>