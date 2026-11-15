@extends('layouts.reserva')
@php
    $iniciales = collect(old('participantes', $participantes))->values();
    $principal = max(0, (int) $iniciales->search(fn ($p) => ! empty($p['es_ctc_pri'])));
    $telefono = preg_replace('/[ -]/', '', $contacto['tel_ctc'] ?? '');
    $prefijo = collect(array_keys(config('turismo.prefijos')))->sortByDesc(fn ($p) => strlen($p))->first(fn ($p) => str_starts_with($telefono, $p)) ?? '+591';
@endphp
@section('titulo', 'Participantes · '.$tour->nom)
@section('stepper')
<x-reserva.stepper :actual="2" :enlaces="[$urlAnterior]" />
@endsection
@section('contenido')
<form method="POST" action="{{ $accionForm }}" x-data="participantesForm(@js($iniciales), {{ $principal }}, {{ (int) $salida['cup_dis'] }}, @js($salida['fec_sal']), {{ $tour->edadMinimaEfectiva() }}, @js($tour->admiteMenores()))" class="grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start">
@csrf
<div class="space-y-6">
    <div><h1 class="font-titulo text-3xl font-semibold">{{ __('¿Quiénes viajan?') }}</h1><p class="mt-2">La edad se calcula para el día de la salida: {{ $salida['fec_sal'] }}.</p></div>
    @if ($errors->any())<div role="alert" class="rounded-xl border border-red-300 bg-red-50 p-4 text-red-800"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @guest<p class="rounded-xl bg-amber-50 p-4">Reservas como invitado. Guarda tu código y correo para consultar la reserva. <a href="{{ route('login') }}" class="underline">Iniciar sesión</a></p>@endguest
    <section class="rsv-tarjeta space-y-4">
        <h2 class="rsv-titulo">{{ __('Datos de contacto') }}</h2>
        @auth<p class="text-sm">Nombre y correo pertenecen a tu cuenta. Puedes actualizarlos desde <a class="underline" href="{{ route('profile.show') }}">tu perfil</a>.</p>@endauth
        <label class="rsv-etiqueta" for="nom_ctc">{{ __('Nombre completo') }}</label>
        <input class="rsv-campo" id="nom_ctc" name="nom_ctc" required maxlength="150" data-letras autocomplete="name" x-ref="nomCtc" value="{{ auth()->user()?->nom ?? old('nom_ctc', $contacto['nom_ctc']) }}" @readonly(auth()->check())>
        <label class="rsv-etiqueta" for="cor_ctc">{{ __('Correo electrónico') }}</label>
        <input class="rsv-campo" id="cor_ctc" name="cor_ctc" type="email" required maxlength="150" autocomplete="email" value="{{ auth()->user()?->cor ?? old('cor_ctc', $contacto['cor_ctc']) }}" @readonly(auth()->check())>
        <div class="grid gap-3 sm:grid-cols-2">
            <div><label class="rsv-etiqueta" for="prefijo_ctc">{{ __('País / prefijo') }}</label><select id="prefijo_ctc" name="prefijo_ctc" class="rsv-campo" autocomplete="tel-country-code">@foreach (config('turismo.prefijos') as $codigo => $nombre)<option value="{{ $codigo }}" @selected(old('prefijo_ctc', $prefijo) === $codigo)>{{ $nombre }}</option>@endforeach</select></div>
            <div><label class="rsv-etiqueta" for="telefono_nacional">{{ __('Teléfono / WhatsApp') }}</label><input id="telefono_nacional" name="telefono_nacional" type="tel" inputmode="numeric" pattern="[0-9]{4,14}" data-numeros required maxlength="14" autocomplete="tel-national" class="rsv-campo" value="{{ old('telefono_nacional', substr($telefono, strlen($prefijo))) }}"><p class="text-xs mt-1">{{ __('Solo números, sin prefijo ni espacios.') }}</p></div>
        </div>
    </section>
    <div class="rsv-tarjeta border-l-4 border-amber-500">
        <h2 class="font-bold">{{ __('Condiciones de participación') }}</h2>
        <p>Edad mínima: {{ $tour->edadMinimaEfectiva() }} años el día de salida.</p>
        @if (! $tour->admiteMenores())<p class="font-semibold">Este tour admite únicamente adultos. En alto riesgo, la autorización de un representante no permite excepciones.</p>@else<p>Los menores viajan con un adulto de la reserva. La agencia verificará identidad, vínculo y permisos originales antes de emitir su boleto. Una casilla en la web no sustituye la autorización legal.</p>@endif
    </div>
    <template x-for="(p, i) in lista" :key="p._k">
        <article class="rsv-tarjeta space-y-4">
            <header class="flex justify-between"><h2 class="rsv-titulo" x-text="'Participante '+(i+1)"></h2><button type="button" class="text-red-700 underline" @click="quitar(i)" x-show="lista.length > 1">Quitar</button></header>
            <input type="hidden" :name="`participantes[${i}][es_ctc_pri]`" :value="pri === i ? 1 : 0">
            <div><label class="rsv-etiqueta" :for="`nombre_${i}`">{{ __('Nombre completo') }}</label><input class="rsv-campo" :id="`nombre_${i}`" :name="`participantes[${i}][nom_cpl]`" x-model="p.nom_cpl" data-letras required maxlength="150"></div>
            <div><label class="rsv-etiqueta" :for="`nacimiento_${i}`">{{ __('Fecha de nacimiento') }}</label><input class="rsv-campo" type="date" :id="`nacimiento_${i}`" :name="`participantes[${i}][fecha_nacimiento]`" x-model="p.fecha_nacimiento" max="{{ today()->toDateString() }}" min="{{ today()->subYears(110)->toDateString() }}" required><p class="text-sm" x-show="p.fecha_nacimiento" x-text="'Edad el día de salida: '+edad(p)+' años'"></p><p class="rsv-error" x-show="invalido(p)">No cumple las condiciones de edad de este tour. Elige otra actividad o modifica los participantes.</p></div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div><label class="rsv-etiqueta" :for="`tipo_${i}`">{{ __('Tipo de documento') }}</label><select class="rsv-campo" :id="`tipo_${i}`" :name="`participantes[${i}][tip_doc]`" x-model="p.tip_doc" @change="p.num_doc = ''; p.ci_cmp = ''; p.ci_dep_id = ''"><option value="CI">{{ __('Cédula de identidad (Bolivia)') }}</option><option value="PASAPORTE">{{ __('Pasaporte / documento extranjero') }}</option></select></div>
                <div><label class="rsv-etiqueta" :for="`documento_${i}`">{{ __('Número de documento') }}</label><input class="rsv-campo" :id="`documento_${i}`" :name="`participantes[${i}][num_doc]`" x-model="p.num_doc" @input="p.num_doc = p.num_doc.toUpperCase().replace(p.tip_doc === 'CI' ? /[^0-9]/g : /[^A-Z0-9-]/g, '')" :pattern="p.tip_doc === 'CI' ? '[0-9]{5,10}' : '[A-Z0-9\\-]{5,15}'" :inputmode="p.tip_doc === 'CI' ? 'numeric' : 'text'" maxlength="15" required></div>
                <div x-show="p.tip_doc === 'CI'"><label class="rsv-etiqueta" :for="`complemento_${i}`">{{ __('Complemento (opcional)') }}</label><input class="rsv-campo" :id="`complemento_${i}`" :name="`participantes[${i}][ci_cmp]`" x-model="p.ci_cmp" maxlength="5" @input="p.ci_cmp = p.ci_cmp.toUpperCase().replace(/[^A-Z0-9]/g, '')"></div>
                <div x-show="p.tip_doc === 'CI'"><label class="rsv-etiqueta" :for="`departamento_${i}`">{{ __('Expedido en') }}</label><select class="rsv-campo" :id="`departamento_${i}`" :name="`participantes[${i}][ci_dep_id]`" x-model="p.ci_dep_id" :required="p.tip_doc === 'CI'" :disabled="p.tip_doc !== 'CI'"><option value="">{{ __('Seleccionar') }}</option>@foreach ($departamentos as $d)<option value="{{ $d['id'] }}">{{ $d['nom'] }}</option>@endforeach</select></div>
                <div><label class="rsv-etiqueta" :for="`pais_${i}`">{{ __('Nacionalidad') }}</label><select class="rsv-campo" :id="`pais_${i}`" :name="`participantes[${i}][pai_id]`" x-model="p.pai_id" required><option value="">{{ __('Seleccionar') }}</option>@foreach ($paises as $pais)<option value="{{ $pais['id'] }}">{{ $pais['nom'] }}</option>@endforeach</select></div>
            </div>
            <template x-if="p.fecha_nacimiento && edad(p) < 18 && ! invalido(p)">
                <fieldset class="space-y-3 rounded-xl border border-amber-300 bg-amber-50 p-4">
                    <legend class="font-bold">{{ __('Representante y acompañante del menor') }}</legend>
                    <label class="rsv-etiqueta" :for="`adulto_${i}`">{{ __('Adulto que viaja con el menor') }}</label><select class="rsv-campo" :id="`adulto_${i}`" :name="`participantes[${i}][acompanante_indice]`" x-model="p.acompanante_indice" required><option value="">{{ __('Seleccionar un adulto de esta reserva') }}</option><template x-for="(adulto, j) in lista" :key="adulto._k"><option :value="j" :disabled="j === i || ! adulto.fecha_nacimiento || edad(adulto) < 18" x-text="adulto.nom_cpl || 'Participante '+(j+1)"></option></template></select>
                    <label class="rsv-etiqueta" :for="`representante_${i}`">{{ __('Nombre del representante que autoriza') }}</label><input class="rsv-campo" :id="`representante_${i}`" :name="`participantes[${i}][responsable_nombre]`" x-model="p.responsable_nombre" data-letras required maxlength="150">
                    <label class="rsv-etiqueta" :for="`doc_rep_${i}`">{{ __('Documento del representante') }}</label><input class="rsv-campo" :id="`doc_rep_${i}`" :name="`participantes[${i}][responsable_documento]`" x-model="p.responsable_documento" pattern="[A-Za-z0-9\-]{5,20}" maxlength="20" required>
                    <label class="rsv-etiqueta" :for="`vinculo_${i}`">{{ __('¿Con quién viaja?') }}</label><select class="rsv-campo" :id="`vinculo_${i}`" :name="`participantes[${i}][vinculo]`" x-model="p.vinculo" required><option value="">{{ __('Seleccionar') }}</option><option value="ambos_padres">{{ __('Ambos progenitores') }}</option><option value="un_progenitor">{{ __('Un progenitor') }}</option><option value="tutor">{{ __('Tutor o guardador legal') }}</option><option value="tercero">{{ __('Familiar o tercero') }}</option></select>
                    <p class="text-sm" x-show="p.vinculo === 'ambos_padres'">Presentar documentos originales del menor y ambos progenitores, y acreditar el vínculo.</p>
                    <p class="text-sm" x-show="p.vinculo === 'un_progenitor'">Para viajes nacionales interdepartamentales: identidad del menor y adulto, autorización del progenitor que no viaja o documentación que justifique su ausencia, según verificación de la Defensoría.</p>
                    <p class="text-sm" x-show="p.vinculo === 'tutor'">Presentar identidad del menor y tutor, y resolución judicial que acredite tutela o guarda.</p>
                    <p class="text-sm" x-show="p.vinculo === 'tercero'">Presentar identidad del menor y acompañante, y autorización de ambos progenitores o representante legal verificada por la autoridad competente.</p>
                    <label class="flex gap-2 text-sm"><input type="checkbox" :name="`participantes[${i}][consentimiento_menor]`" value="1" x-model="p.consentimiento_menor" required> Declaro que el representante autoriza la participación del menor en este tour y que presentaremos los documentos y permisos originales aplicables. El adulto indicado lo acompañará durante el recorrido.</label>
                </fieldset>
            </template>
            <label class="flex gap-2"><input type="radio" :checked="pri === i" @change="pri = i"> Contacto principal</label>
            <button type="button" @click="p.nom_cpl = $refs.nomCtc.value" class="text-sm underline">{{ __('Usar el nombre del contacto') }}</button>
        </article>
    </template>
    <button type="button" class="rsv-btn-sec w-full" @click="agregar()" :disabled="lista.length >= max">{{ __('Agregar participante') }}</button>
    <div class="flex justify-between gap-3"><a href="{{ $urlAnterior }}" class="rsv-btn-sec">{{ __('Cambiar salida') }}</a><button class="rsv-btn" :disabled="lista.some(p => invalido(p))">{{ __('Continuar al equipo') }}</button></div>
</div>
<aside class="lg:sticky lg:top-6"><x-reserva.resumen-tour :tour="$tour" :salida="$salida" :cnt="$cnt_ptc" cnt-expr="lista.length" /></aside>
</form>
@endsection
@push('scripts')
<script>
function participantesForm(iniciales, principal, max, fecha, minima, admiteMenores) {
    let k = 0;
    const vacio = () => ({_k: ++k, nom_cpl:'', fecha_nacimiento:'', tip_doc:'CI', num_doc:'', ci_cmp:'', ci_dep_id:'', pai_id:'', acompanante_indice:'', responsable_nombre:'', responsable_documento:'', vinculo:'', consentimiento_menor:false});
    return { lista: iniciales.map(p => ({...vacio(), ...p, _k:++k})), pri:principal, max,
        edad(p) { if (!p.fecha_nacimiento) return null; const [y,m,d] = p.fecha_nacimiento.split('-').map(Number); const [a,b,c] = fecha.split('-').map(Number); return a-y-(b<m || (b===m && c<d) ? 1 : 0); },
        invalido(p) { const edad=this.edad(p); return edad !== null && (edad<minima || (!admiteMenores && edad<18)); },
        agregar() { if(this.lista.length<this.max) this.lista.push(vacio()); },
        quitar(i) { this.lista.splice(i,1); if(this.pri===i) this.pri=0; else if(this.pri>i) this.pri--; this.lista.forEach(p => {p.acompanante_indice='';p.consentimiento_menor=false;}); }
    };
}
</script>
@endpush
