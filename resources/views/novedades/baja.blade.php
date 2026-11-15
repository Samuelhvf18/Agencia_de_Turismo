<x-guest-layout><div class="mx-auto my-12 max-w-lg rounded-xl bg-white p-6 shadow"><h1 class="text-2xl font-bold">Cancelar suscripción</h1>
@if ($hecho)<p class="mt-4">Ya no recibirás novedades. Los avisos necesarios de tus reservas seguirán llegando.</p>@else<p class="my-4">Puedes dejar de recibir novedades y promociones.</p><form method="POST">@csrf<x-button>Confirmar baja</x-button></form>@endif
<a class="mt-5 block underline" href="{{ route('home') }}">Volver</a></div></x-guest-layout>
