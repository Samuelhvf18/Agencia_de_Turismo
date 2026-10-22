<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    const CREATED_AT = 'pag_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'cmp_pag_id',
        'mtd',
        'mto',
        'ref_trx',
        'est',
    ];

    public function comprobante()
    {
        return $this->belongsTo(ComprobantePago::class, 'cmp_pag_id');
    }
}