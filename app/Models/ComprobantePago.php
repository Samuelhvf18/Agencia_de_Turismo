<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Comprobante de una reserva: pendiente, pagado o anulado (sin borrado físico). */
class ComprobantePago extends Model
{
    public const EST_PENDIENTE = 'pendiente';
    public const EST_PAGADO = 'pagado';
    public const EST_ANULADO = 'anulado';

    protected $table = 'comprobantes_pago';
    public $timestamps = false;
    protected $fillable = ['cod', 'rsv_id', 'fec_emi', 'sub', 'imp', 'mto_tot', 'est'];

    /** En las URL públicas se usa el código, no el id. */
    public function getRouteKeyName(): string
    {
        return 'cod';
    }

    protected function casts(): array
    {
        return ['fec_emi' => 'date', 'sub' => 'decimal:2', 'imp' => 'decimal:2', 'mto_tot' => 'decimal:2'];
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'rsv_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'cmp_pag_id');
    }

    /** Suma de los pagos completados. */
    public function montoPagado(): float
    {
        return (float) $this->pagos()->where('est', Pago::EST_COMPLETADO)->sum('mto');
    }
}
