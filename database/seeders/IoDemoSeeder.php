<?php

namespace Database\Seeders;

use App\Models\AlquilerEquipoReserva;
use App\Models\BitacoraViajeVehiculo;
use App\Models\ColaParametroTour;
use App\Models\Idioma;
use App\Models\MarkovClienteEstado;
use App\Models\MarkovEstadoCliente;
use App\Models\Pais;
use App\Models\ParticipanteReserva;
use App\Models\PerfilGuia;
use App\Models\PropuestaAsignacion;
use App\Models\Reserva;
use App\Models\Role;
use App\Models\SalidaTour;
use App\Models\TipoEquipo;
use App\Models\Tour;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\Io\ColasService;
use App\Services\Io\MarkovService;
use App\Services\Io\OptimizadorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Inyección de datos de demostración para el Módulo 07 — Investigación Operativa II.
 *
 * Deja datos persistidos para los cuatro paneles de /admin/io:
 *  - colas: eventos de llegada de los últimos 60 días y snapshots diarios de M/M/c/K;
 *  - propuestas: salidas futuras sin asignar y lotes pendientes de guías y vehículos;
 *  - EOQ: salidas, reservas, participantes y alquileres de los últimos meses del año;
 *  - Markov: turistas demo con historial de estados, matriz recalibrada y cohortes.
 *
 * Es idempotente y no se ejecuta en producción. Solo borra registros sintéticos
 * identificables (eventos sin reserva/salida, historial de los turistas demo y
 * propuestas pendientes de los lotes demo); no toca reservas, salidas ni
 * parámetros reales.
 *
 * Uso: php artisan db:seed --class=IoDemoSeeder
 */
class IoDemoSeeder extends Seeder
{
    /** Lotes fijos para reconocer y reemplazar las propuestas demo en reejecuciones. */
    private const LOTE_GUIAS = '00000000-0000-4000-8000-0000000000a1';

    private const LOTE_VEHICULOS = '00000000-0000-4000-8000-0000000000a2';

    /** Días de eventos de llegada: 60 días atrás + hoy. */
    private const DIAS_LLEGADAS = 61;

    /** Días de snapshots diarios de colas que se recalculan. */
    private const DIAS_METRICAS = 30;

    /** Meses completos de alquileres para que EOQ tenga historial suficiente. */
    private const MESES_EQUIPO = 6;

    /** Horizonte de las salidas futuras y propuestas. */
    private const DIAS_PROPUESTAS = 14;

    /**
     * Demanda simulada como múltiplo de λ. Varía por tour para que el panel
     * muestre tours estables, en atención y saturados, no todos iguales.
     */
    private const FACTOR_DEMANDA = [1 => 2.6, 2 => 2.2, 3 => 1.1, 4 => 1.8, 5 => 1.4, 6 => 3.0];

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('IoDemoSeeder no se ejecuta en producción.');

            return;
        }

        if (Tour::count() === 0) {
            $this->command?->warn('IoDemoSeeder: primero cargue el catálogo de tours.');

            return;
        }

        $this->asegurarCatalogosIo();
        $this->crearGuiasDemo();
        $this->sembrarLlegadasYColas();
        $this->sembrarHistorialEquipo();
        $this->sembrarSalidasFuturasYPropuestas();
        $this->sembrarCicloVidaClientes();

        $this->command?->info('Datos demo de IO cargados: colas, propuestas, EOQ y ciclo de vida.');
    }

    /** Catálogo Markov y parámetros M/M/c/K: crea solo lo que falte. */
    private function asegurarCatalogosIo(): void
    {
        if (MarkovEstadoCliente::count() === 0) {
            $this->call(IoColasMarkovSeeder::class);
        }

        foreach (Tour::where('act', true)->get() as $tour) {
            if (ColaParametroTour::where('tur_id', $tour->id)->where('act', true)->exists()) {
                continue;
            }

            $mu = (float) $tour->dur_hrs > 0 ? round(24 / (float) $tour->dur_hrs, 4) : 1.0;
            ColaParametroTour::create([
                'tur_id' => $tour->id,
                'lmb' => 2.0,
                'mu' => $mu,
                'c_srv' => 1,
                'k_cap' => max(1, (int) $tour->cap_max),
                'cos_esp' => 15.00,
                'cos_srv' => 25.00,
                'fec_cal' => today(),
                'obs' => 'Parámetros de referencia cargados por IoDemoSeeder.',
                'act' => true,
            ]);
        }
    }

    /** Guías adicionales para que el método húngaro tenga alternativas reales. */
    private function crearGuiasDemo(): void
    {
        $rolGuia = Role::where('nom', User::ROL_GUIA)->value('id');
        if (! $rolGuia) {
            return;
        }

        $idiomas = Idioma::whereIn('cod_iso', ['es', 'en', 'fr', 'de', 'pt'])->pluck('id', 'cod_iso');
        $definiciones = [
            ['cor' => 'guia.io.andina@pipocas.test', 'nom' => 'Guía IO Andina', 'slug' => 'guia-io-andina', 'exp' => 11, 'idiomas' => ['es', 'en', 'de']],
            ['cor' => 'guia.io.altiplano@pipocas.test', 'nom' => 'Guía IO Altiplano', 'slug' => 'guia-io-altiplano', 'exp' => 7, 'idiomas' => ['es', 'en', 'fr']],
            ['cor' => 'guia.io.salar@pipocas.test', 'nom' => 'Guía IO Salar', 'slug' => 'guia-io-salar', 'exp' => 3, 'idiomas' => ['es', 'pt']],
        ];

        foreach ($definiciones as $definicion) {
            $usuario = User::updateOrCreate(['cor' => $definicion['cor']], [
                'rol_id' => $rolGuia,
                'nom' => $definicion['nom'],
                'clv' => 'Pipocas2026',
                'ci_cmp' => '',
                'act' => true,
            ]);
            if (app()->environment(['local', 'testing'])) {
                $usuario->forceFill(['cor_vrf_en' => now()])->save();
            }

            $perfil = PerfilGuia::updateOrCreate(['usr_id' => $usuario->id], [
                'rta_amg' => $definicion['slug'],
                'ani_exp' => $definicion['exp'],
                'lim_hrs_sem' => 40,
                'act' => true,
                'bio' => 'Perfil de demostración para el módulo de Investigación Operativa.',
            ]);

            $ids = collect($definicion['idiomas'])->map(fn ($cod) => $idiomas[$cod] ?? null)->filter()->values()->all();
            if ($ids) {
                $perfil->idiomas()->syncWithoutDetaching($ids);
            }
        }
    }

    /**
     * Eventos de llegada sintéticos + snapshots de colas de los últimos 30 días.
     * Los eventos demo se reconocen porque no tienen salida ni reserva asociada.
     */
    private function sembrarLlegadasYColas(): void
    {
        $desde = today()->subDays(self::DIAS_LLEGADAS - 1)->startOfDay();

        DB::table('cola_eventos_llegada')
            ->whereNull('sal_tur_id')->whereNull('rsv_id')
            ->where('fec_evt', '>=', $desde)
            ->delete();

        $parametros = ColaParametroTour::with('tour')->where('act', true)->get()
            ->filter(fn ($p) => $p->tour !== null);

        $ahora = now();
        $filas = [];

        foreach ($parametros as $parametro) {
            $factor = self::FACTOR_DEMANDA[$parametro->tur_id] ?? 1.6;
            $media = max(0.5, (float) $parametro->lmb * $factor);
            mt_srand(20260101 + (int) $parametro->tur_id);

            for ($i = 0; $i < self::DIAS_LLEGADAS; $i++) {
                $dia = $desde->copy()->addDays($i);
                $llegadas = $this->poisson($media);

                for ($k = 0; $k < $llegadas; $k++) {
                    $hora = $dia->isSameDay(today()) ? mt_rand(0, max(0, $ahora->hour)) : mt_rand(6, 20);
                    $fecha = $dia->copy()->setTime($hora, mt_rand(0, 59));
                    if ($fecha->gt($ahora)) {
                        $fecha = $ahora->copy()->subMinutes(mt_rand(2, 120));
                    }

                    $sorteo = mt_rand(1, 100);
                    $rechazo = $this->porcentajeRechazo($factor);
                    $estado = $sorteo <= 4 ? 'cancelado' : ($sorteo <= 4 + $rechazo ? 'rechazado' : 'admitido');

                    $filas[] = [
                        'tur_id' => $parametro->tur_id,
                        'sal_tur_id' => null,
                        'rsv_id' => null,
                        'fec_evt' => $fecha->format('Y-m-d H:i:s'),
                        'cnt_sol' => mt_rand(1, 4),
                        'est' => $estado,
                        'cre_en' => $fecha->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        mt_srand();

        foreach (array_chunk($filas, 500) as $chunk) {
            DB::table('cola_eventos_llegada')->insert($chunk);
        }

        $colas = app(ColasService::class);
        for ($d = self::DIAS_METRICAS - 1; $d >= 0; $d--) {
            $colas->generarMetricasDiarias(today()->subDays($d));
        }
    }

    /**
     * Historial de alquileres para EOQ: salidas completadas de los últimos meses
     * del año, con reservas, participantes y equipo alquilado. También registra
     * la bitácora de los vehículos para el costo por km de las propuestas.
     */
    private function sembrarHistorialEquipo(): void
    {
        $tipos = TipoEquipo::with('items')->get();
        if ($tipos->isEmpty()) {
            return;
        }

        $vehiculos = Vehiculo::where('est', Vehiculo::EST_ACTIVO)->orderBy('id')->get();
        $paises = Pais::pluck('id', 'cod_iso');
        $codigosPais = ['BO', 'US', 'FR', 'DE', 'BR'];
        $inicio = today()->startOfMonth()->subMonthsNoOverflow(self::MESES_EQUIPO);
        $fin = today()->startOfMonth()->subMonthNoOverflow();

        for ($mes = $inicio->copy(); $mes->lte($fin); $mes->addMonthNoOverflow()) {
            $dia = $mes->copy()->day(min(10 + ($mes->month % 6), $mes->daysInMonth));

            foreach (Tour::where('act', true)->orderBy('id')->get() as $tour) {
                $codigo = 'RES-IOE'.$mes->format('ym').'-'.$tour->id;
                $reserva = Reserva::where('cod', $codigo)->first();

                if (! $reserva) {
                    $fecha = $dia->toDateString();
                    $ocupada = SalidaTour::where('tur_id', $tour->id)
                        ->whereDate('fec_sal', $fecha)
                        ->where('hra_sal', '15:00:00')
                        ->exists();
                    if ($ocupada) {
                        continue;
                    }

                    $vehiculo = $vehiculos->first(fn ($v) => $v->cap_asi >= $tour->cap_max);
                    $inicioReal = $dia->copy()->setTime(15, 0);
                    $finReal = $inicioReal->copy()->addMinutes((int) round((float) $tour->dur_hrs * 60));

                    $salida = SalidaTour::create([
                        'tur_id' => $tour->id,
                        'fec_sal' => $fecha,
                        'hra_sal' => '15:00:00',
                        'prf_gui_id' => null,
                        'prf_cho_id' => null,
                        'veh_id' => $vehiculo?->id,
                        'cap_max' => $tour->cap_max,
                        'est' => SalidaTour::EST_COMPLETADA,
                        'hra_ini_rea' => $inicioReal,
                        'hra_fin_rea' => $finReal,
                    ]);

                    $cantidad = 3 + (($mes->month + $tour->id) % 3);
                    $reserva = Reserva::create([
                        'cod' => $codigo,
                        'sal_tur_id' => $salida->id,
                        'usr_id' => null,
                        'nom_ctc' => 'Cliente EOQ Demo',
                        'cor_ctc' => 'eoq.demo@pipocas.test',
                        'tel_ctc' => '70000000',
                        'cnt_ptc' => $cantidad,
                        'est' => Reserva::EST_COMPLETADA,
                        'pre_tot' => round((float) ($tour->pre_bas ?? 100) * $cantidad, 2),
                        'est_pag' => Reserva::PAG_PAGADO,
                        'idioma' => 'es',
                    ]);
                } else {
                    $salida = $reserva->salida;
                }

                if (! $salida) {
                    continue;
                }

                for ($p = 1; $p <= (int) $reserva->cnt_ptc; $p++) {
                    $documento = 'IOE'.$mes->format('ym').$tour->id.$p;
                    $participante = ParticipanteReserva::firstOrCreate(
                        ['rsv_id' => $reserva->id, 'num_doc' => $documento],
                        [
                            'nom_cpl' => 'Pasajero EOQ '.$p,
                            'tip_doc' => 'PASAPORTE',
                            'eda' => 25 + (($p * 7) % 30),
                            'pai_id' => $paises[$codigosPais[($p + $tour->id) % count($codigosPais)]] ?? null,
                            'es_ctc_pri' => $p === 1,
                        ]
                    );

                    $tipo = $tipos[($p + $tour->id + $mes->month) % $tipos->count()];
                    $item = $tipo->items->first();
                    if ($item) {
                        AlquilerEquipoReserva::firstOrCreate(
                            ['ptc_rsv_id' => $participante->id, 'itm_eqp_id' => $item->id],
                            ['pre' => $tipo->pre_alq_bas]
                        );
                    }
                }

                if ($salida->veh_id) {
                    $vehiculo = $vehiculos->firstWhere('id', $salida->veh_id);
                    $factor = ['diesel' => 0.90, 'gasolina' => 1.10, 'gnv' => 0.60, 'electrico' => 0.30][$vehiculo?->tip_cmb ?? 'diesel'] ?? 1.0;
                    $km = round((float) $tour->dur_hrs * 25 * (0.85 + (($mes->month + $tour->id) % 5) * 0.06), 2);

                    BitacoraViajeVehiculo::firstOrCreate(
                        ['sal_tur_id' => $salida->id],
                        ['veh_id' => $salida->veh_id, 'dst_km' => $km, 'cos_cmb' => round($km * $factor, 2)]
                    );
                }
            }
        }
    }

    /** Salidas futuras sin asignar y lotes pendientes para revisión humana. */
    private function sembrarSalidasFuturasYPropuestas(): void
    {
        $hoy = today();

        foreach (Tour::where('act', true)->orderBy('id')->get()->values() as $indice => $tour) {
            for ($d = 1 + ($indice % 3); $d <= self::DIAS_PROPUESTAS; $d += 3) {
                SalidaTour::firstOrCreate(
                    [
                        'tur_id' => $tour->id,
                        'fec_sal' => $hoy->copy()->addDays($d)->toDateString(),
                        'hra_sal' => '10:30:00',
                    ],
                    ['cap_max' => $tour->cap_max, 'est' => SalidaTour::EST_PROGRAMADA]
                );
            }
        }

        DB::transaction(function () use ($hoy) {
            PropuestaAsignacion::whereIn('lote', [self::LOTE_GUIAS, self::LOTE_VEHICULOS])
                ->where('est', PropuestaAsignacion::EST_PENDIENTE)
                ->delete();

            $optimizador = app(OptimizadorService::class);

            $guias = $optimizador->proponerGuias($hoy, $hoy->copy()->addDays(self::DIAS_PROPUESTAS), true);
            if ($guias['propuestas'] > 0) {
                PropuestaAsignacion::where('lote', $guias['lote'])->update(['lote' => self::LOTE_GUIAS]);
            }

            $vehiculos = $optimizador->proponerVehiculos($hoy, $hoy->copy()->addDays(self::DIAS_PROPUESTAS), true);
            if ($vehiculos['propuestas'] > 0) {
                PropuestaAsignacion::where('lote', $vehiculos['lote'])->update(['lote' => self::LOTE_VEHICULOS]);
            }
        });
    }

    /**
     * Turistas demo con historial mensual de Markov. El historial es relativo a
     * hoy para que la recalibración de 6 meses y las cohortes siempre tengan datos.
     */
    private function sembrarCicloVidaClientes(): void
    {
        $rolTurista = Role::where('nom', User::ROL_TURISTA)->value('id');
        $estados = MarkovEstadoCliente::pluck('id', 'cod');
        if (! $rolTurista || $estados->isEmpty()) {
            return;
        }

        $rutas = [
            'fiel' => ['prospecto', 'reservado', 'completado', 'fidelizado'],
            'perdido' => ['prospecto', 'reservado', 'completado', 'perdido'],
            'largo' => ['prospecto', 'reservado', 'completado', 'reservado', 'completado', 'fidelizado'],
            'activo' => ['prospecto', 'reservado', 'completado'],
            'reservado' => ['prospecto', 'reservado'],
            'prospecto' => ['prospecto'],
        ];

        $personas = [
            ['offset' => 9, 'ruta' => 'fiel', 'nom' => 'Turista IO Ana', 'pais' => 'BO'],
            ['offset' => 9, 'ruta' => 'perdido', 'nom' => 'Turista IO Bruno', 'pais' => 'US'],
            ['offset' => 8, 'ruta' => 'fiel', 'nom' => 'Turista IO Carla', 'pais' => 'FR'],
            ['offset' => 8, 'ruta' => 'perdido', 'nom' => 'Turista IO Diego', 'pais' => 'DE'],
            ['offset' => 8, 'ruta' => 'largo', 'nom' => 'Turista IO Elena', 'pais' => 'BR'],
            ['offset' => 7, 'ruta' => 'largo', 'nom' => 'Turista IO Fabio', 'pais' => 'IT'],
            ['offset' => 7, 'ruta' => 'activo', 'nom' => 'Turista IO Gabriela', 'pais' => 'BO'],
            ['offset' => 6, 'ruta' => 'activo', 'nom' => 'Turista IO Hugo', 'pais' => 'AR'],
            ['offset' => 6, 'ruta' => 'activo', 'nom' => 'Turista IO Irene', 'pais' => 'CL'],
            ['offset' => 5, 'ruta' => 'activo', 'nom' => 'Turista IO Javier', 'pais' => 'PE'],
            ['offset' => 5, 'ruta' => 'activo', 'nom' => 'Turista IO Karen', 'pais' => 'MX'],
            ['offset' => 4, 'ruta' => 'activo', 'nom' => 'Turista IO Luis', 'pais' => 'CO'],
            ['offset' => 4, 'ruta' => 'reservado', 'nom' => 'Turista IO Marta', 'pais' => 'ES'],
            ['offset' => 3, 'ruta' => 'reservado', 'nom' => 'Turista IO Nico', 'pais' => 'US'],
            ['offset' => 3, 'ruta' => 'reservado', 'nom' => 'Turista IO Olga', 'pais' => 'DE'],
            ['offset' => 2, 'ruta' => 'prospecto', 'nom' => 'Turista IO Pablo', 'pais' => 'BO'],
            ['offset' => 2, 'ruta' => 'prospecto', 'nom' => 'Turista IO Rosa', 'pais' => 'FR'],
            ['offset' => 1, 'ruta' => 'prospecto', 'nom' => 'Turista IO Sofía', 'pais' => 'BR'],
            ['offset' => 1, 'ruta' => 'prospecto', 'nom' => 'Turista IO Tomás', 'pais' => 'US'],
            ['offset' => 1, 'ruta' => 'prospecto', 'nom' => 'Turista IO Úrsula', 'pais' => 'AR'],
        ];

        $paises = Pais::pluck('id', 'cod_iso');
        $inicioMes = today()->startOfMonth();

        foreach ($personas as $indice => $persona) {
            $cadena = $rutas[$persona['ruta']];
            $alta = $inicioMes->copy()->subMonthsNoOverflow($persona['offset'])->day(2)->setTime(9, 0);

            $usuario = User::updateOrCreate(
                ['cor' => 'turista.io'.str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT).'@pipocas.test'],
                [
                    'rol_id' => $rolTurista,
                    'nom' => $persona['nom'],
                    'clv' => 'Pipocas2026',
                    'ci_cmp' => '',
                    'num_doc' => 'IODEMO'.str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT),
                    'pai_id' => $paises[$persona['pais']] ?? null,
                    'act' => true,
                ]
            );
            $usuario->forceFill(['created_at' => $alta, 'cor_vrf_en' => $alta->copy()->addHour()])->save();

            MarkovClienteEstado::where('usr_id', $usuario->id)->delete();

            $anterior = null;
            foreach ($cadena as $paso => $codigo) {
                $fecha = $inicioMes->copy()
                    ->subMonthsNoOverflow(max(1, $persona['offset'] - $paso))
                    ->day(5);

                $registro = MarkovClienteEstado::create([
                    'usr_id' => $usuario->id,
                    'est_id' => $estados[$codigo],
                    'est_ant_id' => $anterior,
                    'fec_cmb' => $fecha->toDateString(),
                    'obs' => 'Historial demo de Investigación Operativa.',
                ]);
                $anterior = $registro->est_id;
            }
        }

        $markov = app(MarkovService::class);
        $markov->recalibrar(6);
        $markov->construirCohortes();
    }

    /** Muestra de Poisson por transformada inversa (Knuth), suficiente para λ pequeñas. */
    private function poisson(float $media): int
    {
        $limite = exp(-$media);
        $k = 0;
        $p = 1.0;

        do {
            $k++;
            $p *= mt_rand() / mt_getrandmax();
        } while ($p > $limite && $k < 200);

        return $k - 1;
    }

    private function porcentajeRechazo(float $factor): int
    {
        return $factor >= 2.5 ? 14 : ($factor >= 1.8 ? 8 : 3);
    }
}
