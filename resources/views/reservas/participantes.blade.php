@extends('layouts.reserva')

@php
    /*
     | DATOS DE EJEMPLO (solo frontend).
     | El controlador debe pasar:
     |   $tour          -> Tour presentado (nom, cod, img, dur_txt, dif_txt, pre_bas, categoria->nom)
     |   $salida        -> salidas_tour elegida (id, fec_sal, hra_sal, cap_max) + 'cup_dis' (cupos libres)
     |   $cnt_ptc       -> cantidad de participantes elegida en el paso 1
     |   $contacto      -> datos precargados del contacto (nom_ctc, cor_ctc, tel_ctc); si hay sesión, desde auth()->user()
     |   $participantes -> filas precargadas de participantes_reserva (nom_cpl, tip_doc, num_doc, ci_cmp,
     |                     ci_dep_id, eda, pai_id, es_ctc_pri)
     |   $paises        -> paises (id, nom)
     |   $departamentos -> departamentos_bolivia (id, cod, nom)
     |   $accionForm    -> route('reservas.participantes.store')
     | Se envía: nom_ctc, cor_ctc, tel_ctc y participantes[i][nom_cpl|tip_doc|num_doc|ci_cmp|ci_dep_id|eda|pai_id|es_ctc_pri]
     */
    $tour ??= (object) [
        'nom' => 'Valle de la Luna', 'cod' => 'TUR002', 'img' => asset('images/tours/valle-de-la-luna.jpg'),
        'img_alt' => 'Valle de la Luna', 'dur_txt' => '5', 'dif_txt' => 'Moderado', 'pre_bas' => 130.00,
        'categoria' => (object) ['nom' => 'Hiking'],
    ];
    $salida ??= ['id' => 11, 'fec_sal' => now()->addDays(2)->toDateString(), 'hra_sal' => '08:00:00', 'cap_max' => 12, 'cup_dis' => 8];
    $cnt_ptc ??= 2;
    $contacto ??= [
        'nom_ctc' => auth()->user()->name ?? '',
        'cor_ctc' => auth()->user()->email ?? '',
        'tel_ctc' => '',
    ];
    $participantes ??= [
        ['nom_cpl' => '', 'tip_doc' => 'CI', 'num_doc' => '', 'ci_cmp' => '', 'ci_dep_id' => 1, 'eda' => '', 'pai_id' => 1, 'es_ctc_pri' => true],
        ['nom_cpl' => '', 'tip_doc' => 'Pasaporte', 'num_doc' => '', 'ci_cmp' => '', 'ci_dep_id' => null, 'eda' => '', 'pai_id' => 2, 'es_ctc_pri' => false],
    ];
    $paises ??= [
        ['id' => 1, 'nom' => 'Bolivia'], ['id' => 2, 'nom' => 'Argentina'], ['id' => 3, 'nom' => 'Brasil'],
        ['id' => 4, 'nom' => 'Chile'], ['id' => 5, 'nom' => 'Perú'], ['id' => 6, 'nom' => 'Estados Unidos'],
        ['id' => 7, 'nom' => 'España'], ['id' => 8, 'nom' => 'Francia'], ['id' => 9, 'nom' => 'Alemania'],
    ];
    $departamentos ??= [
        ['id' => 1, 'cod' => 'LP', 'nom' => 'La Paz'], ['id' => 2, 'cod' => 'CB', 'nom' => 'Cochabamba'],
        ['id' => 3, 'cod' => 'SC', 'nom' => 'Santa Cruz'], ['id' => 4, 'cod' => 'OR', 'nom' => 'Oruro'],
        ['id' => 5, 'cod' => 'PT', 'nom' => 'Potosí'], ['id' => 6, 'cod' => 'CH', 'nom' => 'Chuquisaca'],
        ['id' => 7, 'cod' => 'TJ', 'nom' => 'Tarija'], ['id' => 8, 'cod' => 'BE', 'nom' => 'Beni'],
        ['id' => 9, 'cod' => 'PD', 'nom' => 'Pando'],
    ];
    $tiposDoc ??= ['CI' => 'Cédula de identidad', 'Pasaporte' => 'Pasaporte', 'DNI' => 'DNI / Documento extranjero'];
    $accionForm ??= url('/reservas/preview/equipo'); // controlador: route('reservas.participantes.store')
    $urlAnterior ??= url('/reservas/preview/salida'); // controlador: route('reservas.salida', $tour)

    $iniciales = collect(old('participantes', $participantes))->values()->map(fn ($p) => array_merge(
        ['nom_cpl' => '', 'tip_doc' => 'CI', 'num_doc' => '', 'ci_cmp' => '', 'ci_dep_id' => null, 'eda' => '', 'pai_id' => null],
        (array) $p
    ));
    $principal = max(0, (int) $iniciales->search(fn ($p) => ! empty($p['es_ctc_pri'])));
@endphp

@section('titulo', 'Participantes · ' . $tour->nom)

@section('stepper')
    <x-reserva.stepper :actual="2" :enlaces="[$urlAnterior]" />
@endsection

@section('contenido')
<form method="POST" action="{{ $accionForm }}" novalidate
      x-data="participantesForm(@js($iniciales), {{ $principal }}, {{ (int) ($salida['cup_dis'] ?? $salida['cap_max']) }}, @js($errors->getMessages()))"
      class="grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start lg:gap-8">
    @csrf

    <div class="space-y-6">
        <div>
            <h1 class="font-titulo text-3xl font-semibold leading-tight sm:text-4xl">¿Quiénes viajan?</h1>
            <p class="mt-2 text-pipocas-texto-2">Necesitamos los datos de cada participante para emitir su ticket con código QR y su seguro.</p>
        </div>

        @guest
            <div class="flex flex-col gap-3 rounded-2xl border border-pipocas-acento/60 bg-pipocas-acento/15 p-4 sm:flex-row sm:items-center">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-pipocas-acento text-pipocas-calido"><i class="fa-regular fa-user"></i></span>
                <div class="flex-1 text-sm">
                    <p class="font-bold text-pipocas-calido">Estás reservando como invitado</p>
                    <p class="text-pipocas-texto-2">Podrás consultar tu reserva con el código y tu correo. Si inicias sesión, la guardamos en tu cuenta.</p>
                </div>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="rsv-btn-sec shrink-0 px-4 py-2.5">Iniciar sesión</a>
                @endif
            </div>
        @endguest

        {{-- Contacto --}}
        <section class="rsv-tarjeta">
            <h2 class="rsv-titulo mb-1">Datos de contacto</h2>
            <p class="mb-5 text-sm text-pipocas-texto-2">A este correo enviaremos la confirmación, el comprobante y los tickets.</p>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="nom_ctc" class="rsv-etiqueta">Nombre completo</label>
                    <input id="nom_ctc" name="nom_ctc" type="text" maxlength="150" autocomplete="name" required
                           value="{{ old('nom_ctc', $contacto['nom_ctc']) }}" x-ref="nomCtc" class="rsv-campo" placeholder="Ej. Ana Quispe Mamani">
                    @error('nom_ctc') <p class="rsv-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="cor_ctc" class="rsv-etiqueta">Correo electrónico</label>
                    <input id="cor_ctc" name="cor_ctc" type="email" maxlength="150" autocomplete="email" required
                           value="{{ old('cor_ctc', $contacto['cor_ctc']) }}" class="rsv-campo" placeholder="tucorreo@ejemplo.com">
                    @error('cor_ctc') <p class="rsv-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tel_ctc" class="rsv-etiqueta">Teléfono / WhatsApp</label>
                    <input id="tel_ctc" name="tel_ctc" type="tel" maxlength="20" autocomplete="tel" required
                           value="{{ old('tel_ctc', $contacto['tel_ctc']) }}" class="rsv-campo" placeholder="+591 70000000">
                    @error('tel_ctc') <p class="rsv-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Participantes (bloques repetibles) --}}
        <section class="space-y-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <h2 class="rsv-titulo">Participantes</h2>
                    <p class="text-sm text-pipocas-texto-2"><span x-text="lista.length"></span> de <span x-text="max"></span> cupos disponibles en esta salida</p>
                </div>
            </div>
            @error('participantes') <p class="rsv-error">{{ $message }}</p> @enderror

            <template x-for="(p, i) in lista" :key="p._k">
                <article class="rsv-tarjeta" :class="pri === i && 'ring-2 ring-pipocas-principal/40'">
                    <header class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-pipocas-principal font-bold text-white" x-text="i + 1"></span>
                            <h3 class="font-bold" x-text="p.nom_cpl || 'Participante ' + (i + 1)"></h3>
                            <span x-show="pri === i" class="rounded-full bg-pipocas-secundario/15 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wide text-pipocas-secundario">Contacto principal</span>
                        </div>
                        <button type="button" x-show="lista.length > 1" @click="quitar(i)"
                                class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                            <i class="fa-regular fa-trash-can"></i> Quitar
                        </button>
                    </header>

                    <input type="hidden" :name="`participantes[${i}][es_ctc_pri]`" :value="pri === i ? 1 : 0">

                    <div class="grid gap-4 sm:grid-cols-6">
                        <div class="sm:col-span-4">
                            <label class="rsv-etiqueta" :for="`nom_cpl_${i}`">Nombre completo</label>
                            <input type="text" maxlength="150" class="rsv-campo" :id="`nom_cpl_${i}`" :name="`participantes[${i}][nom_cpl]`" x-model="p.nom_cpl" placeholder="Como figura en su documento">
                            <p class="rsv-error" x-text="err(i, 'nom_cpl')" x-show="err(i, 'nom_cpl')"></p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="rsv-etiqueta" :for="`eda_${i}`">Edad</label>
                            <input type="number" min="1" max="99" class="rsv-campo" :id="`eda_${i}`" :name="`participantes[${i}][eda]`" x-model="p.eda">
                            <p class="rsv-error" x-text="err(i, 'eda')" x-show="err(i, 'eda')"></p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="rsv-etiqueta" :for="`tip_doc_${i}`">Tipo de documento</label>
                            <select class="rsv-campo" :id="`tip_doc_${i}`" :name="`participantes[${i}][tip_doc]`" x-model="p.tip_doc">
                                @foreach ($tiposDoc as $valor => $texto)
                                    <option value="{{ $valor }}">{{ $texto }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div :class="p.tip_doc === 'CI' ? 'sm:col-span-2' : 'sm:col-span-4'">
                            <label class="rsv-etiqueta" :for="`num_doc_${i}`">Número de documento</label>
                            <div class="flex gap-2">
                                <input type="text" maxlength="15" class="rsv-campo" :id="`num_doc_${i}`" :name="`participantes[${i}][num_doc]`" x-model="p.num_doc">
                                <input type="text" maxlength="5" class="rsv-campo w-20" x-show="p.tip_doc === 'CI'" :name="`participantes[${i}][ci_cmp]`" x-model="p.ci_cmp" placeholder="Comp." aria-label="Complemento del CI">
                            </div>
                            <p class="rsv-error" x-text="err(i, 'num_doc')" x-show="err(i, 'num_doc')"></p>
                        </div>
                        <div class="sm:col-span-2" x-show="p.tip_doc === 'CI'">
                            <label class="rsv-etiqueta" :for="`ci_dep_${i}`">Expedido en</label>
                            <select class="rsv-campo" :id="`ci_dep_${i}`" :name="`participantes[${i}][ci_dep_id]`" x-model="p.ci_dep_id" :disabled="p.tip_doc !== 'CI'">
                                <option value="">—</option>
                                @foreach ($departamentos as $d)
                                    <option value="{{ $d['id'] }}">{{ $d['nom'] }} ({{ $d['cod'] }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-3">
                            <label class="rsv-etiqueta" :for="`pai_${i}`">Nacionalidad</label>
                            <select class="rsv-campo" :id="`pai_${i}`" :name="`participantes[${i}][pai_id]`" x-model="p.pai_id">
                                <option value="">Selecciona un país</option>
                                @foreach ($paises as $pais)
                                    <option value="{{ $pais['id'] }}">{{ $pais['nom'] }}</option>
                                @endforeach
                            </select>
                            <p class="rsv-error" x-text="err(i, 'pai_id')" x-show="err(i, 'pai_id')"></p>
                        </div>
                        <div class="flex flex-wrap items-end gap-x-5 gap-y-2 sm:col-span-3">
                            <label class="inline-flex cursor-pointer items-center gap-2 py-2 text-sm font-semibold">
                                <input type="radio" class="h-5 w-5 border-gray-300 text-pipocas-principal focus:ring-pipocas-principal" :checked="pri === i" @change="pri = i">
                                Contacto principal
                            </label>
                            <button type="button" x-show="i === 0" @click="p.nom_cpl = $refs.nomCtc.value"
                                    class="py-2 text-sm font-semibold text-pipocas-complementario underline-offset-2 hover:text-pipocas-hover hover:underline">
                                Usar mis datos de contacto
                            </button>
                        </div>
                    </div>
                </article>
            </template>

            <button type="button" @click="agregar()" :disabled="lista.length >= max"
                    class="flex w-full items-center justify-center gap-2 rounded-3xl border-2 border-dashed border-pipocas-complementario/50 bg-white/60 p-5 font-bold text-pipocas-complementario transition hover:border-pipocas-principal hover:text-pipocas-principal disabled:cursor-not-allowed disabled:opacity-50">
                <i class="fa-solid fa-user-plus"></i>
                <span x-text="lista.length >= max ? 'No quedan más cupos en esta salida' : 'Agregar participante'"></span>
            </button>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
            <a href="{{ $urlAnterior }}" class="rsv-btn-sec"><i class="fa-solid fa-arrow-left"></i> Cambiar salida</a>
            <button type="submit" class="rsv-btn">Continuar al equipo <i class="fa-solid fa-arrow-right"></i></button>
        </div>
    </div>

    <aside class="lg:sticky lg:top-6">
        <x-reserva.resumen-tour :tour="$tour" :salida="$salida" :cnt="$cnt_ptc" cnt-expr="lista.length" />
    </aside>
</form>
@endsection

@push('scripts')
<script>
    // Bloques repetibles de participantes (Alpine, incluido con Livewire)
    function participantesForm(iniciales, principal, max, errores) {
        let k = 0;
        const vacio = () => ({ _k: ++k, nom_cpl: '', tip_doc: 'CI', num_doc: '', ci_cmp: '', ci_dep_id: '', eda: '', pai_id: '' });
        return {
            lista: iniciales.map(p => ({ ...vacio(), ...p, _k: ++k })),
            pri: principal,
            max: max,
            errores: errores || {},
            agregar() { if (this.lista.length < this.max) this.lista.push(vacio()); },
            quitar(i) {
                this.lista.splice(i, 1);
                if (this.pri === i) this.pri = 0; else if (this.pri > i) this.pri--;
            },
            err(i, campo) { return (this.errores[`participantes.${i}.${campo}`] || [])[0] || ''; },
        };
    }
</script>
@endpush
