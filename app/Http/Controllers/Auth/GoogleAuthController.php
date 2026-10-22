<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $google = Socialite::driver('google')->user();

            $email = $google->getEmail();
            $googleData = $google->user ?? [];

            $verified = filter_var(
                $googleData['email_verified']
                    ?? $googleData['verified_email']
                    ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

            if (!$email || !$verified) {
                return redirect()->route('login')
                    ->withErrors([
                        'cor' => 'Google debe proporcionar un correo verificado.',
                    ]);
            }

            $email = Str::lower(trim($email));

            $user = User::where('cor', $email)->first();

            if ($user) {
                if (!$user->act) {
                    return redirect()->route('login')
                        ->withErrors([
                            'cor' => 'Esta cuenta está desactivada.',
                        ]);
                }

                if (!$user->cor_vrf_en) {
                    $user->cor_vrf_en = now();
                    $user->save();
                }
            } else {
                $user = DB::transaction(function () use ($google, $email) {
                    $rolTuristaId = DB::table('roles')
                        ->where('nom', 'turista')
                        ->value('id');

                    if (!$rolTuristaId) {
                        throw new \RuntimeException(
                            'No existe el rol turista en la tabla roles.'
                        );
                    }

                    return User::create([
                        'rol_id' => $rolTuristaId,
                        'pai_id' => null,
                        'nom' => Str::limit(
                            trim($google->getName() ?: Str::before($email, '@')),
                            150,
                            ''
                        ),
                        'cor' => $email,
                        'cor_vrf_en' => now(),
                        'clv' => Str::random(64),
                        'rol' => 'turista',
                        'ci_cmp' => '',
                        'act' => true,
                    ]);
                });
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(
                config('fortify.home', '/dashboard')
            );

        } catch (Throwable $e) {
            Log::error('Error de autenticación con Google', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->withErrors([
                    'cor' => 'No se pudo iniciar sesión con Google. Inténtalo nuevamente.',
                ]);
        }
    }
}