<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalidaTour extends Model
{
    protected $table = 'salidas_tour';

    protected $fillable = [
        'tur_id',
        'veh_id',
        'fec_sal',
        'hra_sal',
        'cap_max',
        'est'
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'veh_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'sal_tur_id');
    }
}