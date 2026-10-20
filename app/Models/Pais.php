<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pais extends Model
{
    protected $table = 'paises';
    public $timestamps = false;
    protected $fillable = ['nom', 'cod_iso'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'pai_id');
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(ParticipanteReserva::class, 'pai_id');
    }
}
