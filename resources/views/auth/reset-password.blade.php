@extends('layouts.autenticacion')
@section('title', 'Restablecer contraseña | Pipocas')
@section('content')
<div class="relative flex min-h-screen items-center justify-center px-4 py-12 sm:px-6">
    <div class="w-full max-w-md">
        <div class="overflow-hidden rounded-3xl border border-white/60 bg-white/95 shadow-2xl shadow-[#102F49]/25 backdrop-blur-xl">
            <div class="bg-gradient-to-r from-[#1F5F8B] to-[#457699] px-6 py-8 text-center sm:px-8">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border border-white/30 bg-white/15 shadow-lg">
                    <i class="fa-solid fa-lock-open text-2xl text-white" aria-hidden="true"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    Nueva contraseña
                </h1>
                <p class="mt-2 text-sm leading-6 text-white/85">
                    Crea una contraseña nueva y segura para tu cuenta de Pipocas.
                </p>
            </div>
            <div class="px-6 py-8 sm:px-8">
                <x-validation-errors class="mb-5 rounded-xl" />
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input
                        type="hidden"
                        name="token"
                        value="{{ $request->route('token') }}">
                    <div>
                        <label for="cor" class="mb-2 block text-sm font-semibold text-[#343A40]">
                            Correo electrónico
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#6B6F72]">
                                <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                            </span>
                            <input
                                id="cor"
                                class="block w-full rounded-xl border border-[#457699]/25 bg-[#F0F0F1]/60 py-3 pl-11 pr-4 text-sm text-[#343A40] outline-none transition placeholder:text-[#6B6F72]/70 focus:border-[#1F5F8B] focus:bg-white focus:ring-4 focus:ring-[#1F5F8B]/10"
                                type="email"
                                name="cor"
                                value="{{ old('cor', $request->email) }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="tu@correo.com">
                        </div>
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-[#343A40]">
                            Nueva contraseña
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#6B6F72]">
                                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                            </span>
                            <input
                                id="password"
                                class="block w-full rounded-xl border border-[#457699]/25 bg-[#F0F0F1]/60 py-3 pl-11 pr-12 text-sm text-[#343A40] outline-none transition placeholder:text-[#6B6F72]/70 focus:border-[#1F5F8B] focus:bg-white focus:ring-4 focus:ring-[#1F5F8B]/10"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Ingresa tu nueva contraseña">
                            <button
                                type="button"
                                data-toggle-password="password"
                                aria-label="Mostrar contraseña"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-[#6B6F72] transition hover:text-[#1F5F8B]">
                                <i class="fa-regular fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-[#343A40]">
                            Confirmar contraseña
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#6B6F72]">
                                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                            </span>
                            <input
                                id="password_confirmation"
                                class="block w-full rounded-xl border border-[#457699]/25 bg-[#F0F0F1]/60 py-3 pl-11 pr-12 text-sm text-[#343A40] outline-none transition placeholder:text-[#6B6F72]/70 focus:border-[#1F5F8B] focus:bg-white focus:ring-4 focus:ring-[#1F5F8B]/10"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Repite tu nueva contraseña">
                            <button
                                type="button"
                                data-toggle-password="password_confirmation"
                                aria-label="Mostrar contraseña"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-[#6B6F72] transition hover:text-[#1F5F8B]">
                                <i class="fa-regular fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#1F5F8B] px-5 py-3.5 font-semibold text-white shadow-lg shadow-[#1F5F8B]/20 transition duration-200 hover:-translate-y-0.5 hover:bg-[#17496B] hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#1F5F8B]/25">
                        <i class="fa-solid fa-key" aria-hidden="true"></i>
                        <span>Cambiar contraseña</span>
                    </button>
                </form>
                <div class="mt-6 text-center">
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-[#1F5F8B] transition hover:text-[#C96F4A]">

                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Volver al inicio de sesión
                    </a>
                </div>
            </div>
        </div>
        <p class="mt-6 text-center text-xs leading-5 text-white/80">
            Protegemos el acceso a tu cuenta de Pipocas.
        </p>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.togglePassword);
            const icon = button.querySelector('i');
            if (!input || !icon) return;
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
});
</script>
@endpush