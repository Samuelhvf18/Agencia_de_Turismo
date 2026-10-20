<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartamentoBolivia extends Model
{
    protected $table = 'departamentos_bolivia';
    public $timestamps = false;

    protected $fillable = ['cod', 'nom'];
}