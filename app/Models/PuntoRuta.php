<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Punto del trazado de un tour: inicio, checkpoint, mirador o fin. */
class PuntoRuta extends Model
{
    protected $table = 'puntos_ruta';
    public $timestamps = false;
    protected $fillable = ['tur_id', 'ord_sec', 'nom', 'lat', 'lon', 'alt_m', 'tip_pun'];

    protected function casts(): array
    {
        return ['ord_sec' => 'integer', 'lat' => 'float', 'lon' => 'float', 'alt_m' => 'integer'];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }
}
