<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        $input['nom'] = trim($input['nom'] ?? '');
        $input['cor'] = mb_strtolower(trim($input['cor'] ?? ''));

        Validator::make($input, [
            'nom' => ['required', 'string', 'max:150'],
            'cor' => [
                'required',
                'string',
                'email',
                'max:150',
                'unique:users,cor',
            ],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature()
                ? ['accepted', 'required']
                : [],
        ], [
            'nom.required' => 'El nombre es obligatorio.',
            'nom.max' => 'El nombre no puede superar los 150 caracteres.',
            'cor.required' => 'El correo electrónico es obligatorio.',
            'cor.email' => 'Ingresa un correo electrónico válido.',
            'cor.unique' => 'Este correo ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'terms.accepted' => 'Debes aceptar los términos y condiciones.',
        ])->validate();

        return DB::transaction(function () use ($input) {
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
                'nom' => $input['nom'],
                'cor' => $input['cor'],
                'clv' => $input['password'],
                'ci_cmp' => '',
                'act' => true,
            ]);
        });
    }
}