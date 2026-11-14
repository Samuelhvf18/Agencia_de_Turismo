@extends('layouts.autenticacion')
@section('title', 'Recuperar contraseña | Pipocas')
@section('content')
<div class="relative flex min-h-screen items-center justify-center px-4 py-12 sm:px-6">
    <div class="w-full max-w-md">
        <div class="overflow-hidden rounded-3xl border border-white/60 bg-white/95 shadow-2xl shadow-[#102F49]/25 backdrop-blur-xl">
            <div class="bg-gradient-to-r from-[#1F5F8B] to-[#457699] px-6 py-8 text-center sm:px-8">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border border-white/30 bg-white/15 shadow-lg">
                    <i class="fa-solid fa-key text-2xl text-white" aria-hidden="true"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    Recuperar contraseña
                </h1>
                <p class="mt-2 text-sm leading-6 text-white/85">
                    Recupera el acceso a tu cuenta de Pipocas de forma segura.
                </p>
            </div>
            <div class="px-6 py-8 sm:px-8">
                <p class="mb-6 text-sm leading-6 text-[#6B6F72]">
                    Introduce el correo electrónico que utilizaste al crear tu cuenta.
                    Te enviaremos un enlace para restablecer tu contraseña.
                </p>
                @session('status')
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-check mt-0.5" aria-hidden="true"></i>
                            <span>{{ $value }}</span>
                        </div>
                    </div>
                @endsession
                @if (app()->environment('local') && session('password_reset_link'))
                    <div class="mb-5 rounded-xl border border-[#457699]/30 bg-[#457699]/10 p-4">
                        <p class="font-semibold text-[#1F5F8B]">
                            <i class="fa-solid fa-link mr-1" aria-hidden="true"></i>
                            Enlace de recuperación para desarrollo
                        </p>
                        <p class="mt-2 text-sm leading-6 text-[#343A40]">
                            Abre el siguiente enlace para restablecer tu contraseña:
                        </p>
                        <a
                            href="{{ session('password_reset_link') }}"
                            class="mt-2 block break-all text-sm font-semibold text-[#1F5F8B] underline decoration-[#1F5F8B]/40 underline-offset-4 hover:text-[#C96F4A]">
                            {{ session('password_reset_link') }}
                        </a>
                    </div>
                @endif
                <x-validation-errors class="mb-5" />
                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf
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
                                value="{{ old('cor') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="tu@correo.com">
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#1F5F8B] px-5 py-3.5 font-semibold text-white shadow-lg shadow-[#1F5F8B]/20 transition duration-200 hover:-translate-y-0.5 hover:bg-[#17496B] hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#1F5F8B]/25">
                        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                        <span>Enviar enlace de recuperación</span>
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