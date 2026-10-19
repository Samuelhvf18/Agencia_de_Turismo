<?php

namespace App\Services\Io;

use App\Models\MarkovEstadoCliente;
use Illuminate\Support\Facades\Schema;

/**
 * Arma las variables de la vista admin/io/ciclo-vida: estados, matriz P, absorción (N, B, t),
 * proyección v(0)·P^n y cohortes. Lo usan el controlador y la vista previa.
 */
class PanelMarkovBuilder
{
    /** Nombres y colores por defecto (los mismos que carga IoColasMarkovSeeder). */
    public const ESTILO = [
        'prospecto' => ['Prospecto', '#6B6F72'],
        'reservado' => ['Reservado', '#F4C95D'],
        'completado' => ['Tour completado', '#1F5F8B'],
        'fidelizado' => ['Cliente fidelizado', '#C96F4A'],
        'perdido' => ['Cliente perdido', '#174870'],
    ];

    public static function armar(MarkovService $svc, array $P, ?string $fecCal, array $conteos, ?string $inicio, array $cohortes): array
    {
        try {
            $catalogo = Schema::hasTable('markov_estados_cliente') ? MarkovEstadoCliente::orderBy('ord_vis')->get()->keyBy('cod') : collect();
        } catch (\Throwable) {
            $catalogo = collect(); // vista previa sin base de datos
        }

        $estados = [];
        foreach (MarkovService::ORDEN as $cod) {
            $e = $catalogo[$cod] ?? null;
            $estados[] = [
                'cod' => $cod,
                'nom' => $e->nom ?? self::ESTILO[$cod][0],
                'col' => $e->col_hex ?? self::ESTILO[$cod][1],
                'es_abs' => in_array($cod, MarkovService::ABSORBENTES, true),
                'clientes' => (int) ($conteos[$cod] ?? 0),
            ];
        }

        $an = $svc->analizar($P);
        $absorcion = [];
        foreach ($an['transitorios'] as $cod) {
            $absorcion[] = [
                'cod' => $cod,
                'nom' => collect($estados)->firstWhere('cod', $cod)['nom'],
                'p_fid' => $an['B'][$cod]['fidelizado'] ?? 0,
                'p_per' => $an['B'][$cod]['perdido'] ?? 0,
                'meses' => $an['t'][$cod],
            ];
        }

        $inicio = in_array($inicio, $an['transitorios'], true) ? $inicio : 'prospecto';

        return [
            'estados' => $estados,
            'P' => $P,
            'fecCal' => $fecCal ? \Carbon\Carbon::parse($fecCal)->format('d/m/Y') : 'matriz inicial',
            'absorcion' => $absorcion,
            'N' => $an['N'],
            'proyeccion' => $svc->proyectar($P, $inicio, 12),
            'cohortes' => $cohortes,
            'totalClientes' => array_sum($conteos),
        ];
    }
}
