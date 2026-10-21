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

    protected $casts = [
        'es_ctc_pri' => 'boolean',
        'eda' => 'integer',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'rsv_id');
    }

    public function departamento()
    {
        return $this->belongsTo(DepartamentoBolivia::class, 'ci_dep_id');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pai_id');
    }

    public function fichasMedicas()
    {
        return $this->hasOne(FichaMedica::class, 'ptc_rsv_id');
    }

    public function fichaMedica()
    {
        return $this->hasOne(FichaMedica::class, 'ptc_rsv_id');
    }

    public function boleto()
    {
        return $this->hasOne(Boleto::class, 'ptc_rsv_id');
    }
}
