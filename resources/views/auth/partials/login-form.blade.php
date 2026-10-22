<div class="relative flex min-h-screen items-center justify-center px-4 py-8 sm:px-6">
    <section class="relative flex w-full items-center justify-center">
        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#F4C95D]/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-[#C96F4A]/10 blur-3xl"></div>
        <div class="pointer-events-none absolute right-10 top-16 hidden h-16 w-16 rounded-full border border-[#1F5F8B]/10 sm:block"></div>
        <div class="pointer-events-none absolute bottom-20 right-12 hidden h-3 w-3 rounded-full bg-[#C96F4A]/40 sm:block"></div>
        <main class="relative z-10 mx-auto w-full max-w-[460px]">
            <header class="mb-7 text-center">
                <a href="{{ url('/') }}" class="inline-block" aria-label="Pipocas, página principal">
                    <div class="relative mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#1F5F8B] shadow-lg shadow-[#1F5F8B]/20">
                        <div class="absolute inset-1 rounded-xl border border-white/15"></div>
                        <i class="fa-solid fa-mountain-sun text-2xl text-[#F4C95D]" aria-hidden="true"></i>
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white">Pipocas</h1>
                    <p class="mt-1 text-[10px] font-bold uppercase tracking-[0.2em] text-white/80">
                        Agencia de Viajes y Turismo
                    </p>
                </a>
            </header>
            <section class="login-card relative w-full overflow-hidden rounded-[28px] border border-white/70 bg-white/[0.96] shadow-[0_25px_100px_rgba(7,35,56,0.45),0_0_60px_rgba(240,240,241,0.18)] backdrop-blur-2xl ring-1 ring-white/20">
                <div class="h-1.5 bg-gradient-to-r from-[#1F5F8B] via-[#F4C95D] to-[#C96F4A]"></div>
                <div class="p-6 sm:p-9">
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
                    @if (session('status'))
                        <div class="mb-5 flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 p-4"
                             role="status" aria-live="polite">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-green-100">
                                <i class="fa-solid fa-circle-check text-green-600" aria-hidden="true"></i>
                            </div>
                            <p class="pt-1.5 text-sm leading-5 text-green-700">
                                {{ session('status') }}
                            </p>
                        </div>
                    @endif
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

                            <i class"fa-solid fa-arrow-right ml-auto text-xs text-[#6B6F72] transition group-hover:translate-x-1 group-hover:text-[#1F5F8B]" aria-hidden="true"></i>
                        </a>
                        <div class="my-6 flex items-center gap-4">
                            <span class="h-px flex-1 bg-[#457699]/20"></span>
                            <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-[0.15em] text-[#6B6F72]">
                                O ingresa con tu correo
                            </span>
                            <span class="h-px flex-1 bg-[#457699]/20"></span>
                        </div>
                    @endif
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
                        {{-- Contraseña --}}
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
                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
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
            <footer class="mt-6 text-center">
                <p class="text-xs font-semibold text-white/90">
                    <i class="fa-solid fa-location-dot mr-1 text-[#F4C95D]" aria-hidden="true"></i>
                    Hecho para descubrir Bolivia
                </p>
                <p class="mt-1 text-[11px] text-white/75">
                    &copy; {{ date('Y') }} Pipocas · La Paz, Bolivia
                </p>
            </footer>
        </main>
    </section>
</div>