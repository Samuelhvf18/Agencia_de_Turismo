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
        'pre_bas', 'cap_max', 'trz_geo', 'act', 'eli_en',
    ];

    protected function casts(): array
    {
        return [
            'trz_geo' => 'array',
            'act' => 'boolean',
            'eli_en' => 'datetime',
        ];
    }

    /** Altitud (m s. n. m.) desde la cual un tour se considera de alto riesgo. */
    public const ALTITUD_ALTO_RIESGO = 4500;

    /**
     * Tours de alto riesgo: dificultad difícil/experto o altitud máxima ≥ 4500 m.
     * En ellos la ficha médica firmada es obligatoria y sin ella no se emite el boleto.
     */
    public function esAltoRiesgo(): bool
    {
        return in_array($this->niv_dif, ['dificil', 'experto'], true)
            || (int) $this->alt_max_m >= self::ALTITUD_ALTO_RIESGO;
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
