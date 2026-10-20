<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Reseña verificada de un tour (cal 1–5), ligada a una reserva completada. */
class Resena extends Model
{
    protected $table = 'resenas';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = null;

    protected $fillable = ['tur_id', 'rsv_id', 'cal', 'com', 'mdt'];

    protected function casts(): array
    {
        return ['cal' => 'integer', 'mdt' => 'array'];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'rsv_id');
    }
}
