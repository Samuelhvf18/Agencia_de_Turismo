<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoTour extends Model
{
    protected $table = 'fotos_tour';
    public $timestamps = false;
    protected $fillable = ['tur_id', 'rta_arc', 'ord_vis', 'txt_atv'];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tur_id');
    }
}