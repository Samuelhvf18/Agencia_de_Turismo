<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilChofer extends Model
{
    use HasFactory;

    protected $table = 'perfiles_chofer';

    public $timestamps = false;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'usr_id');
    }

    public function salidas()
    {
        return $this->hasMany(SalidaTour::class, 'prf_cho_id');
    }
}