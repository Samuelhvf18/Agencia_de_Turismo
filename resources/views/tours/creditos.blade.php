@extends('layouts.reserva')
@section('titulo', 'Créditos de imágenes')
@section('contenido')
<div class="mx-auto max-w-4xl space-y-6"><div><h1 class="rsv-titulo">Créditos de las fotografías del catálogo</h1><p class="mt-3 text-sm text-slate-600">Fotografías de referencia de los destinos, procedentes de Wikimedia Commons. Las tarjetas pueden recortar la vista para ajustarse a su formato.</p></div>
@foreach($creditos as $foto)
<article class="rsv-tarjeta flex flex-col gap-5 sm:flex-row"><img src="{{ asset('images/tours/'.$foto['archivo']) }}" alt="Fotografía de {{ pathinfo($foto['archivo'], PATHINFO_FILENAME) }}" class="h-36 w-full rounded-xl object-cover sm:w-52"><div class="space-y-2"><h2 class="font-bold text-andinist-principal">{{ $foto['autor'] }}</h2><p class="text-sm"><a href="{{ $foto['fuente'] }}" class="underline" target="_blank" rel="noopener">Ver fotografía y ficha original</a></p><p class="text-sm"><a href="{{ $foto['url_licencia'] }}" class="underline" target="_blank" rel="noopener">{{ $foto['licencia'] }}</a></p><p class="text-xs text-slate-500">{{ $foto['cambios'] }}</p></div></article>
@endforeach
</div>
@endsection
