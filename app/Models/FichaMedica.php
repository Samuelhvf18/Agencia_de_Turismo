<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Ficha médica firmada digitalmente (rta_fir = ruta de la imagen de la firma). */
class FichaMedica extends Model
{
    protected $table = 'fichas_medicas';
    public $timestamps = false;
    protected $fillable = ['ptc_rsv_id', 'con_med', 'nom_ctc_eme', 'tel_ctc_eme', 'rta_fir', 'fir_en', 'dir_ip'];

    protected function casts(): array
    {
        return ['fir_en' => 'datetime'];
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(ParticipanteReserva::class, 'ptc_rsv_id');
    }
}
