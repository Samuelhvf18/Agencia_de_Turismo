<?php

namespace App\Actions\Fortify;

use App\Models\MarkovEstadoCliente;
use App\Models\Role;
use App\Models\User;
use App\Services\Io\MarkovService;
use App\Services\Reservas\SeguridadParticipantes;
use App\Support\ReglasEntrada;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Alta de turistas.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input = ReglasEntrada::componerTelefono($input);
        $input['cor'] = mb_strtolower(trim((string) ($input['cor'] ?? '')));
        foreach (['nom', 'pat', 'mat'] as $campo) {
            $input[$campo] = trim(preg_replace('/\s+/', ' ', (string) ($input[$campo] ?? '')));
        }
        Validator::make($input, [
            'nom' => ['required', 'string', 'max:60', SeguridadParticipantes::NOMBRE],
            'pat' => ['required', 'string', 'max:45', SeguridadParticipantes::NOMBRE],
            'mat' => ['nullable', 'string', 'max:45', SeguridadParticipantes::NOMBRE],
            'cor' => ['required', 'string', 'email:rfc', 'max:150', Rule::unique('users', 'cor'), function ($attribute, $value, $fail) {
                if (User::whereRaw('LOWER(cor) = ?', [$value])->exists()) {
                    $fail('Este correo electrónico ya está registrado.');
                }
            }],
            ...ReglasEntrada::telefono(),
            'pai_id' => ['nullable', 'exists:paises,id'],
            'tip_doc' => ['nullable', 'string', 'in:CI,PASAPORTE'],
            'num_doc' => ['nullable', 'string', 'max:15'],
            'ci_cmp' => ['nullable', 'string', 'max:5'],
            'ci_dep_id' => ['nullable', 'required_if:tip_doc,CI', 'exists:departamentos_bolivia,id'],
            'password' => $this->passwordRules(),
        ], [
            'nom.required' => 'El nombre es obligatorio.',
            'nom.regex' => 'El nombre solo puede contener letras.',
            'pat.required' => 'El apellido paterno es obligatorio.',
            'pat.regex' => 'El apellido paterno solo puede contener letras.',
            'mat.regex' => 'El apellido materno solo puede contener letras.',
            'cor.required' => 'El correo electrónico es obligatorio.',
            'cor.email' => 'Ingresa un correo electrónico válido.',
            'cor.unique' => 'Este correo electrónico ya está registrado.',
            'pai_id.exists' => 'El país seleccionado no es válido.',
            'ci_dep_id.exists' => 'El departamento seleccionado no es válido.',
            'ci_dep_id.required_if' => 'Indica el departamento de expedición del CI.',
            'tip_doc.in' => 'El tipo de documento seleccionado no es válido.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ])->validate();

        $rolTurista = Role::where('nom', User::ROL_TURISTA)->first();
        if (! $rolTurista) {
            throw ValidationException::withMessages([
                'cor' => 'No se encontró el rol turista. Contacta al administrador del sistema.',
            ]);
        }

        $nombreCompleto = trim(preg_replace('/\s+/', ' ', implode(' ', [
            $input['nom'], $input['pat'], $input['mat'] ?? '',
        ])));

        $usuario = User::create([
            'rol_id' => $rolTurista->id,
            'nom' => $nombreCompleto,
            'cor' => $input['cor'],
            'tel' => $input['tel'] ?? null,
            'pai_id' => $input['pai_id'] ?? null,
            'tip_doc' => $input['tip_doc'] ?? null,
            'num_doc' => $input['num_doc'] ?? null,
            'ci_cmp' => $input['ci_cmp'] ?? '',
            'ci_dep_id' => ($input['tip_doc'] ?? null) === 'CI' ? ($input['ci_dep_id'] ?? null) : null,
            'clv' => $input['password'],
        ]);

        // Estado inicial: prospecto.
        if (MarkovEstadoCliente::where('cod', 'prospecto')->exists()) {
            app(MarkovService::class)->registrarCambio($usuario->id, 'prospecto', 'Registro de cuenta');
        }

        return $usuario;
    }
}
