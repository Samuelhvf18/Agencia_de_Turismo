<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Intento de pago (tarjeta, qr, transferencia o efectivo). */
class Pago extends Model
{
    public const EST_COMPLETADO = 'completado';
    public const EST_FALLIDO = 'fallido';
    public const EST_PENDIENTE = 'pendiente';

    protected $table = 'pagos';
    const CREATED_AT = 'pag_en';
    const UPDATED_AT = null;

    protected $fillable = ['cmp_pag_id', 'mtd', 'mto', 'ref_trx', 'pag_en', 'est'];

    protected function casts(): array
    {
        return ['mto' => 'decimal:2', 'pag_en' => 'datetime'];
    }

    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(ComprobantePago::class, 'cmp_pag_id');
    }
}
