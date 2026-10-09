<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'cre_en';
    const UPDATED_AT = 'mod_en';
    protected $fillable = [
        'rol_id',
        'pai_id',
        'nom',
        'pat',
        'mat',
        'usr',
        'cor',
        'clv',
        'tel',
        'tip_doc',
        'num_doc',
        'ci_cmp',
        'ci_dep_id',
        'fot_prf',
        'tok_rec',
    ];
    protected $hidden = [
        'clv',
        'tok_rec',
    ];
    protected function casts(): array
    {
        return [
            'clv' => 'hashed',
            'cor_vrf_en' => 'datetime',
            'cre_en' => 'datetime',
            'mod_en' => 'datetime',
            'eli_en' => 'datetime',
        ];
    }
    public function getAuthIdentifierName()
    {
        return 'id';
    }
    public function getAuthPasswordName()
    {
        return 'clv';
    }
    public function getAuthPassword()
    {
        return $this->clv;
    }
    public function getEmailForPasswordReset()
    {
        return $this->cor;
    }
    public function routeNotificationForMail($notification = null)
    {
        return $this->cor;
    }
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
    public function getRememberTokenName(): ?string
    {
        return null;
        }
}
