<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Métricas diarias del modelo M/M/c/K por tour. */
class ColaMetricaDiaria extends Model
{
    protected $table = 'cola_metricas_diarias';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = null;

    protected $fillable = ['tur_id', 'fec', 'lmb_obs', 'mu_obs', 'rho', 'p0', 'lq', 'l_sis', 'wq_hrs', 'w_hrs', 'pq', 'p_rec'];

    protected function casts(): array
    {
        return [
            'fec' => 'date',
            'lmb_obs' => 'float',
            'mu_obs' => 'float',
            'rho' => 'float',
            'p0' => 'float',
            'lq' => 'float',
            'l_sis' => 'float',
            'wq_hrs' => 'float',
            'w_hrs' => 'float',
            'pq' => 'float',
            'p_rec' => 'float',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }
}
