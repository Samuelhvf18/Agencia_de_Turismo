<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipanteReserva extends Model
{
    use HasFactory;

    protected $table = 'participantes_reserva';
    public $timestamps = false;

    protected $fillable = [
        'rsv_id',
        'nom_cpl',
        'tip_doc',
        'num_doc',
        'ci_cmp',
        'ci_dep_id',
        'eda',
        'pai_id',
        'es_ctc_pri',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'rsv_id');
    }

    public function fichasMedicas()
    {
        return $this->hasOne(FichaMedica::class, 'ptc_rsv_id');
    }
}