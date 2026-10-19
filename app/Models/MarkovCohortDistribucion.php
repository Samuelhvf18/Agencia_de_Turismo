<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Clientes de una cohorte por estado y mes relativo. */
class MarkovCohortDistribucion extends Model
{
    protected $table = 'markov_cohort_distribucion';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = null;

    protected $fillable = ['coh_id', 'per_rel', 'est_id', 'cnt'];

    protected function casts(): array
    {
        return [
            'per_rel' => 'integer',
            'cnt' => 'integer',
        ];
    }

    public function cohorte(): BelongsTo
    {
        return $this->belongsTo(MarkovCohort::class, 'coh_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(MarkovEstadoCliente::class, 'est_id');
    }
}
