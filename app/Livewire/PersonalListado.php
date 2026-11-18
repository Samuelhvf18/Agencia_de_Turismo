<?php

namespace App\Livewire;

use App\Models\PerfilChofer;
use App\Models\PerfilGuia;
use App\Models\SalidaTour;
use App\Support\AccesoPanel;
use Livewire\Component;
use Livewire\WithPagination;

class PersonalListado extends Component
{
    use WithPagination;

    public string $buscar = '';

    public string $tipo = 'guia';

    public string $estado = 'todos';

    public function updated(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        AccesoPanel::exigir();
        $this->validate(['buscar' => ['string', 'max:120'], 'tipo' => ['in:guia,chofer'], 'estado' => ['in:todos,activo,inactivo']]);
        $modelo = $this->tipo === 'guia' ? PerfilGuia::class : PerfilChofer::class;
        $perfiles = $modelo::with($this->tipo === 'guia' ? ['usuario', 'idiomas'] : ['usuario'])
            ->withCount(['salidas as futuras' => fn ($q) => $q->where('est', SalidaTour::EST_PROGRAMADA)->whereDate('fec_sal', '>=', today())])
            ->when($this->buscar !== '', fn ($q) => $q->whereHas('usuario', fn ($u) => $u->where(fn ($u) => $u
                ->where('nom', 'ILIKE', '%'.$this->buscar.'%')->orWhere('cor', 'ILIKE', '%'.$this->buscar.'%'))))
            ->when($this->estado !== 'todos', fn ($q) => $q->where('act', $this->estado === 'activo'))
            ->orderBy('id')->paginate(12);

        return view('livewire.personal-listado', compact('perfiles'));
    }
}
