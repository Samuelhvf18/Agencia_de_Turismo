<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaTour extends Model
{
    protected $table = 'categorias_tour';
    public $timestamps = false;
    protected $fillable = ['nom', 'rta_amg', 'des', 'ico'];

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'cat_id');
    }
}
