<section class="s" id="catalogo" style="padding-top:0">
  <div class="wrap">
    <div class="head-row">
      <div class="">
        <span class="eyebrow">{{ __('Catálogo oficial') }}</span>
        <h2 class="h2">{{ __('Tours') }} <em>{{ __('destacados') }}</em></h2>
      </div>
      <div class="filters" id="filters">
        <button type="button" wire:click="$set('categoria', 'todos')" @class(['chip', 'on' => $categoria === 'todos'])>{{ __('Todos') }}</button>
        @foreach ($categorias as $cat)
          <button type="button" wire:click="$set('categoria', '{{ $cat->rta_amg }}')" @class(['chip', 'on' => $categoria === $cat->rta_amg])><i class="{{ $cat->ico ?: 'fa-solid fa-route' }}"></i> {{ $cat->nom }}</button>
        @endforeach
      </div>
    </div>

    <div class="filtros2" style="margin-bottom:16px"><label for="catalogo-buscar">Buscar tour</label><input id="catalogo-buscar" type="search" maxlength="120" wire:model.live.debounce.350ms="q" class="fsel" placeholder="Nombre o código"><label for="catalogo-fecha">Fecha de salida</label><input id="catalogo-fecha" type="date" wire:model.live="fecha" class="fsel" min="{{ today()->toDateString() }}"></div>
    <div class="filtros2" role="group" aria-label="Filtrar por dificultad y altitud">
      <label class="sr-only" for="f-dif">{{ __('Dificultad') }}</label>
      <select id="f-dif" wire:model.live="dificultad" class="fsel">
        <option value="">{{ __('Toda dificultad') }}</option>
        <option value="facil">{{ __('Fácil') }}</option>
        <option value="moderado">{{ __('Moderado') }}</option>
        <option value="dificil">{{ __('Difícil') }}</option>
        <option value="experto">{{ __('Experto') }}</option>
      </select>
      <label class="sr-only" for="f-alt">{{ __('Altitud máxima') }}</label>
      <select id="f-alt" wire:model.live="altitud" class="fsel">
        <option value="">{{ __('Cualquier altitud') }}</option>
        <option value="0-3000">{{ __('Hasta 3.000 m') }}</option>
        <option value="3000-4000">3.000 – 4.000 m</option>
        <option value="4000-5000">4.000 – 5.000 m</option>
        <option value="5000-9000">{{ __('Más de 5.000 m') }}</option>
      </select>
      <span class="fcount" id="f-count" aria-live="polite">{{ $tours->count() }} tours</span>
      <button type="button" wire:click="limpiar" class="flimpiar" id="f-limpiar">{{ __('Quitar filtros') }}</button>
    </div>

    <div wire:loading role="status" style="margin:12px 0">Buscando tours…</div>
    <div class="cards" id="cards" wire:loading.class="opacity-60">
      @forelse ($tours as $tour)
      <article wire:key="tour-{{ $tour->id }}" class="card" data-cat="{{ $tour->categoria->rta_amg }}" data-dif="{{ $tour->niv_dif }}" data-alt="{{ (int) $tour->alt_max_m }}">
        <div class="cimg"><a href="{{ route('tours.show', $tour) }}"><img data-imagen-tour src="{{ $tour->img }}" alt="{{ $tour->img_alt }}" loading="lazy"></a><span class="tag {{ $tour->tag }}"><i class="{{ $tour->categoria->ico ?: 'fa-solid fa-route' }}"></i> {{ $tour->categoria->nom }}</span><span class="code">{{ $tour->cod }}</span></div>
        <div class="cbody"><div class="meta"><span><i class="fa-regular fa-clock"></i>{{ $tour->dur_txt }} h</span>@if ($tour->alt_max_m)<span><i class="fa-solid fa-mountain"></i>{{ number_format($tour->alt_max_m, 0, ',', '.') }} m</span>@endif<span><i class="fa-solid fa-users"></i>Máx {{ $tour->cap_max }}</span></div>
          <h3><a href="{{ route('tours.show', $tour) }}">{{ $tour->nom }}</a></h3><p>{{ $tour->des }}</p>
          <div class="diff"><span class="bars">@for ($i = 1; $i <= 5; $i++)<i @class(['f' => $i <= $tour->dif_niv])></i>@endfor</span>{{ $tour->dif_txt }}</div>
          <div class="cfoot"><div class="price"><small>{{ __('Desde') }}</small><b>Bs. {{ number_format($tour->pre_bas, 0, ',', '.') }} <span>/ {{ __('pers') }}</span></b></div><a class="cbtn" href="{{ route('tours.show', $tour) }}">{{ __('Reservar') }} <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </article>
      @empty
      <p class="sub">{{ __('No encontramos tours con estos filtros. Prueba otra búsqueda.') }}</p>
      @endforelse
      <p class="vacio" id="f-vacio" hidden><i class="fa-solid fa-mountain-sun"></i> {{ __('Ningún tour coincide con esos filtros. Prueba con otra combinación.') }}</p>
    </div>
  </div>
</section>
