<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Cambio de estado de un cliente en la cadena de Markov. */
class MarkovClienteEstado extends Model
{
    protected $table = 'markov_cliente_estado';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = null;

    protected $fillable = ['usr_id', 'est_id', 'est_ant_id', 'fec_cmb', 'obs'];

    protected function casts(): array
    {
        return [
            'fec_cmb' => 'date',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(MarkovEstadoCliente::class, 'est_id');
    }

    public function estadoAnterior(): BelongsTo
    {
        return $this->belongsTo(MarkovEstadoCliente::class, 'est_ant_id');
    }
}
