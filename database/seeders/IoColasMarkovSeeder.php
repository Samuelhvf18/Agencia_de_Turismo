<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Semilla inicial del Módulo 07 — Investigación Operativa II.
 *
 * - Catálogo de estados Markov del ciclo de vida del cliente.
 * - Matriz de transición inicial de referencia (valores razonables; se recalibran con datos reales).
 * - Parámetros iniciales M/M/c/K para los tours ya cargados por CatalogoLandingSeeder.
 */
class IoColasMarkovSeeder extends Seeder
{
    public function run(): void
    {
        $this->sembrarEstadosMarkov();
        $this->sembrarMatrizTransicionInicial();
        $this->sembrarParametrosColasPorTour();
    }

    private function sembrarEstadosMarkov(): void
    {
        $estados = [
            ['cod' => 'prospecto',  'nom' => 'Prospecto',          'des' => 'Visitante registrado que aún no reserva.',                  'es_abs' => false, 'ord_vis' => 1, 'col_hex' => '#6B6F72'],
            ['cod' => 'reservado',  'nom' => 'Reservado',          'des' => 'Cliente con al menos una reserva confirmada pendiente.',    'es_abs' => false, 'ord_vis' => 2, 'col_hex' => '#F4C95D'],
            ['cod' => 'completado', 'nom' => 'Tour completado',    'des' => 'Cliente que al menos una vez vivió el tour.',               'es_abs' => false, 'ord_vis' => 3, 'col_hex' => '#1F5F8B'],
            ['cod' => 'fidelizado', 'nom' => 'Cliente fidelizado', 'des' => 'Cliente recurrente (≥ 2 tours). Estado absorbente.',       'es_abs' => true,  'ord_vis' => 4, 'col_hex' => '#C96F4A'],
            ['cod' => 'perdido',    'nom' => 'Cliente perdido',    'des' => 'Sin actividad en ≥ 12 meses. Estado absorbente.',          'es_abs' => true,  'ord_vis' => 5, 'col_hex' => '#174870'],
        ];

        foreach ($estados as $e) {
            DB::table('markov_estados_cliente')->updateOrInsert(['cod' => $e['cod']], $e);
        }
    }

    private function sembrarMatrizTransicionInicial(): void
    {
        // Matriz P (período mensual). Las filas deben sumar 1.
        // Valores iniciales de referencia — se recalibran mes a mes con datos reales.
        $hoy = Carbon::today();
        $ids = DB::table('markov_estados_cliente')->pluck('id', 'cod');

        $matriz = [
            // origen       → destinos: [cod_destino => probabilidad]
            'prospecto'  => ['prospecto' => 0.60, 'reservado' => 0.25, 'perdido' => 0.15],
            'reservado'  => ['reservado' => 0.10, 'completado' => 0.80, 'perdido' => 0.10],
            'completado' => ['completado' => 0.25, 'reservado' => 0.20, 'fidelizado' => 0.40, 'perdido' => 0.15],
            'fidelizado' => ['fidelizado' => 1.00],
            'perdido'    => ['perdido' => 1.00],
        ];

        foreach ($matriz as $origen => $destinos) {
            foreach ($destinos as $destino => $p) {
                DB::table('markov_transiciones')->updateOrInsert(
                    [
                        'est_ori_id' => $ids[$origen],
                        'est_des_id' => $ids[$destino],
                        'per' => 'mensual',
                        'fec_cal' => $hoy,
                    ],
                    [
                        'prb' => $p,
                        'act' => true,
                        'cre_en' => now(),
                    ]
                );
            }
        }
    }

    private function sembrarParametrosColasPorTour(): void
    {
        // Si no hay tours cargados aún, no sembramos parámetros (opcional en entornos limpios).
        $tours = DB::table('tours')->select('id', 'cap_max', 'dur_hrs')->get();
        if ($tours->isEmpty()) {
            return;
        }

        $hoy = Carbon::today();

        foreach ($tours as $tur) {
            // μ se expresa en 1/día: si un tour dura dur_hrs, μ = 24 / dur_hrs.
            $mu = $tur->dur_hrs > 0 ? round(24 / (float) $tur->dur_hrs, 4) : 1.0;

            DB::table('cola_parametros_tour')->updateOrInsert(
                ['tur_id' => $tur->id, 'act' => true],
                [
                    'lmb'     => 2.0,                    // ≈ 2 reservas/día estimadas por defecto
                    'mu'      => $mu,
                    'c_srv'   => 1,                      // 1 guía por salida por defecto
                    'k_cap'   => $tur->cap_max,
                    'cos_esp' => 15.00,                  // Bs/hora de espera estimados
                    'cos_srv' => 25.00,                  // Bs/hora de guía ocioso estimados
                    'fec_cal' => $hoy,
                    'obs'     => 'Parámetros iniciales de referencia. Recalibrar con datos reales.',
                    'cre_en'  => now(),
                    'mod_en'  => now(),
                ]
            );
        }
    }
}
