<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobantePago extends Model
{
    use HasFactory;

    protected $table = 'comprobantes_pago';

    public $timestamps = false;

    protected $fillable = [
        'cod',
        'rsv_id',
        'fec_emi',
        'mto_tot',
        'est',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'rsv_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'cmp_pag_id');
    }
}