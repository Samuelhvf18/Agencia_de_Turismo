<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Rol del sistema: turista, guia, chofer, operaciones o admin. */
class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    public $timestamps = false;
    protected $fillable = ['nom', 'des'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}
