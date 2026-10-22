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
            <section class="register-card relative w-full overflow-hidden rounded-[28px] border border-white/70 bg-white/[0.96] shadow-[0_25px_100px_rgba(7,35,56,0.45),0_0_60px_rgba(240,240,241,0.18)] backdrop-blur-2xl ring-1 ring-white/20">
                <div class="h-1.5 bg-gradient-to-r from-[#1F5F8B] via-[#F4C95D] to-[#C96F4A]"></div>
                <div class="p-6 sm:p-9">
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
                    @if ($errors->any())
                        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4"
                             role="alert" aria-live="polite">
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
                    @if (Route::has('auth.google.redirect'))
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
                    @endif
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
                                    maxlength="150"
                                    placeholder="Nombre completo"
                                    aria-invalid="{{ $errors->has('nom') ? 'true' : 'false' }}"
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
                            <div class="register-input relative rounded-2xl border {{ $errors->has('password') ? 'border-red-400' : 'border-[#457699]/25' }} bg-[#F0F0F1]/60 focus-within:border-[#1F5F8B] focus-within:bg-white">
                                <i class="fa-solid fa-lock pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#457699]" aria-hidden="true"></i>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Crea una contraseña segura"
                                    aria-describedby="strengthText"
                                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                    class="w-full rounded-2xl border-0 bg-transparent py-3.5 pl-11 pr-12 text-sm text-[#343A40] placeholder:text-[#6B6F72]/70 focus:outline-none focus:ring-0">
                                <button
                                    type="button"
                                    data-toggle-password="password"
                                    class="absolute inset-y-0 right-0 flex items-center rounded-r-2xl px-4 text-[#6B6F72] transition hover:text-[#1F5F8B] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#1F5F8B]/30"
                                    aria-label="Mostrar contraseña"
                                    aria-pressed="false">
                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
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
                                    autocomplete="new-password"
                                    placeholder="Repite tu contraseña"
                                    class="w-full rounded-2xl border-0 bg-transparent py-3.5 pl-11 pr-12 text-sm text-[#343A40] placeholder:text-[#6B6F72]/70 focus:outline-none focus:ring-0">
                                <button
                                    type="button"
                                    data-toggle-password="password_confirmation"
                                    class="absolute inset-y-0 right-0 flex items-center rounded-r-2xl px-4 text-[#6B6F72] transition hover:text-[#1F5F8B] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#1F5F8B]/30"
                                    aria-label="Mostrar confirmación de contraseña"
                                    aria-pressed="false">
                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <p id="matchText" class="mt-2 text-xs" aria-live="polite"></p>
                        </div>
                        <div class="rounded-2xl border border-[#457699]/20 bg-[#F0F0F1]/60 p-4">
                            <div class="flex items-start gap-3">
                                <input
                                    id="terms"
                                    type="checkbox"
                                    name="terms"
                                    value="1"
                                    required
                                    @checked(old('terms'))
                                    aria-describedby="termsDescription"
                                    class="mt-1 h-4 w-4 shrink-0 rounded border-[#457699]/40 text-[#1F5F8B] focus:ring-2 focus:ring-[#1F5F8B]">
                                <label for="terms" id="termsDescription" class="text-xs leading-5 text-[#343A40]">
                                    He leído y acepto los
                                    <a href="{{ route('terms.show') }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="font-bold text-[#1F5F8B] underline hover:text-[#C96F4A]">
                                        términos y condiciones
                                    </a>
                                    y la
                                    <a href="{{ route('policy.show') }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="font-bold text-[#1F5F8B] underline hover:text-[#C96F4A]">
                                        política de privacidad
                                    </a>.
                                </label>
                            </div>
                            @error('terms')
                                <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <div class="overflow-x-auto">
                                <div class="g-recaptcha"
                                     data-sitekey="{{ config('services.recaptcha.site_key') }}">
                                </div>
                            </div>
                            @error('g-recaptcha-response')
                                <p class="text-xs font-medium text-red-600" role="alert">
                                    <i class="fa-solid fa-circle-exclamation mr-1" aria-hidden="true"></i>
                                    {{ $message }}
                                </p>
                            @enderror
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
            <footer class="mt-6 text-center">
                <p class="text-xs font-semibold text-white/90">
                    <i class="fa-solid fa-location-dot mr-1 text-[#F4C95D]" aria-hidden="true"></i>
                    Vive La Paz. Descubre Bolivia.
                </p>
                <p class="mt-1 text-[11px] text-white/75">
                    &copy; {{ date('Y') }} Pipocas · Agencia de Viajes y Turismo
                </p>
            </footer>
        </main>
    </section>
</div>