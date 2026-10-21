<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\Tour;
use App\Models\SalidaTour;

class SalidasTestSeeder extends Seeder
{
    public function run(): void
    {
        $vehiculo = Vehiculo::firstOrCreate(
            ['pla' => '4582-KLP'],
            [
                'mar'     => 'Toyota',
                'mdl'     => 'Coaster',
                'tip'     => 'Minibús',
                'cap_asi' => 15,
                'tip_cmb' => 'Gasolina',
                'est'     => 'act',
            ]
        );

        $tourMuerte = Tour::where('rta_amg', 'descenso-camino-muerte')->first();
        if ($tourMuerte) {
            SalidaTour::firstOrCreate(
                [
                    'tur_id'  => $tourMuerte->id,
                    'fec_sal' => now()->addDays(1)->toDateString(),
                ],
                [
                    'veh_id'  => $vehiculo->id,
                    'hra_sal' => '07:30:00',
                    'cap_max' => 12,
                    'est'     => 'programada',
                ]
            );
        }

        $tourHuayna = Tour::where('rta_amg', 'glaciar-huayna-potosi')->first();
        if ($tourHuayna) {
            SalidaTour::firstOrCreate(
                [
                    'tur_id'  => $tourHuayna->id,
                    'fec_sal' => now()->addDays(3)->toDateString(),
                ],
                [
                    'veh_id'  => $vehiculo->id,
                    'hra_sal' => '06:00:00',
                    'cap_max' => 6,
                    'est'     => 'programada',
                ]
            );
        }
    }
}
