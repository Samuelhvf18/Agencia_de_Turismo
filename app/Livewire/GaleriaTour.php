<?php

namespace App\Livewire;

use App\Models\FotoTour;
use App\Models\Tour;
use App\Services\Catalogo\GaleriaTours;
use App\Support\AccesoPanel;
use App\Support\PresentadorTour;
use App\Support\ReglasEntrada;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class GaleriaTour extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $tourId;

    public array $fotos = [];

    public array $textos = [];

    public string $texto = '';

    public string $estado = '';

    public function mount(Tour $tour): void
    {
        AccesoPanel::exigir();
        $this->tourId = $tour->id;
        $this->cargarTextos();
    }

    public function subir(): void
    {
        AccesoPanel::exigir();
        $this->validate(['fotos' => ['required', 'array', 'min:1', 'max:10'], 'fotos.*' => ReglasEntrada::imagen(), 'texto' => ['nullable', 'string', 'max:150']]);
        app(GaleriaTours::class)->agregar(Tour::findOrFail($this->tourId), $this->fotos, $this->texto);
        $this->reset('fotos', 'texto');
        $this->cargarTextos();
        $this->estado = 'Fotos agregadas. Ya están disponibles en el catálogo y el chatbot.';
    }

    public function guardarTextos(): void
    {
        AccesoPanel::exigir();
        $this->validate(['textos' => ['array', 'max:30'], 'textos.*' => ['nullable', 'string', 'max:150']]);
        $tour = Tour::findOrFail($this->tourId);
        app(GaleriaTours::class)->ordenar($tour, $tour->fotos()->orderBy('ord_vis')->orderBy('id')->pluck('id')->all(), $this->textos);
        $this->estado = 'Textos alternativos guardados.';
    }

    public function mover(int $fotoId, int $paso): void
    {
        AccesoPanel::exigir();
        abort_unless(in_array($paso, [-1, 1], true), 422);
        $tour = Tour::findOrFail($this->tourId);
        $orden = $tour->fotos()->orderBy('ord_vis')->orderBy('id')->pluck('id')->all();
        $i = array_search($fotoId, $orden, true);
        abort_if($i === false, 404);
        $j = $i + $paso;
        if (isset($orden[$j])) {
            [$orden[$i], $orden[$j]] = [$orden[$j], $orden[$i]];
            app(GaleriaTours::class)->ordenar($tour, $orden);
        }
        $this->estado = 'Orden actualizado. La primera imagen es la portada.';
    }

    public function eliminar(int $fotoId): void
    {
        AccesoPanel::exigir();
        $tour = Tour::findOrFail($this->tourId);
        $foto = $tour->fotos()->find($fotoId);
        abort_unless($foto, 404);
        app(GaleriaTours::class)->eliminar($tour, $foto);
        $this->cargarTextos();
        $this->estado = 'Foto eliminada.';
    }

    protected function cargarTextos(): void
    {
        $this->textos = FotoTour::where('tur_id', $this->tourId)->pluck('txt_atv', 'id')->all();
    }

    public function render()
    {
        AccesoPanel::exigir();

        return view('livewire.galeria-tour', ['imagenes' => FotoTour::where('tur_id', $this->tourId)->orderBy('ord_vis')->orderBy('id')->get()
            ->map(fn ($f) => ['foto' => $f, 'url' => PresentadorTour::urlFoto($f->rta_arc)])]);
    }
}
