@props(['name' => 'tel', 'value' => '', 'required' => false])
@php
    $valor = (string) $value;
    $prefijo = collect(array_keys(config('turismo.prefijos')))->sortByDesc(fn ($p) => strlen($p))->first(fn ($p) => str_starts_with($valor, $p)) ?? '+591';
    $numero = str_starts_with($valor, '+') ? substr($valor, strlen($prefijo)) : $valor;
    $id = $attributes->get('id', $name.'_'.str_replace('.', '_', uniqid()));
@endphp
<div {{ $attributes->except('id')->merge(['class' => 'grid min-w-0 gap-2 sm:grid-cols-2']) }}>
    <div><label class="rsv-etiqueta text-xs" for="{{ $id }}_prefijo">{{ __('País / prefijo') }}</label><select id="{{ $id }}_prefijo" name="{{ $name }}_prefijo" class="rsv-campo" autocomplete="tel-country-code">@foreach(config('turismo.prefijos') as $codigo => $pais)<option value="{{ $codigo }}" @selected(old($name.'_prefijo', $prefijo) === $codigo)>{{ $pais }}</option>@endforeach</select></div>
    <div><label class="rsv-etiqueta text-xs" for="{{ $id }}_numero">{{ __('Teléfono') }}</label><input id="{{ $id }}_numero" name="{{ $name }}_numero" type="tel" inputmode="numeric" pattern="[0-9]{4,14}" maxlength="14" data-numeros autocomplete="tel-national" @required($required) class="rsv-campo" value="{{ old($name.'_numero', $numero) }}"><p class="mt-1 text-xs text-slate-500">{{ __('Solo números, sin prefijo ni espacios.') }}</p></div>
</div>
