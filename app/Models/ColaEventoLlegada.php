<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Llegada (solicitud de reserva) al sistema de colas: admitido, rechazado o cancelado. */
class ColaEventoLlegada extends Model
{
    protected $table = 'cola_eventos_llegada';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = null;

    protected $fillable = ['tur_id', 'sal_tur_id', 'rsv_id', 'fec_evt', 'cnt_sol', 'est'];

    protected function casts(): array
    {
        return [
            'fec_evt' => 'datetime',
            'cnt_sol' => 'integer',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }
}
