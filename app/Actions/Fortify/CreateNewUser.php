<?php
namespace App\Actions\Fortify;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    /**
     * Validar y crear un usuario nuevo.
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'nom' => ['required', 'string', 'max:150'],
            'pat' => ['required', 'string', 'max:100'],
            'mat' => ['nullable', 'string', 'max:100'],
            'usr' => [
                'nullable',
                'string',
                'max:50',
                'alpha_dash',
                'unique:usuarios,usr',
            ],

            'cor' => [
                'required',
                'string',
                'email',
                'max:150',
                'unique:usuarios,cor',
            ],
            'tel' => ['nullable', 'string', 'max:20'],
            'pai_id' => ['nullable', 'exists:paises,id'],
            'tip_doc' => ['nullable', 'string', 'in:CI,Pasaporte'],
            'num_doc' => ['nullable', 'string', 'max:15'],
            'ci_cmp' => ['nullable', 'string', 'max:5'],
            'ci_dep_id' => [
                'nullable',
                'exists:departamentos_bolivia,id',
            ],
            'password' => $this->passwordRules(),

            'password_confirmation' => [
                'required',
                'same:password',
            ],
        ], [
            'nom.required' => 'El nombre es obligatorio.',
            'pat.required' => 'El apellido paterno es obligatorio.',
            'usr.unique' => 'Este nombre de usuario ya está registrado.',
            'usr.alpha_dash' => 'El nombre de usuario solo puede contener letras, números, guiones y guiones bajos.',
            'cor.required' => 'El correo electrónico es obligatorio.',
            'cor.email' => 'Ingresa un correo electrónico válido.',
            'cor.unique' => 'Este correo electrónico ya está registrado.',
            'pai_id.exists' => 'El país seleccionado no es válido.',
            'ci_dep_id.exists' => 'El departamento seleccionado no es válido.',
            'tip_doc.in' => 'El tipo de documento seleccionado no es válido.',
            'password_confirmation.same' => 'Las contraseñas no coinciden.',
        ])->validate();
        // Buscar el rol turista por su nombre.
        $rolTurista = Role::where('nom', 'turista')->first();
        if (!$rolTurista) {
            throw ValidationException::withMessages([
                'cor' => 'No se encontró el rol turista. Contacta al administrador del sistema.',
            ]);
        }
        // Crear el usuario con el rol turista.
        return User::create([
            'rol_id' => $rolTurista->id,
            'nom' => $input['nom'],
            'pat' => $input['pat'],
            'mat' => $input['mat'] ?? null,
            'usr' => $input['usr'] ?? null,
            'cor' => $input['cor'],
            'tel' => $input['tel'] ?? null,
            'pai_id' => $input['pai_id'] ?? null,
            'tip_doc' => $input['tip_doc'] ?? null,
            'num_doc' => $input['num_doc'] ?? null,
            'ci_cmp' => $input['ci_cmp'] ?? '',
            'ci_dep_id' => $input['ci_dep_id'] ?? null,
            'clv' => $input['password'],
        ]);
    }
}