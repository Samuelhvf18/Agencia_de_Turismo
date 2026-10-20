<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlquilerEquipoReserva extends Model
{
    protected $table = 'alquileres_equipo_reserva';
    public $timestamps = false;
    protected $fillable = ['ptc_rsv_id', 'itm_eqp_id', 'pre'];

    protected function casts(): array
    {
        return ['pre' => 'decimal:2'];
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(ParticipanteReserva::class, 'ptc_rsv_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemEquipo::class, 'itm_eqp_id');
    }
}
