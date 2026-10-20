<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaMedica extends Model
{
    use HasFactory;

    protected $table = 'fichas_medicas';
    public $timestamps = false;

    protected $fillable = [
        'ptc_rsv_id',
        'con_med',
        'nom_ctc_eme',
        'tel_ctc_eme',
        'rta_fir',
        'fir_en',
        'dir_ip',
    ];

    public function participante()
    {
        return $this->belongsTo(ParticipanteReserva::class, 'ptc_rsv_id');
    }
}