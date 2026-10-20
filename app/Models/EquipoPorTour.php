<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Equipo que se ofrece en alquiler para un tour (precio especial opcional). */
class EquipoPorTour extends Model
{
    protected $table = 'equipo_por_tour';
    public $timestamps = false;
    protected $fillable = ['tur_id', 'tip_eqp_id', 'pre_alq_esp', 'act'];

    protected function casts(): array
    {
        return ['pre_alq_esp' => 'decimal:2', 'act' => 'boolean'];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoEquipo::class, 'tip_eqp_id');
    }

    /** Precio de alquiler efectivo: el especial del tour o el base del tipo. */
    public function precio(): float
    {
        return (float) ($this->pre_alq_esp ?? $this->tipo->pre_alq_bas);
    }
}
