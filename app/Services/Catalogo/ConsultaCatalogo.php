<?php

namespace App\Services\Catalogo;

use App\Models\Tour;
use App\Support\PresentadorTour;

class ConsultaCatalogo
{
    public function buscar(array $filtros = [])
    {
        $altitudes = ['0-3000' => [0, 3000], '3000-4000' => [3000, 4000], '4000-5000' => [4000, 5000], '5000-9000' => [5000, 9000]];

        return Tour::with(['categoria', 'fotos' => fn ($q) => $q->orderBy('ord_vis')->orderBy('id')])
            ->where('act', true)->whereNull('eli_en')
            ->when($filtros['q'] ?? '', fn ($q, $texto) => $q->where(fn ($q) => $q->where('nom', 'ILIKE', '%'.$texto.'%')->orWhere('cod', 'ILIKE', '%'.$texto.'%')))
            ->when(($filtros['categoria'] ?? 'todos') !== 'todos' && ! empty($filtros['categoria']), fn ($q) => $q->whereHas('categoria', fn ($c) => $c->where('rta_amg', $filtros['categoria'])))
            ->when($filtros['dificultad'] ?? '', fn ($q, $d) => $q->where('niv_dif', $d))
            ->when($altitudes[$filtros['altitud'] ?? ''] ?? null, fn ($q, $limites) => $q->whereBetween('alt_max_m', $limites))
            ->when($filtros['fecha'] ?? '', fn ($q, $fecha) => $q->whereHas('salidas', fn ($s) => $s
                ->whereDate('fec_sal', $fecha)->whereDate('fec_sal', '>=', today())->where('est', 'programada')
                ->whereRaw("cap_max > (SELECT COALESCE(SUM(cnt_ptc), 0) FROM reservas WHERE sal_tur_id = salidas_tour.id AND est <> 'cancelada')")))
            ->orderBy('cod')->get()->map(fn (Tour $tour) => PresentadorTour::presentar($tour))->filter(fn (Tour $tour) => $tour->img)->values();
    }
}
