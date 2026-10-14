<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,cor',
            ],
            'password' => $this->passwordRules(),
            'password_confirmation' => [
                'required',
                'same:password',
            ],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature()
                ? ['accepted', 'required']
                : [],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password_confirmation.required' =>
                'Debes confirmar tu contraseña.',
            'password_confirmation.same' =>
                'Las contraseñas no coinciden.',
        ])->validate();

        return User::create([
            'nom' => $input['name'],
            'cor' => $input['email'],
            'clv' => $input['password'],
            'rol' => 'turista',
            'ci_cmp' => '',
            'act' => true,
        ]);
    }
}