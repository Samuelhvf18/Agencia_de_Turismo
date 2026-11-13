<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilGuia extends Model
{
    use HasFactory;

    protected $table = 'perfiles_guia';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'usr_id');
    }
}