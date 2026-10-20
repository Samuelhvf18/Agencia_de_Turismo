<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Reservas proyectadas por tour y mes, con la versión del modelo que las generó. */
class ProyeccionDemanda extends Model
{
    protected $table = 'proyecciones_demanda';
    const CREATED_AT = 'gen_en';
    const UPDATED_AT = null;

    protected $fillable = ['tur_id', 'mes_per', 'ani_per', 'rsv_pry', 'ver_mdl'];

    protected function casts(): array
    {
        return ['mes_per' => 'integer', 'ani_per' => 'integer', 'rsv_pry' => 'integer'];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }
}
