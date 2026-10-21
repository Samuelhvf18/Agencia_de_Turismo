<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaTour extends Model
{
    use HasFactory;

    protected $table = 'salidas_tour';

    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';

    protected $fillable = [
        'tur_id',
        'veh_id',
        'prf_gui_id',
        'prf_cho_id',
        'fec_sal',
        'hra_sal',
        'cap_max',
        'est',
        'hra_ini_rea',
        'hra_fin_rea',
    ];

    protected $casts = [
        'fec_sal' => 'date',
        'cap_max' => 'integer',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'veh_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'sal_tur_id');
    }

    /**
     * Calcula los cupos disponibles en tiempo real.
     */
    public function getCuposDisponiblesAttribute(): int
    {
        $ocupados = (int) $this->reservas()
            ->where('est', '!=', 'cancelada')
            ->sum('cnt_ptc');

        $capacidad = (int) ($this->cap_max ?? ($this->tour?->cap_max ?? 15));
        return max(0, $capacidad - $ocupados);
    }
}
