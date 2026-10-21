<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    use HasFactory;

    protected $table = 'boletos';
    public $timestamps = false;

    protected $fillable = [
        'cod',
        'ptc_rsv_id',
        'est',
        'emt_en',
        'vld_en',
        'vld_por',
    ];

    protected $casts = [
        'emt_en' => 'datetime',
        'vld_en' => 'datetime',
    ];

    public function participante()
    {
        return $this->belongsTo(ParticipanteReserva::class, 'ptc_rsv_id');
    }

    public function validadoPor()
    {
        return $this->belongsTo(User::class, 'vld_por');
    }
}
