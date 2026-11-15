<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** Participante de una reserva (CI con complemento y departamento, o pasaporte). */
class ParticipanteReserva extends Model
{
    protected $table = 'participantes_reserva';

    public $timestamps = false;

    protected $fillable = [
        'fecha_nacimiento', 'acompanante_id', 'autorizacion_menor', 'rsv_id', 'nom_cpl', 'tip_doc', 'num_doc', 'ci_cmp', 'ci_dep_id', 'eda', 'pai_id', 'es_ctc_pri',
    ];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date', 'autorizacion_menor' => 'array', 'eda' => 'integer', 'es_ctc_pri' => 'boolean'];
    }

    public function acompanante(): BelongsTo
    {
        return $this->belongsTo(self::class, 'acompanante_id');
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'rsv_id');
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pai_id');
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(DepartamentoBolivia::class, 'ci_dep_id');
    }

    public function alquileres(): HasMany
    {
        return $this->hasMany(AlquilerEquipoReserva::class, 'ptc_rsv_id');
    }

    public function boleto(): HasOne
    {
        return $this->hasOne(Boleto::class, 'ptc_rsv_id');
    }

    public function fichaMedica(): HasOne
    {
        return $this->hasOne(FichaMedica::class, 'ptc_rsv_id');
    }
}
