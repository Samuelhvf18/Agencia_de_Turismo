<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Existencias de un tipo de equipo por talla (cnt_tot / cnt_dis). */
class ItemEquipo extends Model
{
    protected $table = 'items_equipo';
    public $timestamps = false;
    protected $fillable = ['tip_eqp_id', 'tal', 'cnt_tot', 'cnt_dis', 'con'];

    protected function casts(): array
    {
        return ['cnt_tot' => 'integer', 'cnt_dis' => 'integer'];
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoEquipo::class, 'tip_eqp_id');
    }

    public function alquileres(): HasMany
    {
        return $this->hasMany(AlquilerEquipoReserva::class, 'itm_eqp_id');
    }
}
