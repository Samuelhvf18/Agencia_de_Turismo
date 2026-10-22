<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemEquipo extends Model
{
    protected $table = 'items_equipo';
    public $timestamps = false;

    protected $fillable = [
        'tip_eqp_id',
        'tal',
        'cnt_tot',
        'cnt_dis',
        'con',
    ];

    // Relación con el tipo de equipo
    public function tipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'tip_eqp_id');
    }
}