<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Notificación enviada (o pendiente) por whatsapp, email, sms o panel. */
class RegistroNotificacion extends Model
{
    protected $table = 'registros_notificacion';

    public $timestamps = false;

    protected $fillable = ['rsv_id', 'usr_id', 'can', 'tip', 'est', 'cnd', 'env_en', 'clave_evento', 'intentos', 'disponible_en', 'bloqueado_hasta', 'token_entrega'];

    protected function casts(): array
    {
        return ['cnd' => 'array', 'env_en' => 'datetime', 'disponible_en' => 'datetime', 'bloqueado_hasta' => 'datetime', 'intentos' => 'integer'];
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'rsv_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id');
    }
}
