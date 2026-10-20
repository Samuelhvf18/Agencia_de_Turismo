<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Registro de auditoría (CREATE, UPDATE, DELETE o READ). Solo se inserta. */
class BitacoraAuditoria extends Model
{
    protected $table = 'bitacora_auditoria';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = null;

    protected $fillable = ['usr_id', 'nom_tbl', 'id_reg', 'acc', 'val_ant', 'val_nvo', 'dir_ip', 'age_usr'];

    protected function casts(): array
    {
        return ['val_ant' => 'array', 'val_nvo' => 'array', 'cre_en' => 'datetime'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id');
    }
}
