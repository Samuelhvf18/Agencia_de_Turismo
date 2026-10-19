<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Parámetros del modelo de colas M/M/c/K de un tour (Módulo 07). */
class ColaParametroTour extends Model
{
    protected $table = 'cola_parametros_tour';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';

    protected $fillable = ['tur_id', 'lmb', 'mu', 'c_srv', 'k_cap', 'cos_esp', 'cos_srv', 'fec_cal', 'obs', 'act'];

    protected function casts(): array
    {
        return [
            'lmb' => 'float',
            'mu' => 'float',
            'c_srv' => 'integer',
            'k_cap' => 'integer',
            'cos_esp' => 'float',
            'cos_srv' => 'float',
            'fec_cal' => 'date',
            'act' => 'boolean',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }
}
