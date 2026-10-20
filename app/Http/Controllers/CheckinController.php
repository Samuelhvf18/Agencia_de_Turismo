<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\FichaMedica;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\User;
use App\Observers\AuditObserver;
use App\Services\Operaciones\SalidaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Check-in desde el celular del guía o chofer (RF-18, RF-21, RNF-15):
 * escaneo de QR con la cámara, inicio y fin real del recorrido, y
 * sincronización de los escaneos hechos sin conexión.
 */
class CheckinController extends Controller
{
    public function __construct(private SalidaService $salidas) {}

    public function index(Request $request): View
    {
        $usuario = $request->user();

        $salidas = SalidaTour::with('tour', 'guia.usuario', 'vehiculo')
            ->withSum(['reservas as pasajeros' => fn ($q) => $q->where('est', Reserva::EST_CONFIRMADA)], 'cnt_ptc')
            ->whereBetween('fec_sal', [today()->subDay()->toDateString(), today()->addDays(7)->toDateString()])
            ->whereIn('est', [SalidaTour::EST_PROGRAMADA, SalidaTour::EST_EN_CURSO])
            ->when($usuario->tieneRol(User::ROL_GUIA), fn ($q) => $q->where('prf_gui_id', $usuario->perfilGuia?->id ?? 0))
            ->when($usuario->tieneRol(User::ROL_CHOFER), fn ($q) => $q->where('prf_cho_id', $usuario->perfilChofer?->id ?? 0))
            ->orderBy('fec_sal')->orderBy('hra_sal')
            ->get();

        return view('guias.index', ['salidas' => $salidas]);
    }

    public function show(Request $request, SalidaTour $salida): View
    {
        $this->autorizar($request, $salida);
        $salida->load('tour', 'guia.usuario', 'chofer.usuario', 'vehiculo');

        return view('guias.checkin', [
            'salida' => $salida,
            'manifiesto' => $this->manifiesto($salida),
        ]);
    }

    /** Manifiesto en JSON (lo guarda la página en IndexedDB para trabajar sin conexión). */
    public function manifiestoJson(Request $request, SalidaTour $salida): JsonResponse
    {
        $this->autorizar($request, $salida);

        return response()->json(['salida' => $salida->id, 'generado' => now()->toIso8601String(), 'pasajeros' => $this->manifiesto($salida)]);
    }

    public function validar(Request $request, SalidaTour $salida): JsonResponse
    {
        $this->autorizar($request, $salida);
        $datos = $request->validate(['cod' => ['required', 'string', 'max:64']]);

        $resultado = $this->salidas->validarBoleto($salida, $datos['cod'], $request->user());

        return response()->json($resultado, $resultado['ok'] ? 200 : 422);
    }

    /** Escaneos hechos sin conexión: [{cod, vld_en}] */
    public function sincronizar(Request $request, SalidaTour $salida): JsonResponse
    {
        $this->autorizar($request, $salida);
        $datos = $request->validate([
            'escaneos' => ['required', 'array', 'max:200'],
            'escaneos.*.cod' => ['required', 'string', 'max:64'],
            'escaneos.*.vld_en' => ['nullable', 'date', 'before_or_equal:now'],
        ]);

        $resultados = collect($datos['escaneos'])->map(fn ($e) => $this->salidas->validarBoleto(
            $salida, $e['cod'], $request->user(), $e['vld_en'] ?? null
        ))->values();

        return response()->json(['resultados' => $resultados]);
    }

    public function iniciar(Request $request, SalidaTour $salida): RedirectResponse
    {
        $this->autorizar($request, $salida);
        $this->salidas->iniciar($salida);

        return back()->with('estado', 'Recorrido iniciado a las '.now()->format('H:i').'.');
    }

    public function finalizar(Request $request, SalidaTour $salida): RedirectResponse
    {
        $this->autorizar($request, $salida);
        $datos = $request->validate([
            'dst_km' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'cos_cmb' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ]);
        $this->salidas->finalizar($salida, isset($datos['dst_km']) ? (float) $datos['dst_km'] : null, isset($datos['cos_cmb']) ? (float) $datos['cos_cmb'] : null);

        return redirect()->route('guias.checkin')->with('estado', 'Recorrido finalizado. ¡Buen trabajo!');
    }

    private function manifiesto(SalidaTour $salida): array
    {
        // RNF-05: el guía ve condiciones médicas; esa lectura queda en la bitácora.
        FichaMedica::whereHas('participante.reserva', fn ($q) => $q->where('sal_tur_id', $salida->id)->where('est', Reserva::EST_CONFIRMADA))
            ->get()->each(fn ($f) => AuditObserver::lectura($f));

        return Reserva::with('participantes.boleto', 'participantes.fichaMedica', 'participantes.alquileres.item.tipo')
            ->where('sal_tur_id', $salida->id)
            ->where('est', Reserva::EST_CONFIRMADA)
            ->get()
            ->flatMap(fn (Reserva $r) => $r->participantes->map(fn ($p) => [
                'reserva' => $r->cod,
                'nom_cpl' => $p->nom_cpl,
                'documento' => trim($p->tip_doc.' '.$p->num_doc.($p->ci_cmp ? '-'.$p->ci_cmp : '')),
                'boleto' => $p->boleto?->cod,
                'est' => $p->boleto?->est ?? 'sin_boleto',
                'vld_en' => $p->boleto?->vld_en?->format('H:i'),
                'ficha' => (bool) $p->fichaMedica,
                'alerta_medica' => $p->fichaMedica && ! in_array(mb_strtolower(trim((string) $p->fichaMedica->con_med)), ['', 'ninguna', 'ninguno', 'no'], true)
                    ? $p->fichaMedica->con_med : null,
                'equipo' => $p->alquileres->map(fn ($a) => $a->item->tipo->nom.' ('.$a->item->tal.')')->implode(', '),
            ]))
            ->values()
            ->all();
    }

    /** Guía o chofer asignados a la salida; operaciones y admin pueden ver todas. */
    private function autorizar(Request $request, SalidaTour $salida): void
    {
        $u = $request->user();
        $permitido = $u->tieneRol([User::ROL_ADMIN, User::ROL_OPERACIONES])
            || ($u->tieneRol(User::ROL_GUIA) && $salida->prf_gui_id && $salida->prf_gui_id === $u->perfilGuia?->id)
            || ($u->tieneRol(User::ROL_CHOFER) && $salida->prf_cho_id && $salida->prf_cho_id === $u->perfilChofer?->id);

        abort_unless($permitido, 403, 'Esta salida no está asignada a ti.');
    }
}
