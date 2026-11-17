<?php

namespace App\Livewire;

use App\Models\SalidaTour;
use App\Models\Vehiculo;
use App\Support\AccesoPanel;
use Livewire\Component;

class FlotaListado extends Component
{
    public string $buscar = '';

    public string $estado = 'todos';

    public function render()
    {
        AccesoPanel::exigir();
        $this->validate(['buscar' => ['string', 'max:120'], 'estado' => ['in:todos,activo,mantenimiento,inactivo']]);
        $vehiculos = Vehiculo::withCount(['salidas as futuras' => fn ($q) => $q->where('est', SalidaTour::EST_PROGRAMADA)->whereDate('fec_sal', '>=', today())])
            ->withSum('viajes as km', 'dst_km')->withSum('viajes as combustible', 'cos_cmb')
            ->when($this->buscar !== '', fn ($q) => $q->where(fn ($q) => $q->where('pla', 'ILIKE', '%'.$this->buscar.'%')->orWhere('mar', 'ILIKE', '%'.$this->buscar.'%')->orWhere('mdl', 'ILIKE', '%'.$this->buscar.'%')))
            ->when($this->estado !== 'todos', fn ($q) => $q->where('est', $this->estado))->orderBy('pla')->get();

        return view('livewire.flota-listado', compact('vehiculos'));
    }
}
