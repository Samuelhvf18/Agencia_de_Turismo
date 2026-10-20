<?php

namespace Database\Seeders;

use App\Models\EquipoPorTour;
use App\Models\ItemEquipo;
use App\Models\PerfilChofer;
use App\Models\PerfilGuia;
use App\Models\SalidaTour;
use App\Models\TipoEquipo;
use App\Models\Tour;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

/**
 * Datos operativos de ejemplo para recorrer el flujo de venta: flota,
 * equipo de alquiler con tallas, equipo por tour y salidas programadas
 * para los próximos 30 días. Es idempotente y no se ejecuta en producción.
 */
class OperacionDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction() || Tour::count() === 0) {
            return;
        }

        foreach ([
            ['pla' => '2345-LPZ', 'mar' => 'Toyota', 'mdl' => 'Hiace', 'tip' => 'van', 'cap_asi' => 12, 'tip_cmb' => 'diesel'],
            ['pla' => '3456-KTR', 'mar' => 'Mercedes-Benz', 'mdl' => 'Sprinter', 'tip' => 'minibus', 'cap_asi' => 20, 'tip_cmb' => 'diesel'],
            ['pla' => '4567-BPT', 'mar' => 'Nissan', 'mdl' => 'Urvan', 'tip' => 'van', 'cap_asi' => 14, 'tip_cmb' => 'gasolina'],
        ] as $v) {
            Vehiculo::updateOrCreate(['pla' => $v['pla']], $v + ['est' => Vehiculo::EST_ACTIVO]);
        }

        $tipos = [
            'Casco de seguridad' => ['des' => 'Casco certificado para bicicleta y escalada', 'pre' => 15, 'cri' => true, 'ord' => 120, 'mnt' => 30, 'tallas' => ['S' => 6, 'M' => 10, 'L' => 6]],
            'Bicicleta doble suspensión' => ['des' => 'Bicicleta de montaña con frenos de disco', 'pre' => 120, 'cri' => true, 'ord' => 450, 'mnt' => 380, 'tallas' => ['M' => 8, 'L' => 6]],
            'Chaqueta impermeable' => ['des' => 'Cortavientos para el frío del altiplano', 'pre' => 25, 'cri' => false, 'ord' => 90, 'mnt' => 20, 'tallas' => ['S' => 5, 'M' => 8, 'L' => 6, 'XL' => 3]],
            'Bastones de trekking' => ['des' => 'Par de bastones regulables', 'pre' => 10, 'cri' => false, 'ord' => 60, 'mnt' => 8, 'tallas' => ['Regulable' => 20]],
            'Crampones y piolet' => ['des' => 'Equipo glaciar completo', 'pre' => 80, 'cri' => true, 'ord' => 300, 'mnt' => 60, 'tallas' => ['Única' => 10]],
        ];

        $ids = [];
        foreach ($tipos as $nom => $t) {
            $tipo = TipoEquipo::updateOrCreate(['nom' => $nom], [
                'des' => $t['des'], 'pre_alq_bas' => $t['pre'], 'eqp_cri' => $t['cri'], 'cos_ord' => $t['ord'], 'cos_mnt_anu' => $t['mnt'],
            ]);
            foreach ($t['tallas'] as $tal => $cnt) {
                ItemEquipo::firstOrCreate(['tip_eqp_id' => $tipo->id, 'tal' => $tal], ['cnt_tot' => $cnt, 'cnt_dis' => $cnt, 'con' => 'bueno']);
            }
            $ids[$nom] = $tipo->id;
        }

        // Equipo que se ofrece según la categoría del tour.
        $porCategoria = [
            'biking' => ['Casco de seguridad', 'Bicicleta doble suspensión', 'Chaqueta impermeable'],
            'climbing' => ['Casco de seguridad', 'Crampones y piolet', 'Chaqueta impermeable'],
            'trekking' => ['Bastones de trekking', 'Chaqueta impermeable'],
            'hiking' => ['Bastones de trekking', 'Chaqueta impermeable'],
            'city' => ['Chaqueta impermeable'],
            'city-tour' => ['Chaqueta impermeable'],
        ];

        $guia = PerfilGuia::first();
        $chofer = PerfilChofer::first();
        $vehiculos = Vehiculo::where('est', Vehiculo::EST_ACTIVO)->orderBy('id')->get();

        foreach (Tour::with('categoria')->where('act', true)->get() as $n => $tour) {
            foreach ($porCategoria[$tour->categoria->rta_amg] ?? [] as $nom) {
                EquipoPorTour::firstOrCreate(['tur_id' => $tour->id, 'tip_eqp_id' => $ids[$nom]], ['act' => true]);
            }

            // Una salida cada 3 días durante el próximo mes, desfasada por tour.
            for ($d = 1 + ($n % 3); $d <= 30; $d += 3) {
                SalidaTour::firstOrCreate(
                    ['tur_id' => $tour->id, 'fec_sal' => today()->addDays($d)->toDateString(), 'hra_sal' => '08:00:00'],
                    [
                        'cap_max' => $tour->cap_max,
                        'est' => SalidaTour::EST_PROGRAMADA,
                        'prf_gui_id' => $guia?->id,
                        'prf_cho_id' => $chofer?->id,
                        'veh_id' => $vehiculos->isNotEmpty() ? $vehiculos[$n % $vehiculos->count()]->id : null,
                    ]
                );
            }
        }
    }
}
