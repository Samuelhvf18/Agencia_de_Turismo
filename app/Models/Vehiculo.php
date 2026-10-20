<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Vehículo de la flota. Estados: activo, mantenimiento o inactivo. */
class Vehiculo extends Model
{
    public const EST_ACTIVO = 'activo';
    public const EST_MANTENIMIENTO = 'mantenimiento';
    public const EST_INACTIVO = 'inactivo';

    protected $table = 'vehiculos';
    public $timestamps = false;
    protected $fillable = ['pla', 'mar', 'mdl', 'tip', 'cap_asi', 'tip_cmb', 'est'];

    protected function casts(): array
    {
        return ['cap_asi' => 'integer'];
    }

    public function salidas(): HasMany
    {
        return $this->hasMany(SalidaTour::class, 'veh_id');
    }

    public function viajes(): HasMany
    {
        return $this->hasMany(BitacoraViajeVehiculo::class, 'veh_id');
    }
}
