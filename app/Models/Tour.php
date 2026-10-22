<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tours';

    // Mapeo de nombres de columnas de timestamp personalizados según PostgreSQL
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';
    const DELETED_AT = 'eli_en';

    protected $fillable = [
        'cat_id',
        'cod',
        'rta_amg',
        'nom',
        'des',
        'niv_dif',
        'alt_min_m',
        'alt_max_m',
        'dur_hrs',
        'req_fis',
        'pre_bas',
        'cap_max',
        'trz_geo',
        'act',
    ];

    protected $casts = [
        'trz_geo' => 'array',
        'act'     => 'boolean',
        'pre_bas' => 'decimal:2',
        'dur_hrs' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (empty($model->rta_amg)) {
                $model->rta_amg = Str::slug($model->nom);
            }
        });
    }

    // ------------------------------------------------------------------------
    // RELACIONES
    // ------------------------------------------------------------------------

    // Pertenece a una Categoría
    public function categoria()
    {
        return $this->belongsTo(CategoriaTour::class, 'cat_id');
    }

    // Un Tour tiene muchas Salidas Programadas
    public function salidas()
    {
        return $this->hasMany(SalidaTour::class, 'tur_id');
    }

    // Relación HasManyThrough: Obtiene las Reservas del Tour a través de sus Salidas
    public function reservas()
    {
        return $this->hasManyThrough(
            Reserva::class,
            SalidaTour::class,
            'tur_id',     // Llave foránea en la tabla salidas_tour
            'sal_tur_id', // Llave foránea en la tabla reservas
            'id',         // Llave local en la tabla tours
            'id'          // Llave local en la tabla salidas_tour
        );
    }
}