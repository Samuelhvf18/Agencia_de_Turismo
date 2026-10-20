<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Boleto con código QR de un participante: emitido, validado o cancelado. */
class Boleto extends Model
{
    public const EST_EMITIDO = 'emitido';
    public const EST_VALIDADO = 'validado';
    public const EST_CANCELADO = 'cancelado';

    protected $table = 'boletos';
    const CREATED_AT = 'emt_en';
    const UPDATED_AT = null;

    protected $fillable = ['cod', 'ptc_rsv_id', 'est', 'emt_en', 'vld_en', 'vld_por'];

    protected function casts(): array
    {
        return ['emt_en' => 'datetime', 'vld_en' => 'datetime'];
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(ParticipanteReserva::class, 'ptc_rsv_id');
    }

    public function validadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vld_por');
    }
}
