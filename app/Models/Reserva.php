<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';

    protected $fillable = [
        'cod',
        'sal_tur_id',
        'usr_id',
        'nom_ctc',
        'cor_ctc',
        'tel_ctc',
        'cnt_ptc',
        'est',
        'pre_tot',
        'est_pag',
    ];

    public function salidaTour()
    {
        return $this->belongsTo(SalidaTour::class, 'sal_tur_id');
    }

    public function participantes()
    {
        return $this->hasMany(ParticipanteReserva::class, 'rsv_id');
    }
}