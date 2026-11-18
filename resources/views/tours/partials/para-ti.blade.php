@if ($recomendaciones['tours']->isNotEmpty())
@once
<style>
.interes{color:#343A40}.interes-titulo{font-family:'Fraunces',Georgia,serif;font-size:26px;font-weight:600;line-height:1.3;color:#1F5F8B}.interes-intro{margin-top:8px;font-size:14px;color:#6B6F72}
.interes-grid{display:grid;gap:20px;margin-top:20px}.interes-card{overflow:hidden;border:1px solid #e7e5e4;border-radius:20px;background:#fff}.interes-imagen{display:block;height:176px;width:100%;object-fit:cover}.interes-cuerpo{padding:20px}.interes-categoria{font-size:12px;font-weight:700;color:#C96F4A}.interes-nombre{margin-top:12px;font-size:17px;font-weight:700;line-height:1.4;color:#1F5F8B}.interes-motivo{margin-top:10px;min-height:36px;font-size:12px;line-height:1.5;color:#6B6F72}.interes-pie{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-top:14px}.interes-enlace{font-size:14px;font-weight:700;color:#1F5F8B}.interes a:focus-visible{outline:2px solid #1F5F8B;outline-offset:4px}.interes-enlace:hover{text-decoration:underline}
@media(min-width:768px){.interes-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
</style>
@endonce
<section class="interes" aria-labelledby="para-ti-titulo">
    <div><h2 id="para-ti-titulo" class="interes-titulo">{{ __('Para tu interés') }}</h2><p class="interes-intro">{{ __($recomendaciones['mensaje']) }}</p></div>
    <div class="interes-grid">
        @foreach ($recomendaciones['tours'] as $recomendado)
            <article class="interes-card">
                <a href="{{ route('tours.show', $recomendado) }}"><img data-imagen-tour src="{{ $recomendado->img }}" alt="{{ $recomendado->img_alt }}" class="interes-imagen" loading="lazy"></a>
                <div class="interes-cuerpo"><p class="interes-categoria">{{ $recomendado->categoria->nom }} · {{ __($recomendado->dif_txt) }}</p><h3 class="interes-nombre"><a href="{{ route('tours.show', $recomendado) }}">{{ $recomendado->nom }}</a></h3><p class="interes-motivo">{{ __($recomendado->motivo_recomendacion) }}</p><div class="interes-pie"><strong>Bs {{ number_format($recomendado->pre_bas, 0, ',', '.') }}</strong><a href="{{ route('tours.show', $recomendado) }}" class="interes-enlace">{{ __('Ver tour') }} →</a></div></div>
            </article>
        @endforeach
    </div>
</section>
@endif
