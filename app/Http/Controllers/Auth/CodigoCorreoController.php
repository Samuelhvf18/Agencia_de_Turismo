<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Notificaciones\CodigoCorreo;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CodigoCorreoController extends Controller
{
    use PasswordValidationRules;

    public function solicitar(Request $request, CodigoCorreo $codigos)
    {
        $datos = $request->validate(['cor' => ['required', 'email:rfc', 'max:150']]);
        $correo = Str::lower(trim($datos['cor']));
        $usuario = User::whereRaw('LOWER(cor) = ?', [$correo])->where('act', true)->whereNull('eli_en')->first();
        if ($usuario) {
            $codigos->enviar($correo, 'recuperar');
        }

        return redirect()->route('codigo.recuperar')->with('status', 'Si existe una cuenta activa con ese correo, recibirás un código. Revisa también spam.')
            ->with('recuperacion.correo', $correo)->withInput(['cor' => $correo]);
    }

    public function restablecer(Request $request, CodigoCorreo $codigos)
    {
        $datos = $request->validate([
            'cor' => ['required', 'email:rfc', 'max:150'], 'codigo' => ['required', 'digits:6'], 'password' => $this->passwordRules(),
        ]);
        $correo = Str::lower(trim($datos['cor']));
        $usuario = User::whereRaw('LOWER(cor) = ?', [$correo])->where('act', true)->whereNull('eli_en')->first();
        if (! $usuario || ! $codigos->comprobar($correo, 'recuperar', $datos['codigo'], function () use ($usuario, $datos) {
            $usuario->forceFill(['clv' => $datos['password'], 'remember_token' => Str::random(60)])->save();
            // Revoca incluso sesiones persistentes.
            DB::table('sessions')->where('user_id', $usuario->id)->delete();
            $usuario->tokens()->delete();
            event(new PasswordReset($usuario));
        })) {
            throw ValidationException::withMessages(['codigo' => 'El código es inválido, venció o agotó sus 5 intentos. Solicita uno nuevo.']);
        }

        return redirect()->route('login')->with('status', 'Contraseña actualizada. Inicia sesión con tu nueva contraseña.');
    }

    public function verificar(Request $request, CodigoCorreo $codigos)
    {
        $request->validate(['codigo' => ['required', 'digits:6']]);
        $usuario = $request->user();
        if ($usuario->hasVerifiedEmail()) {
            return redirect()->intended($usuario->rutaInicio());
        }
        if (! $codigos->comprobar($usuario->cor, 'verificar', $request->string('codigo')->toString(), function () use ($usuario) {
            $usuario->markEmailAsVerified();
            event(new Verified($usuario));
        })) {
            throw ValidationException::withMessages(['codigo' => 'El código es inválido, venció o agotó sus 5 intentos. Solicita uno nuevo.']);
        }

        return redirect()->intended($usuario->rutaInicio());
    }
}
