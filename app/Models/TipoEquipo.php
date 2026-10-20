<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEquipo extends Model
{
    use HasFactory;

    protected $table = 'tipos_equipo';
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'des',
        'pre_alq_bas',
        'cos_ord',
        'cos_mnt_anu',
        'eqp_cri',
    ];

    public function items()
    {
        return $this->hasMany(ItemEquipo::class, 'tip_eqp_id');
    }
}