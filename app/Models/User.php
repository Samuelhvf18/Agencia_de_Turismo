<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Auditable;


class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes; // Habilita el manejo automático de eli_en

    protected $table = 'users';

    // Mapeo de columnas de timestamps personalizadas
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'eli_en';
    protected $fillable = [
        'rol_id',
        'pai_id',
        'nom',
        'cor',
        'cor_vrf_en',
        'clv',
        'tel',
        'tip_doc',
        'num_doc',
        'ci_cmp',
        'ci_dep_id',
        'fot_prf',
        'act',
    ];

    protected $hidden = [
        'clv',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'cor_vrf_en' => 'datetime',
            'clv' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->clv;
    }

    public function username(): string
    {
        return 'cor';
    }
    public function getEmailForPasswordReset(): string
    {
        return $this->cor;
    }

    // --- Relaciones ---

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function departamento()
    {
        return $this->belongsTo(DepartamentoBolivia::class, 'ci_dep_id');
    } 
}