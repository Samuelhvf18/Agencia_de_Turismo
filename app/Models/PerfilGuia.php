<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerfilGuia extends Model
{
    protected $table = 'perfiles_guia';
    public $timestamps = false;
    protected $fillable = ['usr_id', 'rta_amg', 'ani_exp', 'lim_hrs_sem', 'bio', 'act'];

    protected function casts(): array
    {
        return ['ani_exp' => 'integer', 'lim_hrs_sem' => 'integer', 'act' => 'boolean'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id');
    }

    public function idiomas(): BelongsToMany
    {
        return $this->belongsToMany(Idioma::class, 'idiomas_guia', 'prf_gui_id', 'idi_id');
    }

    public function salidas(): HasMany
    {
        return $this->hasMany(SalidaTour::class, 'prf_gui_id');
    }
}
