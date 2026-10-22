<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitacoraAuditoria extends Model
{
    use HasFactory;

    protected $table = 'bitacora_auditoria';
    public $timestamps = false; // Manejado por columna cre_en de la BD

    protected $fillable = [
        'usr_id',
        'nom_tbl',
        'id_reg',
        'acc',
        'val_ant',
        'val_nvo',
        'dir_ip',
        'age_usr',
        'cre_en',
    ];

    protected $casts = [
        'val_ant' => 'array',
        'val_nvo' => 'array',
        'cre_en'  => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usr_id');
    }
}