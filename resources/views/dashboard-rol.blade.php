<x-guest-layout>
    <div class="min-h-screen bg-[#F0F0F1] px-6 py-12 text-[#343A40]">
        <div class="mx-auto max-w-3xl rounded-2xl bg-white p-8 shadow-sm">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#1F5F8B] text-xl text-white">
                    P
                </div>

                <div>
                    <h1 class="text-2xl font-bold">Bienvenido a Pipocas</h1>
                    <p class="text-sm text-[#6B6F72]">
                        Tu espacio de trabajo
                    </p>
                </div>
            </div>

            <p class="mb-2">
                Hola, {{ auth()->user()->nom }}.
            </p>

            <p class="mb-6 text-[#6B6F72]">
                Has iniciado sesión correctamente.
            </p>

            <div class="rounded-xl bg-[#F0F0F1] p-5">
                <p class="text-sm text-[#6B6F72]">Tu rol actual</p>

                <p class="mt-1 text-lg font-semibold capitalize">
                    {{ auth()->user()->rol?->nom ?? 'Sin rol asignado' }}
                </p>
            </div>

            <div class="mt-8">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-lg bg-[#1F5F8B] px-5 py-3 font-semibold text-white transition hover:bg-[#17496B]"
                    >
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>