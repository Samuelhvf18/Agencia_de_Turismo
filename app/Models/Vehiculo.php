<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';
    public $timestamps = false;

    protected $fillable = [
        'pla',
        'mar',
        'mdl',
        'tip',
        'cap_asi',
        'tip_cmb',
        'est',
    ];

    public function salidas()
    {
        return $this->hasMany(SalidaTour::class, 'veh_id');
    }
}