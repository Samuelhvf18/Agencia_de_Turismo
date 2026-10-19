<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Elemento P[i,j] de la matriz de transición. */
class MarkovTransicion extends Model
{
    protected $table = 'markov_transiciones';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = null;

    protected $fillable = ['est_ori_id', 'est_des_id', 'prb', 'per', 'fec_cal', 'act'];

    protected function casts(): array
    {
        return [
            'prb' => 'float',
            'fec_cal' => 'date',
            'act' => 'boolean',
        ];
    }

    public function origen(): BelongsTo
    {
        return $this->belongsTo(MarkovEstadoCliente::class, 'est_ori_id');
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(MarkovEstadoCliente::class, 'est_des_id');
    }
}
