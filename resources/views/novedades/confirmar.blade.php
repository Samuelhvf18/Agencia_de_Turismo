<x-guest-layout><div class="mx-auto my-12 max-w-lg rounded-xl bg-white p-6 shadow"><h1 class="text-2xl font-bold">Novedades de Andinist Bolivia</h1>
@if ($confirmada)<p class="mt-4">Tu suscripción está confirmada. Los correos de novedades incluirán un enlace para darte de baja.</p>@else<p class="my-4">Confirma que deseas recibir novedades y promociones.</p><form method="POST">@csrf<x-button>Confirmar suscripción</x-button></form>@endif
<a class="mt-5 block underline" href="{{ route('home') }}">Volver a los tours</a></div></x-guest-layout>
