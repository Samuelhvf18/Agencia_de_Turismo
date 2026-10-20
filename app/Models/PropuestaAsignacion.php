<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Propuesta de asignación de IO II (RF-24): se revisa y se aplica a mano. */
class PropuestaAsignacion extends Model
{
    public const EST_PENDIENTE = 'pendiente';
    public const EST_APLICADA = 'aplicada';
    public const EST_AJUSTADA = 'ajustada';
    public const EST_RECHAZADA = 'rechazada';

    protected $table = 'propuestas_asignacion';
    const CREATED_AT = 'gen_en';
    const UPDATED_AT = null;

    protected $fillable = ['lote', 'tip', 'sal_tur_id', 'rec_act_id', 'rec_pro_id', 'cos', 'det', 'est', 'rev_por', 'rev_en'];

    protected function casts(): array
    {
        return ['det' => 'array', 'cos' => 'float', 'rev_en' => 'datetime', 'gen_en' => 'datetime'];
    }

    public function salida(): BelongsTo
    {
        return $this->belongsTo(SalidaTour::class, 'sal_tur_id');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rev_por');
    }

    /** Nombre legible del recurso propuesto. */
    public function recursoPropuesto(): ?string
    {
        return $this->nombreRecurso($this->rec_pro_id);
    }

    public function recursoActual(): ?string
    {
        return $this->nombreRecurso($this->rec_act_id);
    }

    private function nombreRecurso(?int $id): ?string
    {
        if (! $id) {
            return null;
        }

        return $this->tip === 'guia'
            ? PerfilGuia::with('usuario')->find($id)?->usuario?->nom
            : Vehiculo::find($id)?->pla;
    }
}
