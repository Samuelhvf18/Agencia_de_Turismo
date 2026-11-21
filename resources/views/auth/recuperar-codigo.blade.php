<x-guest-layout>
@php
    $correo = old('cor', session('recuperacion.correo', ''));
    $pasoCodigo = session('recuperacion.correo') || old('_paso') === 'codigo' || request('paso') === 'codigo';
@endphp
<main class="relative flex min-h-screen items-center justify-center bg-andinist-fondo px-4 py-10 font-cuerpo text-andinist-texto">
    <div class="w-full max-w-lg">
        <a href="{{ route('home') }}" class="mb-7 flex items-center justify-center gap-3 text-2xl font-extrabold text-andinist-principal"><span class="grid h-12 w-12 place-items-center rounded-2xl bg-andinist-acento" aria-hidden="true">A</span> Andinist Bolivia</a>
        <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-xl shadow-stone-200/50 sm:p-9" aria-labelledby="recuperar-titulo">
            <p class="mb-3 text-xs font-bold uppercase tracking-widest text-andinist-secundario">{{ __('Vuelve a tu próxima aventura') }}</p>
            <h1 id="recuperar-titulo" class="font-titulo text-3xl text-andinist-principal">{{ __('Recuperar contraseña') }}</h1>
            <ol class="my-6 flex gap-4 text-sm" aria-label="{{ __('Pasos de recuperación') }}">
                <li class="flex items-center gap-2"><span class="grid h-7 w-7 place-items-center rounded-full bg-andinist-principal text-white">1</span> {{ __('Tu correo') }}</li>
                <li class="flex items-center gap-2"><span @class(['grid h-7 w-7 place-items-center rounded-full', 'bg-andinist-principal text-white' => $pasoCodigo, 'bg-stone-100 text-stone-500' => ! $pasoCodigo])>2</span> {{ __('Nueva contraseña') }}</li>
            </ol>
            @if (session('status'))<p role="status" class="mb-5 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('status') }}</p>@endif
            @if ($errors->any())<div role="alert" class="mb-5 rounded-xl bg-red-50 p-4 text-sm text-red-700"><x-validation-errors /></div>@endif
            @if (! $pasoCodigo)
                <p class="mb-5 text-sm text-andinist-texto-2">{{ __('Escribe el correo de tu cuenta. Te enviaremos un código de 6 dígitos para crear una nueva contraseña.') }}</p>
                <form method="POST" action="{{ route('codigo.solicitar') }}" class="space-y-5">
                    @csrf
                    <div><label class="rsv-etiqueta" for="solicitar-correo">{{ __('Correo de la cuenta') }}</label><input id="solicitar-correo" type="email" name="cor" required maxlength="150" autocomplete="email" value="{{ $correo }}" class="rsv-campo" placeholder="nombre@correo.com" autofocus></div>
                    <button type="submit" class="rsv-btn w-full">{{ __('Enviar código de recuperación') }} →</button>
                </form>
                <a href="{{ route('codigo.recuperar', ['paso' => 'codigo']) }}" class="mt-5 block text-center text-sm font-semibold text-andinist-principal">{{ __('Ya tengo un código') }}</a>
            @else
                @include('auth.partials.nueva-clave', ['correo' => $correo])
            @endif
        </section>
        <a href="{{ route('login') }}" class="mt-6 block text-center text-sm font-semibold text-andinist-principal">← {{ __('Volver al inicio de sesión') }}</a>
    </div>
</main>
</x-guest-layout>
