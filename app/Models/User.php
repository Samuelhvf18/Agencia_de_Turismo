<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'rol_id',
        'nom',
        'cor',
        'clv',
        'rol',
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
}