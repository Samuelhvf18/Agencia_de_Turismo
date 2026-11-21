<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Responses\RedirigirSegunRol;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;
use Laravel\Passkeys\Passkeys;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Inicio según rol.
        $this->app->singleton(LoginResponse::class, RedirigirSegunRol::class);
        $this->app->singleton(RegisterResponse::class, RedirigirSegunRol::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passkeys::authorizeLoginUsing(fn ($request, $user, $passkey) => $user->act && ! $user->eli_en && $user->hasVerifiedEmail());
        RateLimiter::for('passkeys', fn (Request $request) => Limit::perMinute(6)->by($request->ip()));
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Acceso con correo y contraseña.
        // Rechaza cuentas inactivas o eliminadas.
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('cor', Str::lower((string) $request->input(Fortify::username())))
                ->where('act', true)
                ->whereNull('eli_en')
                ->first();

            return $user && Hash::check((string) $request->input('password'), $user->clv) ? $user : null;
        });

        // Confirma contraseña en acciones sensibles.
        Fortify::confirmPasswordsUsing(fn (User $user, ?string $password) => Hash::check((string) $password, $user->clv));

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
