<?php

namespace App\Actions\Fortify;

use App\Models\MarkovEstadoCliente;
use App\Models\Role;
use App\Models\User;
use App\Services\Io\MarkovService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validar y crear un turista nuevo.
     *
     * El formulario pide nombre y apellidos por separado; en `users.nom` se
     * guarda el nombre completo (diccionario de datos v4).
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'nom' => ['required', 'string', 'max:60', "regex:/^[\pL\s'.-]+$/u"],
            'pat' => ['required', 'string', 'max:45', "regex:/^[\pL\s'.-]+$/u"],
            'mat' => ['nullable', 'string', 'max:45', "regex:/^[\pL\s'.-]+$/u"],
            'cor' => ['required', 'string', 'email', 'max:150', 'unique:users,cor'],
            'tel' => ['nullable', 'string', 'max:20'],
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

        // IO II · Markov: todo turista nuevo entra a la cadena como prospecto.
        if (MarkovEstadoCliente::where('cod', 'prospecto')->exists()) {
            app(MarkovService::class)->registrarCambio($usuario->id, 'prospecto', 'Registro de cuenta');
        }

        return $usuario;
    }
}
