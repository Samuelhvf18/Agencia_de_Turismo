<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Estado de la cadena de Markov del ciclo de vida del cliente. */
class MarkovEstadoCliente extends Model
{
    protected $table = 'markov_estados_cliente';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = null;

    protected $fillable = ['cod', 'nom', 'des', 'es_abs', 'ord_vis', 'col_hex'];

    protected function casts(): array
    {
        return [
            'es_abs' => 'boolean',
            'ord_vis' => 'integer',
        ];
    }
}
