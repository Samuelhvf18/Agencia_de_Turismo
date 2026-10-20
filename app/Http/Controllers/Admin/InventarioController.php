<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipoPorTour;
use App\Models\ItemEquipo;
use App\Models\TipoEquipo;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Inventario de equipo de alquiler (M03, RF-20): tipos (con costos para el
 * EOQ), existencias por talla y equipo ofrecido en cada tour. El descuento
 * y la devolución de stock son automáticos al confirmar, cancelar o
 * completar una reserva.
 */
class InventarioController extends Controller
{
    public function index(): View
    {
        return view('admin.panels.inventario', [
            'tipos' => TipoEquipo::with(['items' => fn ($q) => $q->orderBy('id')])->orderBy('nom')->get(),
            'tours' => Tour::with('equipos')->where('act', true)->orderBy('nom')->get(),
        ]);
    }

    public function guardarTipo(Request $request, ?TipoEquipo $tipo = null): RedirectResponse
    {
        $datos = $request->validate([
            'nom' => ['required', 'string', 'max:80', Rule::unique('tipos_equipo', 'nom')->ignore($tipo?->id)],
            'des' => ['nullable', 'string', 'max:500'],
            'pre_alq_bas' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'cos_ord' => ['nullable', 'numeric', 'gt:0', 'max:9999999.99'],
            'cos_mnt_anu' => ['nullable', 'numeric', 'gt:0', 'max:9999999.99'],
            'eqp_cri' => ['nullable', 'boolean'],
        ]);
        $datos['eqp_cri'] = $request->boolean('eqp_cri');

        $tipo ? $tipo->update($datos) : TipoEquipo::create($datos);

        return back()->with('estado', 'Tipo de equipo guardado.');
    }

    /** Agrega una talla o ajusta su total; las unidades alquiladas no se pueden quitar. */
    public function guardarItem(Request $request, TipoEquipo $tipo): RedirectResponse
    {
        $datos = $request->validate([
            'tal' => ['required', 'string', 'max:20'],
            'cnt_tot' => ['required', 'integer', 'min:0', 'max:100000'],
            'con' => ['nullable', 'in:bueno,regular,malo'],
        ]);

        $item = ItemEquipo::firstOrNew(['tip_eqp_id' => $tipo->id, 'tal' => trim($datos['tal'])]);
        $alquiladas = $item->exists ? $item->cnt_tot - $item->cnt_dis : 0;
        if ($datos['cnt_tot'] < $alquiladas) {
            return back()->withErrors(['inventario' => "Hay {$alquiladas} unidad(es) de la talla {$item->tal} alquiladas; el total no puede ser menor."]);
        }

        $item->fill([
            'cnt_tot' => $datos['cnt_tot'],
            'cnt_dis' => $datos['cnt_tot'] - $alquiladas,
            'con' => $datos['con'] ?? ($item->con ?? 'bueno'),
        ])->save();

        return back()->with('estado', "Talla {$item->tal} de {$tipo->nom}: {$item->cnt_tot} en total, {$item->cnt_dis} disponibles.");
    }

    /** Habilita o deshabilita un equipo en un tour, con precio especial opcional. */
    public function equipoTour(Request $request, Tour $tour): RedirectResponse
    {
        $datos = $request->validate([
            'tip_eqp_id' => ['required', 'integer', 'exists:tipos_equipo,id'],
            'act' => ['required', 'boolean'],
            'pre_alq_esp' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
        ]);

        EquipoPorTour::updateOrCreate(
            ['tur_id' => $tour->id, 'tip_eqp_id' => $datos['tip_eqp_id']],
            ['act' => (bool) $datos['act'], 'pre_alq_esp' => $datos['pre_alq_esp'] ?? null]
        );

        return back()->with('estado', "Equipo del tour {$tour->nom} actualizado.");
    }
}
