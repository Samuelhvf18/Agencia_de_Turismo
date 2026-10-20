<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Kilometraje y costo de combustible de un vehículo en una salida. */
class BitacoraViajeVehiculo extends Model
{
    protected $table = 'bitacora_viajes_vehiculo';
    const CREATED_AT = 'reg_en';
    const UPDATED_AT = null;

    protected $fillable = ['veh_id', 'sal_tur_id', 'dst_km', 'cos_cmb', 'reg_en'];

    protected function casts(): array
    {
        return ['dst_km' => 'decimal:2', 'cos_cmb' => 'decimal:2', 'reg_en' => 'datetime'];
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'veh_id');
    }

    public function salida(): BelongsTo
    {
        return $this->belongsTo(SalidaTour::class, 'sal_tur_id');
    }
}
