<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Cohorte de clientes captados en un mismo mes. */
class MarkovCohort extends Model
{
    protected $table = 'markov_cohorts';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';

    protected $fillable = ['nom', 'per_cap', 'tot_cli', 'obs'];

    protected function casts(): array
    {
        return [
            'per_cap' => 'date',
            'tot_cli' => 'integer',
        ];
    }

    public function distribuciones(): HasMany
    {
        return $this->hasMany(MarkovCohortDistribucion::class, 'coh_id');
    }
}
