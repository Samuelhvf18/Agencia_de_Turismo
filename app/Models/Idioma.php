<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Idioma extends Model
{
    protected $table = 'idiomas';
    public $timestamps = false;
    protected $fillable = ['nom', 'cod_iso'];

    public function guias(): BelongsToMany
    {
        return $this->belongsToMany(PerfilGuia::class, 'idiomas_guia', 'idi_id', 'prf_gui_id');
    }
}
