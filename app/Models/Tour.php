<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    protected $table = 'tours';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';

    protected $fillable = [
        'cat_id', 'cod', 'rta_amg', 'nom', 'des', 'niv_dif',
        'alt_min_m', 'alt_max_m', 'dur_hrs', 'req_fis',
        'pre_bas', 'cap_max', 'trz_geo', 'act'
    ];

    protected function casts(): array
    {
        return [
            'trz_geo' => 'array',
            'act' => 'boolean',
            'eli_en' => 'datetime',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaTour::class, 'cat_id');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoTour::class, 'tur_id');
    }

    public function puntosRuta(): HasMany
    {
        return $this->hasMany(PuntoRuta::class, 'tur_id')->orderBy('ord_sec');
    }

    public function salidas(): HasMany
    {
        return $this->hasMany(SalidaTour::class, 'tur_id');
    }

    public function equipos(): BelongsToMany
    {
        return $this->belongsToMany(TipoEquipo::class, 'equipo_por_tour', 'tur_id', 'tip_eqp_id')
            ->withPivot(['id', 'pre_alq_esp', 'act']);
    }

    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class, 'tur_id');
    }

    public function proyecciones(): HasMany
    {
        return $this->hasMany(ProyeccionDemanda::class, 'tur_id');
    }

    public function parametrosCola(): HasMany
    {
        return $this->hasMany(ColaParametroTour::class, 'tur_id');
    }
}
