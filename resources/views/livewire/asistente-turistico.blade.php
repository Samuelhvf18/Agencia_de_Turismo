<main class="mx-auto flex w-full max-w-4xl flex-1 flex-col overflow-hidden p-3 sm:p-6" x-data="{ bajar() { $nextTick(() => { const l = $refs.lista; l.scrollTop = l.scrollHeight; }); } }" x-init="bajar()" @chat-actualizado.window="bajar()">
    <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if($derivado)<p wire:poll.6s.visible="actualizar" class="border-b border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" role="status">Un asesor de Andinist Bolivia continúa esta conversación. / An advisor is handling this conversation.</p>@endif
        <div class="flex-1 space-y-4 overflow-y-auto p-4 sm:p-6" x-ref="lista" aria-live="polite">
            <p class="max-w-lg rounded-2xl bg-slate-100 p-4 text-sm">¡Hola! Dime qué lugar quieres conocer, cuánto tiempo tienes o tu presupuesto en bolivianos. Te mostraré tours con sus imágenes y enlaces. <span lang="en">I also speak English.</span></p>
            @foreach($mensajes as $m)
            <div wire:key="mensaje-{{ $m['id'] }}" class="flex {{ $m['rem'] === 'usuario' ? 'justify-end' : 'justify-start' }}">
                <article class="min-w-0 max-w-[92%] rounded-2xl p-4 text-sm {{ $m['rem'] === 'usuario' ? 'bg-andinist-principal text-white' : 'bg-slate-100 text-slate-800' }}">
                    @if($m['humano'])<p class="mb-2 font-bold text-amber-800">Asesor</p>@endif
                    <p class="whitespace-pre-line break-words">{{ $m['txt'] }}</p>
                    @if(!empty($m['tours']))
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        @foreach($m['tours'] as $tour)
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white text-slate-800">
                                <img src="{{ $tour['imagen'] }}" alt="{{ $tour['imagen_alt'] }}" loading="lazy" class="h-36 w-full object-cover" data-imagen-tour>
                                <div class="space-y-2 p-4"><h2 class="font-bold text-andinist-principal">{{ $tour['nombre'] }}</h2><p class="text-xs">{{ $tour['categoria'] }} · {{ $tour['duracion_horas'] }} h · {{ ucfirst($tour['dificultad']) }}</p><p class="font-bold">Bs {{ number_format($tour['precio_bs'], 2, ',', '.') }}</p><p class="text-xs text-slate-500">{{ $tour['proxima_salida'] ? 'Próxima salida: '.$tour['proxima_salida'] : 'Consulta las próximas salidas en la ficha' }}</p><a href="{{ $tour['url'] }}" class="rsv-btn w-full text-sm" data-chat-tour>Ver tour →</a></div>
                            </div>
                        @endforeach
                        </div>
                    @endif
                    <time class="mt-2 block text-xs opacity-60">{{ $m['hora'] }}</time>
                </article>
            </div>
            @endforeach
            <p wire:loading wire:target="enviar,sugerir" class="text-sm text-slate-500" role="status">Consultando el catálogo…</p>
        </div>
        @if(!$mensajes)
        <div class="flex flex-wrap gap-2 border-t border-slate-100 p-4">
            @foreach(['Muéstrame el Huayna Potosí', 'Recomiéndame un tour fácil', '¿Cómo funcionan los pagos?'] as $s)
            <button type="button" wire:click="sugerir(@js($s))" wire:loading.attr="disabled" class="rounded-full border border-slate-200 px-3 py-2 text-xs font-semibold">{{ $s }}</button>
            @endforeach
        </div>
        @endif
        <form wire:submit="enviar" class="flex items-end gap-2 border-t border-slate-100 p-4">
            <div class="min-w-0 flex-1"><label for="chat-mensaje" class="sr-only">Tu pregunta</label><input id="chat-mensaje" wire:loading.attr="disabled" wire:target="enviar,sugerir" wire:model="mensaje" maxlength="500" required autocomplete="off" class="rsv-campo" placeholder="Escribe tu pregunta… / Type your question…">@error('mensaje')<p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror</div>
            <button type="submit" wire:loading.attr="disabled" wire:target="enviar,sugerir" class="rsv-btn">Enviar</button>
        </form>
    </div>
</main>
