<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Salida programada de un tour con su guía, chofer y vehículo. */
class SalidaTour extends Model
{
    public const EST_PROGRAMADA = 'programada';
    public const EST_EN_CURSO = 'en_curso';
    public const EST_COMPLETADA = 'completada';
    public const EST_CANCELADA = 'cancelada';

    protected $table = 'salidas_tour';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';

    protected $fillable = [
        'tur_id', 'fec_sal', 'hra_sal', 'prf_gui_id', 'prf_cho_id', 'veh_id',
        'cap_max', 'est', 'hra_ini_rea', 'hra_fin_rea',
    ];

    protected function casts(): array
    {
        return [
            'fec_sal' => 'date',
            'cap_max' => 'integer',
            'hra_ini_rea' => 'datetime',
            'hra_fin_rea' => 'datetime',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }

    public function guia(): BelongsTo
    {
        return $this->belongsTo(PerfilGuia::class, 'prf_gui_id');
    }

    public function chofer(): BelongsTo
    {
        return $this->belongsTo(PerfilChofer::class, 'prf_cho_id');
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'veh_id');
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'sal_tur_id');
    }

    public function viajes(): HasMany
    {
        return $this->hasMany(BitacoraViajeVehiculo::class, 'sal_tur_id');
    }

    /** Cupos ocupados por reservas que no están canceladas. */
    public function cuposOcupados(): int
    {
        return (int) $this->reservas()->where('est', '<>', Reserva::EST_CANCELADA)->sum('cnt_ptc');
    }

    public function cuposDisponibles(): int
    {
        return max(0, $this->cap_max - $this->cuposOcupados());
    }
}
