<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    protected $table = 'tours';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';
    
    protected $fillable = [
        'cat_id', 'cod', 'rta_amg', 'nom', 'des', 'niv_dif', 
        'alt_min_m', 'alt_max_m', 'dur_hrs', 'req_fis', 
        'pre_bas', 'cap_max', 'trz_geo', 'act'
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaTour::class, 'cat_id');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoTour::class, 'tur_id');
    }
}
