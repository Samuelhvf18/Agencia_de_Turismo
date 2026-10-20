<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservas\ElegirSalidaRequest;
use App\Http\Requests\Reservas\FichaMedicaRequest;
use App\Http\Requests\Reservas\ParticipantesRequest;
use App\Models\DepartamentoBolivia;
use App\Models\EquipoPorTour;
use App\Models\ItemEquipo;
use App\Models\Pais;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Services\Reservas\BorradorReserva;
use App\Services\Reservas\CupoAgotadoException;
use App\Services\Reservas\FirmaDigital;
use App\Services\Reservas\ReservaService;
use App\Support\PresentadorTour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Motor de reservas (Módulo 05, RF-05 a RF-12): wizard de 5 pasos sobre un
 * borrador en sesión (salida → participantes → equipo → ficha médica →
 * resumen). La reserva se crea al confirmar, con control transaccional de cupo.
 */
class ReservaController extends Controller
{
    public function __construct(
        private BorradorReserva $borrador,
        private ReservaService $reservas,
    ) {}

    // ---- Paso 1: salida ---------------------------------------------------------

    public function salida(Tour $tour): View
    {
        abort_unless($tour->act && is_null($tour->eli_en), 404);
        $tour->load(['categoria', 'fotos' => fn ($q) => $q->orderBy('ord_vis')]);
        PresentadorTour::presentar($tour);

        $salidas = SalidaTour::where('tur_id', $tour->id)
            ->where('est', SalidaTour::EST_PROGRAMADA)
            ->whereDate('fec_sal', '>=', today())
            ->withSum(['reservas as cup_ocu' => fn ($q) => $q->where('est', '<>', Reserva::EST_CANCELADA)], 'cnt_ptc')
            ->orderBy('fec_sal')->orderBy('hra_sal')
            ->get()
            // Salidas de hoy cuya hora ya pasó no se ofrecen.
            ->reject(fn ($s) => $s->fec_sal->isToday() && $s->hra_sal <= now()->format('H:i:s'))
            ->map(fn (SalidaTour $s) => $this->salidaArray($s) + ['est' => $s->est, 'cup_ocu' => (int) $s->cup_ocu])
            ->values()
            ->all();

        return view('reservas.salida', [
            'tour' => $tour,
            'salidas' => $salidas,
            'accionForm' => route('reservas.salida.store', $tour),
        ]);
    }

    public function guardarSalida(ElegirSalidaRequest $request, Tour $tour): RedirectResponse
    {
        $salida = SalidaTour::where('tur_id', $tour->id)->findOrFail($request->integer('sal_tur_id'));

        if ($salida->est !== SalidaTour::EST_PROGRAMADA || $salida->fec_sal->lt(today())) {
            return back()->withErrors(['sal_tur_id' => 'Esta salida ya no admite reservas. Elige otra.'])->withInput();
        }

        $cantidad = $request->integer('cnt_ptc');
        $disponibles = $salida->cap_max - $this->reservas->cuposOcupados($salida->id);
        if ($cantidad > $disponibles) {
            $this->reservas->registrarRechazo($salida, $cantidad);

            return back()->withErrors(['cnt_ptc' => (new CupoAgotadoException(max(0, $disponibles)))->getMessage()])->withInput();
        }

        $this->borrador->iniciar($tour->id, $salida->id, $cantidad);

        return redirect()->route('reservas.participantes');
    }

    // ---- Paso 2: participantes -------------------------------------------------

    public function participantes(Request $request): View|RedirectResponse
    {
        if ($r = $this->exigirPaso('salida')) {
            return $r;
        }
        [$tour, $salida] = $this->contexto();
        $usuario = $request->user();
        $cantidad = (int) $this->borrador->get('cnt_ptc', 1);

        $participantes = $this->borrador->get('participantes') ?: array_map(fn ($i) => [
            'nom_cpl' => $i === 0 && $usuario ? $usuario->nom : '',
            'tip_doc' => 'CI', 'num_doc' => '', 'ci_cmp' => '', 'ci_dep_id' => null, 'eda' => '', 'pai_id' => null,
            'es_ctc_pri' => $i === 0,
        ], range(0, $cantidad - 1));

        return view('reservas.participantes', [
            'tour' => $tour,
            'salida' => $this->salidaArray($salida) + ['cap_max' => $salida->cap_max, 'cup_dis' => $salida->cap_max - $this->reservas->cuposOcupados($salida->id)],
            'cnt_ptc' => count($participantes),
            'contacto' => $this->borrador->get('contacto') ?: [
                'nom_ctc' => $usuario?->nom ?? '', 'cor_ctc' => $usuario?->cor ?? '', 'tel_ctc' => $usuario?->tel ?? '',
            ],
            'participantes' => $participantes,
            'paises' => Pais::orderBy('nom')->get(['id', 'nom'])->toArray(),
            'departamentos' => DepartamentoBolivia::orderBy('id')->get(['id', 'cod', 'nom'])->toArray(),
            'accionForm' => route('reservas.participantes.store'),
            'urlAnterior' => route('reservas.salida', $tour),
        ]);
    }

    public function guardarParticipantes(ParticipantesRequest $request): RedirectResponse
    {
        if ($r = $this->exigirPaso('salida')) {
            return $r;
        }
        [, $salida] = $this->contexto();

        $participantes = array_values($request->validated('participantes'));
        $disponibles = $salida->cap_max - $this->reservas->cuposOcupados($salida->id);
        if (count($participantes) > $disponibles) {
            $this->reservas->registrarRechazo($salida, count($participantes));

            return back()->withErrors(['participantes' => (new CupoAgotadoException(max(0, $disponibles)))->getMessage()])->withInput();
        }

        $participantes = array_map(fn ($p) => [
            'nom_cpl' => $p['nom_cpl'],
            'tip_doc' => $p['tip_doc'],
            'num_doc' => $p['num_doc'],
            'ci_cmp' => $p['tip_doc'] === 'CI' ? ($p['ci_cmp'] ?? '') : '',
            'ci_dep_id' => $p['tip_doc'] === 'CI' ? (int) $p['ci_dep_id'] : null,
            'eda' => (int) $p['eda'],
            'pai_id' => (int) $p['pai_id'],
            'es_ctc_pri' => ! empty($p['es_ctc_pri']),
        ], $participantes);

        $this->borrador->guardar('contacto', $request->only(['nom_ctc', 'cor_ctc', 'tel_ctc']));
        $this->borrador->guardar('participantes', $participantes);
        $this->borrador->guardar('cnt_ptc', count($participantes));
        // Si se quitaron participantes, se descartan su equipo y su ficha.
        $n = count($participantes);
        $this->borrador->guardar('alquileres', array_slice($this->borrador->get('alquileres', []), 0, $n, true));
        $this->borrador->guardar('fichas', array_slice($this->borrador->get('fichas', []), 0, $n, true));

        return redirect()->route('reservas.equipo');
    }

    // ---- Paso 3: equipo --------------------------------------------------------

    public function equipo(): View|RedirectResponse
    {
        if ($r = $this->exigirPaso('participantes')) {
            return $r;
        }
        [$tour, $salida] = $this->contexto();

        return view('reservas.equipo', [
            'tour' => $tour,
            'salida' => $this->salidaArray($salida),
            'participantes' => $this->participantesConId(),
            'equipos' => $this->equiposDelTour($tour->id),
            'seleccion' => $this->borrador->get('alquileres', []),
            'accionForm' => route('reservas.equipo.store'),
            'urlAnterior' => route('reservas.participantes'),
            'urlSalida' => route('reservas.salida', $tour),
        ]);
    }

    public function guardarEquipo(Request $request): RedirectResponse
    {
        if ($r = $this->exigirPaso('participantes')) {
            return $r;
        }
        [$tour] = $this->contexto();

        $equipos = collect($this->equiposDelTour($tour->id))->keyBy('tip_eqp_id');
        $indices = array_keys($this->borrador->get('participantes', []));
        $seleccion = [];
        $uso = [];
        $errores = [];

        foreach ((array) $request->input('alquileres', []) as $i => $porTipo) {
            if (! in_array((int) $i, $indices, true)) {
                continue;
            }
            foreach ((array) $porTipo as $tipId => $itemId) {
                if (! $itemId) {
                    continue;
                }
                $equipo = $equipos->get((int) $tipId);
                $item = $equipo ? collect($equipo['items'])->firstWhere('id', (int) $itemId) : null;
                if (! $item) {
                    $errores["alquileres.$i.$tipId"] = 'Elige una talla válida.';

                    continue;
                }
                $uso[$item['id']] = ($uso[$item['id']] ?? 0) + 1;
                if ($uso[$item['id']] > $item['cnt_dis']) {
                    $errores["alquileres.$i.$tipId"] = "No queda stock de {$equipo['nom']} talla {$item['tal']}.";

                    continue;
                }
                $seleccion[(int) $i][(int) $tipId] = (int) $itemId;
            }
        }

        if ($errores) {
            throw ValidationException::withMessages($errores);
        }

        $this->borrador->guardar('alquileres', $seleccion);

        return redirect()->route('reservas.ficha');
    }

    // ---- Paso 4: ficha médica ---------------------------------------------------

    public function ficha(): View|RedirectResponse
    {
        if ($r = $this->exigirPaso('participantes')) {
            return $r;
        }
        [$tour] = $this->contexto();

        return view('reservas.ficha', [
            'tour' => $tour,
            'altoRiesgo' => $tour->esAltoRiesgo(),
            'participantes' => $this->participantesConId(),
            'fichas' => $this->borrador->get('fichas', []),
            'accionForm' => route('reservas.ficha.store'),
            'urlAnterior' => route('reservas.equipo'),
            'urlSalida' => route('reservas.salida', $tour),
            'urlParticipantes' => route('reservas.participantes'),
        ]);
    }

    public function guardarFicha(FichaMedicaRequest $request): RedirectResponse
    {
        if ($r = $this->exigirPaso('participantes')) {
            return $r;
        }

        $fichas = $this->borrador->get('fichas', []);
        foreach (array_keys($this->borrador->get('participantes', [])) as $i) {
            $f = $request->input("fichas.$i", []);
            $previa = $fichas[$i] ?? [];

            $ruta = ! empty($f['fir_b64'])
                ? FirmaDigital::guardarTemporal($f['fir_b64'], "fichas.$i.fir_b64")
                : ($previa['rta_fir'] ?? null);

            if (! $ruta) {
                unset($fichas[$i]); // ficha opcional sin completar

                continue;
            }

            $fichas[$i] = [
                'con_med' => $f['con_med'] ?? null,
                'nom_ctc_eme' => $f['nom_ctc_eme'] ?? null,
                'tel_ctc_eme' => $f['tel_ctc_eme'] ?? null,
                'rta_fir' => $ruta,
                'fir_en' => ! empty($f['fir_b64']) ? now()->toDateTimeString() : ($previa['fir_en'] ?? now()->toDateTimeString()),
                'dir_ip' => ! empty($f['fir_b64']) ? $request->ip() : ($previa['dir_ip'] ?? $request->ip()),
            ];
        }

        $this->borrador->guardar('fichas', $fichas);

        return redirect()->route('reservas.resumen');
    }

    // ---- Paso 5: resumen y confirmación ------------------------------------------

    public function resumen(): View|RedirectResponse
    {
        if ($r = $this->exigirPaso('participantes')) {
            return $r;
        }
        [$tour, $salida] = $this->contexto();

        $paises = Pais::pluck('nom', 'id');
        $items = ItemEquipo::with('tipo')->get()->keyBy('id');
        $precios = $this->reservas->preciosEquipo($tour->id);
        $alquileres = $this->borrador->get('alquileres', []);
        $fichas = $this->borrador->get('fichas', []);

        $participantes = collect($this->borrador->get('participantes', []))->map(fn ($p, $i) => $p + [
            'id' => $i,
            'pais' => $paises[$p['pai_id']] ?? '—',
            'ficha_ok' => ! empty($fichas[$i]['rta_fir']),
            'alquileres' => collect($alquileres[$i] ?? [])->filter()->map(fn ($itemId, $tipId) => [
                'nom' => $items[$itemId]->tipo->nom ?? 'Equipo',
                'tal' => $items[$itemId]->tal ?? '',
                'pre' => $precios[$tipId] ?? 0,
            ])->values()->all(),
        ])->values()->all();

        return view('reservas.resumen', [
            'tour' => $tour,
            'salida' => $this->salidaArray($salida) + ['gui_nom' => $salida->guia?->usuario?->nom],
            'reserva' => $this->borrador->get('contacto') + ['cnt_ptc' => count($participantes)],
            'participantes' => $participantes,
            'altoRiesgo' => $tour->esAltoRiesgo(),
            'accionForm' => route('reservas.confirmar'),
            'editar' => [
                'salida' => route('reservas.salida', $tour),
                'participantes' => route('reservas.participantes'),
                'equipo' => route('reservas.equipo'),
                'ficha' => route('reservas.ficha'),
            ],
        ]);
    }

    public function confirmar(Request $request): RedirectResponse
    {
        if ($r = $this->exigirPaso('participantes')) {
            return $r;
        }
        $request->validate(['acp_ter' => ['accepted']], ['acp_ter.accepted' => 'Debes aceptar los términos y condiciones.']);

        try {
            $reserva = $this->reservas->confirmar($this->borrador->datos(), $request->user());
        } catch (CupoAgotadoException $e) {
            [$tour] = $this->contexto();

            return redirect()->route('reservas.salida', $tour)->withErrors(['sal_tur_id' => $e->getMessage().' Elige otra salida o reduce la cantidad de personas.']);
        } catch (ValidationException $e) {
            $destino = array_key_exists('fichas', $e->errors()) ? 'reservas.ficha'
                : (array_key_exists('alquileres', $e->errors()) ? 'reservas.equipo' : 'reservas.resumen');

            return redirect()->route($destino)->withErrors($e->errors());
        }

        $this->borrador->limpiar();
        AccesoReserva::conceder($request, $reserva);

        return redirect()->route('reservas.confirmada', $reserva);
    }

    public function confirmada(Request $request, Reserva $reserva): View
    {
        AccesoReserva::exigir($request, $reserva);
        $reserva->load('salida.tour');

        return view('reservas.confirmada', [
            'reserva' => $reserva->only(['cod', 'nom_ctc', 'cor_ctc', 'cnt_ptc', 'pre_tot', 'est', 'est_pag', 'cre_en']),
            'tour' => $reserva->salida->tour,
            'salida' => $this->salidaArray($reserva->salida),
            'urlPago' => route('pagos.create', $reserva),
            'urlEstado' => route('reservas.estado', $reserva),
        ]);
    }

    // ---- Apoyo -------------------------------------------------------------------

    private function exigirPaso(string $paso): ?RedirectResponse
    {
        if ($this->borrador->tienePaso($paso) && $this->contextoValido()) {
            return null;
        }
        $turId = $this->borrador->get('tur_id');
        $tour = $turId ? Tour::find($turId) : null;

        return $tour
            ? redirect()->route('reservas.salida', $tour)->with('aviso', 'Elige una salida para continuar.')
            : redirect()->route('home');
    }

    private function contextoValido(): bool
    {
        return SalidaTour::whereKey($this->borrador->get('sal_tur_id'))->exists();
    }

    /** @return array{0: Tour, 1: SalidaTour} */
    private function contexto(): array
    {
        $salida = SalidaTour::with('tour.categoria', 'tour.fotos', 'guia.usuario')->findOrFail($this->borrador->get('sal_tur_id'));
        $tour = PresentadorTour::presentar($salida->tour);

        return [$tour, $salida];
    }

    private function salidaArray(SalidaTour $s): array
    {
        return ['id' => $s->id, 'fec_sal' => $s->fec_sal->toDateString(), 'hra_sal' => $s->hra_sal, 'cap_max' => $s->cap_max];
    }

    private function participantesConId(): array
    {
        return collect($this->borrador->get('participantes', []))
            ->map(fn ($p, $i) => ['id' => $i, 'nom_cpl' => $p['nom_cpl'], 'eda' => $p['eda']])
            ->values()->all();
    }

    private function equiposDelTour(int $turId): array
    {
        return EquipoPorTour::with(['tipo.items' => fn ($q) => $q->orderBy('id')])
            ->where('tur_id', $turId)
            ->where('act', true)
            ->get()
            ->map(fn (EquipoPorTour $e) => [
                'tip_eqp_id' => $e->tip_eqp_id,
                'nom' => $e->tipo->nom,
                'des' => $e->tipo->des,
                'eqp_cri' => $e->tipo->eqp_cri,
                'pre' => $e->precio(),
                'items' => $e->tipo->items->map(fn ($it) => ['id' => $it->id, 'tal' => $it->tal, 'cnt_dis' => $it->cnt_dis])->all(),
            ])->all();
    }
}
