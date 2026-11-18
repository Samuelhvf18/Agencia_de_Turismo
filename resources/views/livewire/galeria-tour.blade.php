<section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5" aria-label="Galería del tour">
    <div><h2 class="rsv-titulo">Galería de imágenes</h2><p class="mt-2 text-sm text-slate-500">La primera imagen es la portada del catálogo, las recomendaciones y el chatbot.</p></div>
    @if ($estado)<p class="rounded-xl bg-emerald-50 p-3 text-sm text-emerald-800" role="status">{{ $estado }}</p>@endif
    @if ($errors->any())<ul class="rounded-xl bg-red-50 p-3 text-sm text-red-700" role="alert">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($imagenes as $imagen)
        <article wire:key="galeria-{{ $imagen['foto']->id }}" class="min-w-0 rounded-xl border border-slate-200 p-3">
            <img src="{{ $imagen['url'] ?: asset('images/tour-sin-imagen.svg') }}" alt="{{ $imagen['foto']->txt_atv }}" class="h-32 w-full rounded-lg object-cover" data-imagen-tour>
            <label class="mt-3 block text-xs" for="foto-txt-{{ $imagen['foto']->id }}">Descripción de la imagen</label><input id="foto-txt-{{ $imagen['foto']->id }}" wire:model="textos.{{ $imagen['foto']->id }}" maxlength="150" class="rsv-campo mt-1">
            <div class="mt-3 flex flex-wrap justify-between gap-2 text-sm"><div class="flex gap-3"><button type="button" wire:click="mover({{ $imagen['foto']->id }}, -1)" @disabled($loop->first) aria-label="Mover foto a la izquierda" class="disabled:opacity-30">←</button><button type="button" wire:click="mover({{ $imagen['foto']->id }}, 1)" @disabled($loop->last) aria-label="Mover foto a la derecha" class="disabled:opacity-30">→</button></div><button type="button" wire:click="eliminar({{ $imagen['foto']->id }})" wire:confirm="¿Eliminar esta foto de la galería?" class="text-red-700">Eliminar</button></div>
        </article>
        @endforeach
    </div>
    @if($imagenes->isNotEmpty())<button type="button" wire:click="guardarTextos" wire:loading.attr="disabled" class="rsv-btn-sec">Guardar descripciones</button>@endif
    <form wire:submit="subir" class="space-y-4 border-t border-slate-100 pt-5">
        <label class="rsv-etiqueta" for="galeria-archivos">Añadir fotos</label><input id="galeria-archivos" type="file" wire:model="fotos" multiple accept="image/jpeg,image/png,image/webp" data-imagenes data-max-kb="4096" data-max-files="10" class="block w-full text-sm">
        <p class="text-xs text-slate-500">JPG, PNG o WebP; de 100 × 100 a 6000 × 6000 píxeles; hasta 4 MB por imagen. Máximo 10 por carga y 30 por tour.</p>
        @if($fotos)<p class="text-sm">{{ count($fotos) }} archivo(s) seleccionado(s).</p>@endif
        <div><label for="galeria-texto" class="rsv-etiqueta">Descripción de las nuevas fotos</label><input id="galeria-texto" wire:model="texto" maxlength="150" class="rsv-campo" placeholder="Ejemplo: vista del Valle de la Luna"></div>
        <p wire:loading wire:target="fotos" role="status" class="text-sm text-slate-500">Subiendo archivos para validarlos…</p>
        <button type="submit" wire:loading.attr="disabled" wire:target="fotos,subir" class="rsv-btn">Subir fotos</button>
    </form>
</section>
