<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request)
    {
        abort_unless(config('services.google.client_id') && config('services.google.client_secret') && config('services.google.redirect'), 503, 'Google aún no está configurado.');
        if ($request->user()) {
            $request->session()->put('google.vincular', $request->user()->id);
        } else {
            $request->session()->forget('google.vincular');
        }

        return Socialite::driver('google')->scopes(['openid', 'email:rfc', 'profile'])->redirect();
    }

    public function callback(Request $request)
    {
        try {
            // Socialite valida el estado OAuth.
            $google = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['cor' => 'No se pudo verificar Google. Intenta de nuevo.']);
        }
        $correo = Str::lower((string) $google->getEmail());
        $verificado = filter_var($google->user['verified_email'] ?? $google->user['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (! $verificado || ! filter_var($correo, FILTER_VALIDATE_EMAIL) || ! $google->getId()) {
            return redirect()->route('login')->withErrors(['cor' => 'Google debe proporcionar un correo verificado.']);
        }

        if ($vincular = $request->session()->pull('google.vincular')) {
            abort_unless($request->user()?->id === $vincular && $request->user()->cor === $correo, 403);
            abort_if(User::where('google_id', $google->getId())->whereKeyNot($vincular)->exists(), 409, 'Esta cuenta de Google ya está vinculada.');
            $request->user()->forceFill(['google_id' => $google->getId()])->save();

            return redirect()->route('profile.show')->with('status', 'Cuenta de Google vinculada.');
        }

        $usuario = User::where('google_id', $google->getId())->first();
        if (! $usuario && User::whereRaw('LOWER(cor) = ?', [$correo])->exists()) {
            return redirect()->route('login')->withErrors(['cor' => 'Este correo ya tiene una cuenta. Inicia sesión con tu contraseña y vincula Google desde tu perfil.']);
        }
        if (! $usuario) {
            $rol = Role::where('nom', User::ROL_TURISTA)->firstOrFail();
            $nombre = trim(preg_replace('/[^\p{L}\p{M} ]/u', '', (string) $google->getName()));
            $nombre = preg_replace('/ +/', ' ', $nombre);
            $usuario = User::create(['nom' => $nombre ?: 'Turista', 'cor' => $correo, 'rol_id' => $rol->id, 'clv' => Str::random(64)]);
            $usuario->forceFill(['google_id' => $google->getId(), 'cor_vrf_en' => now()])->save();
        }
        abort_unless($usuario->act && ! $usuario->eli_en, 403, 'La cuenta no está activa.');

        if ($usuario->two_factor_secret && $usuario->two_factor_confirmed_at) {
            $request->session()->put(['login.id' => $usuario->id, 'login.remember' => false]);

            return redirect()->route('two-factor.login');
        }
        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->intended($usuario->rutaInicio());
    }
}
