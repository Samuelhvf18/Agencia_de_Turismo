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

    protected $casts = [
        'cnt_ptc' => 'integer',
        'pre_tot' => 'decimal:2',
    ];

    public function salidaTour()
    {
        return $this->belongsTo(SalidaTour::class, 'sal_tur_id');
    }

    public function participantes()
    {
        return $this->hasMany(ParticipanteReserva::class, 'rsv_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usr_id');
    }

    public function comprobantePago()
    {
        return $this->hasOne(ComprobantePago::class, 'rsv_id');
    }
}
