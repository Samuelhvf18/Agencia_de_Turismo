<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Tipo de equipo de alquiler. cos_ord y cos_mnt_anu alimentan el modelo EOQ. */
class TipoEquipo extends Model
{
    protected $table = 'tipos_equipo';
    public $timestamps = false;
    protected $fillable = ['nom', 'des', 'pre_alq_bas', 'cos_ord', 'cos_mnt_anu', 'eqp_cri'];

    protected function casts(): array
    {
        return [
            'pre_alq_bas' => 'decimal:2',
            'cos_ord' => 'decimal:2',
            'cos_mnt_anu' => 'decimal:2',
            'eqp_cri' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemEquipo::class, 'tip_eqp_id');
    }

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'equipo_por_tour', 'tip_eqp_id', 'tur_id')
            ->withPivot(['id', 'pre_alq_esp', 'act']);
    }
}
