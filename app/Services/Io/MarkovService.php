<?php

namespace App\Services\Io;

use App\Models\MarkovClienteEstado;
use App\Models\MarkovCohort;
use App\Models\MarkovCohortDistribucion;
use App\Models\MarkovEstadoCliente;
use App\Models\MarkovTransicion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Módulo 07 — Cadena de Markov absorbente del ciclo de vida del cliente.
 *
 * Estados: prospecto → reservado → completado → fidelizado / perdido (los dos últimos absorbentes).
 * Forma canónica: P = [Q R; 0 I], N = (I − Q)^−1, B = N·R, t = N·1.
 */
class MarkovService
{
    public const ORDEN = ['prospecto', 'reservado', 'completado', 'fidelizado', 'perdido'];

    /** Matriz mensual inicial de referencia (la misma que carga IoColasMarkovSeeder). */
    public const MATRIZ_INICIAL = [
        'prospecto' => ['prospecto' => 0.60, 'reservado' => 0.25, 'perdido' => 0.15],
        'reservado' => ['reservado' => 0.10, 'completado' => 0.80, 'perdido' => 0.10],
        'completado' => ['completado' => 0.25, 'reservado' => 0.20, 'fidelizado' => 0.40, 'perdido' => 0.15],
        'fidelizado' => ['fidelizado' => 1.0],
        'perdido' => ['perdido' => 1.0],
    ];

    public const ABSORBENTES = ['fidelizado', 'perdido'];

    /** Meses sin actividad a partir de los cuales un cliente se considera perdido. */
    public const MESES_INACTIVIDAD = 12;

    // ------------------------------------------------------------------ álgebra

    /** Completa la matriz con ceros para todos los pares de estados. */
    public function normalizar(array $p, array $orden = self::ORDEN): array
    {
        $out = [];
        foreach ($orden as $i) {
            foreach ($orden as $j) {
                $out[$i][$j] = (float) ($p[$i][$j] ?? 0);
            }
        }

        return $out;
    }

    /** Verifica que cada fila sume 1 (con tolerancia). */
    public function esEstocastica(array $p, float $tol = 1e-4): bool
    {
        foreach ($p as $fila) {
            if (abs(array_sum($fila) - 1) > $tol) {
                return false;
            }
        }

        return true;
    }

    /** Inversa de una matriz cuadrada por eliminación de Gauss-Jordan con pivoteo parcial. */
    public function inversa(array $a): array
    {
        $n = count($a);
        $m = [];
        for ($i = 0; $i < $n; $i++) {
            $m[$i] = array_merge(array_values($a[$i]), array_fill(0, $n, 0.0));
            $m[$i][$n + $i] = 1.0;
        }
        for ($col = 0; $col < $n; $col++) {
            $piv = $col;
            for ($r = $col + 1; $r < $n; $r++) {
                if (abs($m[$r][$col]) > abs($m[$piv][$col])) {
                    $piv = $r;
                }
            }
            if (abs($m[$piv][$col]) < 1e-12) {
                throw new \RuntimeException('La matriz I − Q es singular: revise que todos los estados transitorios puedan llegar a un estado absorbente.');
            }
            [$m[$col], $m[$piv]] = [$m[$piv], $m[$col]];
            $d = $m[$col][$col];
            for ($j = 0; $j < 2 * $n; $j++) {
                $m[$col][$j] /= $d;
            }
            for ($r = 0; $r < $n; $r++) {
                if ($r !== $col && $m[$r][$col] != 0.0) {
                    $f = $m[$r][$col];
                    for ($j = 0; $j < 2 * $n; $j++) {
                        $m[$r][$j] -= $f * $m[$col][$j];
                    }
                }
            }
        }

        return array_map(fn ($fila) => array_slice($fila, $n), $m);
    }

    /**
     * Análisis de la cadena absorbente.
     *
     * @param  array  $p  matriz [cod][cod]
     * @return array{transitorios: string[], absorbentes: string[], N: array, B: array, t: array}
     *               N[i][j] = meses esperados en j partiendo de i; B[i][a] = prob. de terminar en a; t[i] = meses hasta absorción
     */
    public function analizar(array $p, array $absorbentes = self::ABSORBENTES): array
    {
        $estados = array_keys($p);
        $tr = array_values(array_diff($estados, $absorbentes));
        $ab = array_values(array_intersect($estados, $absorbentes));

        $iq = [];
        foreach ($tr as $a => $i) {
            foreach ($tr as $b => $j) {
                $iq[$a][$b] = ($a === $b ? 1.0 : 0.0) - $p[$i][$j];
            }
        }
        $nInv = $this->inversa($iq);

        $N = $B = $t = [];
        foreach ($tr as $a => $i) {
            $t[$i] = 0.0;
            foreach ($tr as $b => $j) {
                $N[$i][$j] = $nInv[$a][$b];
                $t[$i] += $nInv[$a][$b];
            }
            foreach ($ab as $k) {
                $B[$i][$k] = 0.0;
                foreach ($tr as $b => $j) {
                    $B[$i][$k] += $nInv[$a][$b] * $p[$j][$k];
                }
            }
        }

        return ['transitorios' => $tr, 'absorbentes' => $ab, 'N' => $N, 'B' => $B, 't' => $t];
    }

    /**
     * Proyección v(n) = v(0)·P^n para n = 0..meses, partiendo de un estado.
     *
     * @return array{inicio: string, meses: int[], series: array<string, float[]>}
     */
    public function proyectar(array $p, string $inicio, int $meses = 12): array
    {
        $estados = array_keys($p);
        $v = array_fill_keys($estados, 0.0);
        $v[$inicio] = 1.0;
        $series = array_fill_keys($estados, []);

        for ($n = 0; $n <= $meses; $n++) {
            foreach ($estados as $e) {
                $series[$e][] = round($v[$e], 6);
            }
            $sig = array_fill_keys($estados, 0.0);
            foreach ($estados as $i) {
                foreach ($estados as $j) {
                    $sig[$j] += $v[$i] * $p[$i][$j];
                }
            }
            $v = $sig;
        }

        return ['inicio' => $inicio, 'meses' => range(0, $meses), 'series' => $series];
    }

    /**
     * Estima P contando transiciones mes a mes: para cada par de meses consecutivos
     * se compara el estado de cada cliente al cierre de ambos meses. Las filas sin datos
     * conservan la probabilidad previa y los estados absorbentes quedan con pii = 1.
     */
    public function estimarMatriz(array $conteos, array $previa): array
    {
        $p = [];
        foreach (self::ORDEN as $i) {
            if (in_array($i, self::ABSORBENTES, true)) {
                $p[$i] = array_fill_keys(self::ORDEN, 0.0);
                $p[$i][$i] = 1.0;
                continue;
            }
            $total = array_sum($conteos[$i] ?? []);
            foreach (self::ORDEN as $j) {
                $p[$i][$j] = $total > 0 ? ($conteos[$i][$j] ?? 0) / $total : ($previa[$i][$j] ?? 0.0);
            }
        }

        return $p;
    }

    // ------------------------------------------------------------------ datos

    /** Matriz vigente (última calibración activa del período) o la matriz inicial si aún no hay datos. */
    public function matrizVigente(string $per = 'mensual'): array
    {
        $estados = MarkovEstadoCliente::orderBy('ord_vis')->get()->keyBy('id');
        $fec = MarkovTransicion::where('per', $per)->where('act', true)->max('fec_cal');

        if (! $fec || $estados->isEmpty()) {
            return ['P' => $this->normalizar(self::MATRIZ_INICIAL), 'fec_cal' => null];
        }

        $p = [];
        MarkovTransicion::where('per', $per)->where('act', true)->whereDate('fec_cal', $fec)->get()
            ->each(function ($t) use (&$p, $estados) {
                $p[$estados[$t->est_ori_id]->cod][$estados[$t->est_des_id]->cod] = (float) $t->prb;
            });

        return ['P' => $this->normalizar($p), 'fec_cal' => Carbon::parse($fec)->toDateString()];
    }

    /** Estado de cada cliente a una fecha de corte: [usr_id => cod]. */
    public function estadosAl(Carbon $corte): array
    {
        $codigos = MarkovEstadoCliente::pluck('cod', 'id');
        $ult = MarkovClienteEstado::query()
            ->whereDate('fec_cmb', '<=', $corte->toDateString())
            ->orderBy('usr_id')->orderBy('fec_cmb')->orderBy('id')
            ->get(['usr_id', 'est_id']);

        $res = [];
        foreach ($ult as $r) {
            $res[$r->usr_id] = $codigos[$r->est_id];
        }

        return $res;
    }

    /** Clientes por estado según su último registro. */
    public function conteoActual(): array
    {
        $c = array_fill_keys(self::ORDEN, 0);
        foreach ($this->estadosAl(now()) as $cod) {
            $c[$cod]++;
        }

        return $c;
    }

    /** Registra un cambio de estado si difiere del estado actual del cliente. */
    public function registrarCambio(int $usrId, string $cod, ?string $obs = null, ?Carbon $fecha = null): ?MarkovClienteEstado
    {
        $nuevo = MarkovEstadoCliente::where('cod', $cod)->firstOrFail();
        $actual = MarkovClienteEstado::where('usr_id', $usrId)->orderByDesc('fec_cmb')->orderByDesc('id')->first();

        if ($actual && $actual->est_id === $nuevo->id) {
            return null;
        }
        if ($actual && in_array(optional($actual->estado)->cod, self::ABSORBENTES, true)) {
            return null; // los estados absorbentes (fidelizado, perdido) no se abandonan
        }

        return MarkovClienteEstado::create([
            'usr_id' => $usrId,
            'est_id' => $nuevo->id,
            'est_ant_id' => $actual?->est_id,
            'fec_cmb' => ($fecha ?? now())->toDateString(),
            'obs' => $obs,
        ]);
    }

    /**
     * Deriva el estado de cada turista a partir de sus reservas y registra los cambios:
     * reserva confirmada con salida futura → reservado; ≥ 2 tours completados → fidelizado;
     * 1 tour completado → completado; sin reservas → prospecto; sin actividad en 12 meses → perdido.
     *
     * @return int cambios registrados
     */
    public function sincronizarDesdeReservas(): int
    {
        $hoy = now()->startOfDay();
        $turistas = DB::table('users')
            ->leftJoin('roles', 'roles.id', '=', 'users.rol_id')
            ->where(fn ($q) => $q->where('roles.nom', 'turista')->orWhere('users.rol', 'turista'))
            ->get(['users.id', 'users.created_at']);

        $cambios = 0;
        foreach ($turistas as $u) {
            $res = DB::table('reservas as r')
                ->join('salidas_tour as s', 's.id', '=', 'r.sal_tur_id')
                ->where('r.usr_id', $u->id)
                ->get(['r.est', 's.fec_sal', 's.est as est_sal', 'r.cre_en']);

            $completadas = $res->filter(fn ($r) => $r->est === 'completada' || $r->est_sal === 'completada')->count();
            $pendientes = $res->filter(fn ($r) => in_array($r->est, ['pendiente', 'confirmada'], true) && $r->est_sal !== 'completada' && Carbon::parse($r->fec_sal)->gte($hoy))->count();
            $ultima = $res->max('cre_en') ?? $u->created_at;

            $cod = match (true) {
                $pendientes > 0 => 'reservado',
                $completadas >= 2 => 'fidelizado',
                $ultima && Carbon::parse($ultima)->lt($hoy->copy()->subMonths(self::MESES_INACTIVIDAD)) => 'perdido',
                $completadas === 1 => 'completado',
                default => 'prospecto',
            };

            $fecha = $cod === 'prospecto' && $u->created_at ? Carbon::parse($u->created_at) : now();
            if ($this->registrarCambio($u->id, $cod, 'Sincronizado desde reservas', $fecha)) {
                $cambios++;
            }
        }

        return $cambios;
    }

    /**
     * Recalibra la matriz mensual con las transiciones observadas en los últimos $meses meses
     * y la guarda como la nueva matriz activa (fec_cal = hoy).
     *
     * @return array{P: array, transiciones: int}
     */
    public function recalibrar(int $meses = 6): array
    {
        $previa = $this->matrizVigente()['P'];
        $conteos = [];
        $obs = 0;
        $fin = now()->startOfMonth();
        for ($m = $meses; $m >= 1; $m--) {
            $a = $this->estadosAl($fin->copy()->subMonths($m)->endOfMonth());
            $b = $this->estadosAl($fin->copy()->subMonths($m - 1)->endOfMonth());
            foreach ($a as $usr => $i) {
                $j = $b[$usr] ?? $i;
                $conteos[$i][$j] = ($conteos[$i][$j] ?? 0) + 1;
                $obs++;
            }
        }

        $p = $this->estimarMatriz($conteos, $previa);
        $ids = MarkovEstadoCliente::pluck('id', 'cod');
        $hoy = now()->toDateString();

        DB::transaction(function () use ($p, $ids, $hoy) {
            MarkovTransicion::where('per', 'mensual')->update(['act' => false]);
            foreach ($p as $i => $fila) {
                foreach ($fila as $j => $prb) {
                    if ($prb <= 0) {
                        continue;
                    }
                    MarkovTransicion::updateOrCreate(
                        ['est_ori_id' => $ids[$i], 'est_des_id' => $ids[$j], 'per' => 'mensual', 'fec_cal' => $hoy],
                        ['prb' => round($prb, 4), 'act' => true]
                    );
                }
            }
        });

        return ['P' => $p, 'transiciones' => $obs];
    }

    /**
     * Construye o actualiza las cohortes mensuales de captación (mes de alta del primer registro de estado)
     * y su distribución por estado en cada mes relativo.
     *
     * @return int cohortes procesadas
     */
    public function construirCohortes(): int
    {
        $primeros = MarkovClienteEstado::select('usr_id', DB::raw('MIN(fec_cmb) as ini'))->groupBy('usr_id')->get();
        $grupos = $primeros->groupBy(fn ($r) => Carbon::parse($r->ini)->startOfMonth()->toDateString());
        $ids = MarkovEstadoCliente::pluck('id', 'cod');
        $hoy = now();

        foreach ($grupos as $perCap => $usuarios) {
            $inicio = Carbon::parse($perCap);
            $coh = MarkovCohort::updateOrCreate(
                ['per_cap' => $perCap],
                ['nom' => 'Cohorte ' . $inicio->locale('es')->isoFormat('MMMM YYYY'), 'tot_cli' => $usuarios->count()]
            );
            $miembros = $usuarios->pluck('usr_id')->all();
            for ($rel = 0; $inicio->copy()->addMonths($rel)->startOfMonth()->lte($hoy); $rel++) {
                $estados = array_intersect_key($this->estadosAl($inicio->copy()->addMonths($rel)->endOfMonth()), array_flip($miembros));
                foreach (self::ORDEN as $cod) {
                    MarkovCohortDistribucion::updateOrCreate(
                        ['coh_id' => $coh->id, 'per_rel' => $rel, 'est_id' => $ids[$cod]],
                        ['cnt' => count(array_filter($estados, fn ($e) => $e === $cod))]
                    );
                }
            }
        }

        return $grupos->count();
    }

    /**
     * Cohortes para el panel: proporción de activos, fidelizados y perdidos por mes relativo.
     */
    public function cohortesParaPanel(int $limite = 12): Collection
    {
        $codigos = MarkovEstadoCliente::pluck('cod', 'id');

        return MarkovCohort::orderByDesc('per_cap')->limit($limite)->get()->map(function ($c) use ($codigos) {
            $filas = [];
            foreach (MarkovCohortDistribucion::where('coh_id', $c->id)->get()->groupBy('per_rel') as $rel => $dist) {
                $tot = max(1, $c->tot_cli);
                $porCod = $dist->mapWithKeys(fn ($d) => [$codigos[$d->est_id] => $d->cnt]);
                $filas[(int) $rel] = [
                    'activos' => ($porCod['prospecto'] ?? 0) / $tot + ($porCod['reservado'] ?? 0) / $tot + ($porCod['completado'] ?? 0) / $tot,
                    'fidelizados' => ($porCod['fidelizado'] ?? 0) / $tot,
                    'perdidos' => ($porCod['perdido'] ?? 0) / $tot,
                ];
            }
            ksort($filas);

            return ['nom' => $c->nom, 'per_cap' => $c->per_cap, 'tot' => $c->tot_cli, 'filas' => $filas];
        })->values();
    }
}
