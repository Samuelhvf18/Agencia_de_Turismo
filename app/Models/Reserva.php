<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** Reserva de una salida. usr_id es nulo en las reservas de invitado. */
class Reserva extends Model
{
    public const EST_PENDIENTE = 'pendiente';
    public const EST_CONFIRMADA = 'confirmada';
    public const EST_CANCELADA = 'cancelada';
    public const EST_COMPLETADA = 'completada';

    public const PAG_PENDIENTE = 'pendiente';
    public const PAG_PARCIAL = 'parcial';
    public const PAG_PAGADO = 'pagado';
    public const PAG_REEMBOLSADO = 'reembolsado';

    protected $table = 'reservas';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';

    protected $fillable = [
        'cod', 'sal_tur_id', 'usr_id', 'nom_ctc', 'cor_ctc', 'tel_ctc',
        'cnt_ptc', 'est', 'pre_tot', 'est_pag',
    ];

    protected function casts(): array
    {
        return ['cnt_ptc' => 'integer', 'pre_tot' => 'decimal:2'];
    }

    public function salida(): BelongsTo
    {
        return $this->belongsTo(SalidaTour::class, 'sal_tur_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id');
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(ParticipanteReserva::class, 'rsv_id');
    }

    public function boletos(): HasManyThrough
    {
        return $this->hasManyThrough(Boleto::class, ParticipanteReserva::class, 'rsv_id', 'ptc_rsv_id');
    }

    public function comprobante(): HasOne
    {
        return $this->hasOne(ComprobantePago::class, 'rsv_id');
    }

    public function resena(): HasOne
    {
        return $this->hasOne(Resena::class, 'rsv_id');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(RegistroNotificacion::class, 'rsv_id');
    }
}
