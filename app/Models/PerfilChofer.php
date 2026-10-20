<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerfilChofer extends Model
{
    protected $table = 'perfiles_chofer';
    public $timestamps = false;
    protected $fillable = ['usr_id', 'num_lic', 'tip_lic', 'fec_ven_lic', 'act'];

    protected function casts(): array
    {
        return ['fec_ven_lic' => 'date', 'act' => 'boolean'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id');
    }

    public function salidas(): HasMany
    {
        return $this->hasMany(SalidaTour::class, 'prf_cho_id');
    }
}
