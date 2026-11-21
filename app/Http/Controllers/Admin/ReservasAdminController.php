<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParticipanteReserva;
use App\Models\Reserva;
use App\Models\Tour;
use App\Observers\AuditObserver;
use App\Services\Pagos\PagoService;
use App\Services\Reservas\BoletoService;
use App\Services\Reservas\ReservaService;
use App\Services\Reservas\SeguridadParticipantes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Gestión de reservas y pasajeros. */
class ReservasAdminController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'est' => ['nullable', 'in:pendiente,confirmada,cancelada,completada'],
            'tur_id' => ['nullable', 'integer'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $reservas = Reserva::with('salida.tour')
            ->withCount('participantes')
            ->when($filtros['q'] ?? null, function ($q, $texto) {
                $like = '%'.mb_strtolower($texto).'%';
                $q->where(fn ($w) => $w->whereRaw('LOWER(cod) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(nom_ctc) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(cor_ctc) LIKE ?', [$like])
                    ->orWhereHas('participantes', fn ($p) => $p->whereRaw('LOWER(nom_cpl) LIKE ?', [$like])->orWhere('num_doc', 'like', '%'.strtoupper($texto).'%')));
            })
            ->when($filtros['est'] ?? null, fn ($q, $est) => $q->where('est', $est))
            ->when($filtros['tur_id'] ?? null, fn ($q, $tur) => $q->whereHas('salida', fn ($s) => $s->where('tur_id', $tur)))
            ->when($filtros['desde'] ?? null, fn ($q, $d) => $q->whereHas('salida', fn ($s) => $s->whereDate('fec_sal', '>=', $d)))
            ->when($filtros['hasta'] ?? null, fn ($q, $h) => $q->whereHas('salida', fn ($s) => $s->whereDate('fec_sal', '<=', $h)))
            ->orderByDesc('cre_en')
            ->paginate(20)
            ->withQueryString();

        $resumen = Reserva::selectRaw('est, COUNT(*) AS n, COALESCE(SUM(cnt_ptc), 0) AS ptc')->groupBy('est')->get()->keyBy('est');

        return view('admin.panels.reservas', [
            'reservas' => $reservas,
            'resumen' => $resumen,
            'tours' => Tour::orderBy('nom')->pluck('nom', 'id'),
            'filtros' => $filtros,
        ]);
    }

    public function show(Reserva $reserva, PagoService $pagos): View
    {
        $reserva->load('salida.tour', 'salida.guia.usuario', 'usuario', 'participantes.pais', 'participantes.departamento',
            'participantes.boleto', 'participantes.fichaMedica', 'participantes.alquileres.item.tipo', 'comprobante.pagos');

        // Audita datos sensibles.
        foreach ($reserva->participantes as $p) {
            AuditObserver::lectura($p);
            if ($p->fichaMedica) {
                AuditObserver::lectura($p->fichaMedica);
            }
        }

        return view('admin.reservas.show', [
            'reserva' => $reserva,
            'saldo' => $pagos->saldo($reserva),
            'metodos' => PagoService::METODOS + ['efectivo' => 'Efectivo'],
        ]);
    }

    public function verificarMenor(Request $request, Reserva $reserva, ParticipanteReserva $participante)
    {
        abort_unless($participante->rsv_id === $reserva->id, 404);
        $request->validate(['documentos_originales' => ['accepted']]);
        abort_unless(in_array($reserva->est, [Reserva::EST_PENDIENTE, Reserva::EST_CONFIRMADA], true), 422);
        $reserva->load('salida.tour');
        $seguridad = app(SeguridadParticipantes::class);
        $aut = $participante->autorizacion_menor;
        abort_unless($aut && $participante->fecha_nacimiento && $seguridad->edad($participante->fecha_nacimiento->toDateString(), $reserva->salida) < 18, 422, 'No hay autorización de menor que verificar.');
        // Aplica las restricciones vigentes.
        abort_unless($reserva->salida->tour->admiteMenores() && $seguridad->edad($participante->fecha_nacimiento->toDateString(), $reserva->salida) >= $reserva->salida->tour->edadMinimaEfectiva(), 422, 'Este tour no admite al menor.');
        $acompanante = $participante->acompanante;
        abort_unless($acompanante && $acompanante->rsv_id === $reserva->id && $acompanante->fecha_nacimiento && $seguridad->edad($acompanante->fecha_nacimiento->toDateString(), $reserva->salida) >= 18, 422, 'Falta un acompañante adulto válido.');
        $aut['verificada_en'] = now()->toIso8601String();
        $aut['verificada_por'] = $request->user()->id;
        $aut['huella'] = $seguridad->huella($participante, $reserva->salida);
        $participante->update(['autorizacion_menor' => $aut]);
        if ($reserva->est_pag === Reserva::PAG_PAGADO) {
            app(BoletoService::class)->emitir($reserva);
        }

        return back()->with('estado', 'Documentación del menor verificada para esta salida.');
    }

    public function cancelar(Request $request, Reserva $reserva, ReservaService $servicio): RedirectResponse
    {
        $datos = $request->validate(['motivo' => ['required', 'string', 'max:200']]);
        abort_if(in_array($reserva->est, [Reserva::EST_CANCELADA, Reserva::EST_COMPLETADA], true), 422, 'La reserva ya está cerrada.');

        $servicio->cancelar($reserva, $datos['motivo']);

        return back()->with('estado', "Reserva {$reserva->cod} cancelada.");
    }
}
