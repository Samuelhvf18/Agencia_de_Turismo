<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

/**
 * Usuario del sistema (tabla `users`, diccionario de datos v4).
 *
 * Columnas propias: nom (nombre completo), cor (correo), clv (contraseña),
 * rol_id (roles). Se exponen los alias `name` y `email` para que las vistas
 * de Jetstream y Fortify sigan funcionando.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROL_TURISTA = 'turista';
    public const ROL_GUIA = 'guia';
    public const ROL_CHOFER = 'chofer';
    public const ROL_OPERACIONES = 'operaciones';
    public const ROL_ADMIN = 'admin';

    protected $table = 'users';

    protected $fillable = [
        'rol_id', 'pai_id', 'nom', 'cor', 'clv', 'tel', 'tip_doc', 'num_doc',
        'ci_cmp', 'ci_dep_id', 'fot_prf', 'act',
    ];

    protected $hidden = ['clv', 'remember_token'];

    protected $appends = ['name', 'email', 'profile_photo_url'];

    protected function casts(): array
    {
        return [
            'clv' => 'hashed',
            'cor_vrf_en' => 'datetime',
            'eli_en' => 'datetime',
            'act' => 'boolean',
        ];
    }

    // ---- Autenticación con columnas propias -------------------------------

    public function getAuthPasswordName(): string
    {
        return 'clv';
    }

    public function getAuthPassword(): string
    {
        return $this->clv;
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->cor;
    }

    public function getEmailForVerification(): string
    {
        return $this->cor;
    }

    public function routeNotificationForMail($notification = null): string
    {
        return $this->cor;
    }

    // ---- Alias de compatibilidad (Jetstream / vistas) ------------------------

    protected function name(): Attribute
    {
        return Attribute::get(fn () => $this->nom);
    }

    protected function email(): Attribute
    {
        return Attribute::get(fn () => $this->cor);
    }

    /** Hash de la contraseña; Jetstream lo lee como `password` al confirmar acciones. */
    protected function password(): Attribute
    {
        return Attribute::get(fn () => $this->clv);
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->fot_prf
            ? Storage::disk('public')->url($this->fot_prf)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->nom ?? '').'&color=1F5F8B&background=F4C95D');
    }

    public function deleteProfilePhoto(): void
    {
        if ($this->fot_prf) {
            Storage::disk('public')->delete($this->fot_prf);
            $this->forceFill(['fot_prf' => null])->save();
        }
    }

    // ---- Roles ---------------------------------------------------------------

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function nombreRol(): ?string
    {
        return $this->rol?->nom;
    }

    /** @param  string|array<int, string>  $roles */
    public function tieneRol(string|array $roles): bool
    {
        return in_array($this->nombreRol(), (array) $roles, true);
    }

    public function esAdmin(): bool
    {
        return $this->tieneRol(self::ROL_ADMIN);
    }

    /** Ruta de inicio según el rol (redirección tras iniciar sesión). */
    public function rutaInicio(): string
    {
        return match ($this->nombreRol()) {
            self::ROL_ADMIN, self::ROL_OPERACIONES => route('admin.dashboard'),
            self::ROL_GUIA, self::ROL_CHOFER => route('guias.checkin'),
            default => route('home'),
        };
    }

    // ---- Relaciones de negocio -------------------------------------------------

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pai_id');
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(DepartamentoBolivia::class, 'ci_dep_id');
    }

    public function perfilGuia(): HasOne
    {
        return $this->hasOne(PerfilGuia::class, 'usr_id');
    }

    public function perfilChofer(): HasOne
    {
        return $this->hasOne(PerfilChofer::class, 'usr_id');
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'usr_id');
    }

    public function conversacionesChatbot(): HasMany
    {
        return $this->hasMany(ConversacionChatbot::class, 'usr_id');
    }
}
