<?php

namespace App\Livewire;

use App\Models\CategoriaTour;
use App\Services\Catalogo\ConsultaCatalogo;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class CatalogoTours extends Component
{
    #[Url(except: '')]
    public string $q = '';

    #[Url(except: 'todos')]
    public string $categoria = 'todos';

    #[Url(except: '')]
    public string $fecha = '';

    #[Url(except: '')]
    public string $dificultad = '';

    #[Url(except: '')]
    public string $altitud = '';

    #[On('filtrar-categoria')]
    public function filtrarCategoria(string $categoria): void
    {
        $this->categoria = $categoria;
    }

    public function limpiar(): void
    {
        $this->reset('q', 'categoria', 'fecha', 'dificultad', 'altitud');
    }

    public function render()
    {
        $filtros = $this->validate([
            'q' => ['string', 'max:120'], 'categoria' => ['string', 'max:100'],
            'fecha' => ['nullable', 'date_format:Y-m-d'], 'dificultad' => ['nullable', 'in:facil,moderado,dificil,experto'],
            'altitud' => ['nullable', 'in:0-3000,3000-4000,4000-5000,5000-9000'],
        ]);

        return view('livewire.catalogo-tours', [
            'tours' => app(ConsultaCatalogo::class)->buscar($filtros),
            'categorias' => CategoriaTour::whereHas('tours', fn ($q) => $q->where('act', true)->whereNull('eli_en'))->orderBy('nom')->get(),
        ]);
    }
}
